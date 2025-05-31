<?php
// controllers/Auth_controller.php

// Ativa a exibição de erros (para depuração, pode ser removido em produção)
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Inclui o modelo de autenticação, que interage com o banco de dados
require_once __DIR__ . '/../models/Auth_model.php';

class Auth_controller {
    private $authModel;

    public function __construct() {
        $this->authModel = new Auth_model();
    }

    /**
     * Exibe o formulário de login (página inicial do sistema).
     */
    public function showLoginForm() {
        require_once __DIR__ . '/../views/auth/Login.php';
    }

    /**
     * Processa a requisição de login (geralmente via POST).
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $senhaDigitada = $_POST['senha'] ?? '';

            if (empty($login) || empty($senhaDigitada)) {
                // Usa a função global displayErrorPage do index.php
                displayErrorPage("Por favor, preencha todos os campos de login e senha.", 'index.php?controller=auth&action=showLoginForm');
            }

            $user = $this->authModel->authenticate($login, $senhaDigitada);

            if ($user) {
                // A sessão já deve ter sido iniciada no index.php
                $_SESSION['logado'] = true;
                $_SESSION['tipo_usuario'] = $user['type'];
                $_SESSION['id_usuario'] = $user['data']['id_' . $user['type']];
                $_SESSION['nome_usuario'] = $user['data']['nome'];
                $_SESSION['email_usuario'] = $user['data']['email'];

                // Redireciona para o dashboard correto com base no tipo de usuário
                if ($user['type'] === 'aluno') {
                    $_SESSION['nome_turma'] = $user['data']['nomeTurma'] ?? 'N/A';
                    redirect('index.php?controller=dashboard&action=showAlunoDashboard');
                } else { // Professor
                    
                    redirect('index.php?controller=dashboard&action=showProfessorDashboard');
                }
            } else {
                displayErrorPage("Login ou senha inválidos. Por favor, tente novamente.", 'index.php?controller=auth&action=showLoginForm');
            }
        } else {
            // Se a requisição não for POST (tentativa de acessar diretamente), redireciona para o formulário de login
            redirect('index.php?controller=auth&action=showLoginForm');
        }
    }

    /**
     * Realiza o logout do usuário, limpando a sessão.
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();   // Remove todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        displayErrorPage("Você foi desconectado com sucesso!", 'index.php?controller=auth&action=showLoginForm');
    }

    /**
     * Exibe o formulário de cadastro de professor.
     */
    public function showProfessorRegisterForm() {
        $isUpdating = false; // Usado na view para diferenciar cadastro de edição
        $professorData = []; // Inicializa para evitar erros se a view esperar dados
        $errors = "";        // Inicializa para evitar erros se a view esperar erros
        require_once __DIR__ . '/../views/auth/register_professor.php';
    }

    /**
     * Processa a requisição de cadastro de professor (geralmente via POST).
     */
    public function registerProfessor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Supondo que Auth_model tenha um método validateProfessorData
            $errors = $this->authModel->validateProfessorData($_POST);

            if (!empty($errors)) {
                $isUpdating = false;
                $professorData = $_POST; // Preserva os dados digitados para reexibir
                
                require_once __DIR__ . '/../views/auth/register_professor.php';
                return; // Para a execução para mostrar o formulário com erros
            }

            if ($this->authModel->registerProfessor($_POST)) {
                echo "<p>Professor cadastrado com sucesso!</p>";
                echo '<button onclick="window.location.href=\'index.php?controller=auth&action=showLoginForm\'">Voltar para o Login</button>';
                exit(); // Para a execução para mostrar a mensagem de sucesso
            } else {
                displayErrorPage("Erro ao cadastrar professor. Por favor, tente novamente.", 'index.php?controller=auth&action=showLoginForm');
            }
        } else {
            // Se a requisição não for POST, redireciona para o formulário de cadastro
            redirect('index.php?controller=auth&action=showProfessorRegisterForm');
        }
    }

    /**
     * Exibe o formulário de cadastro de aluno.
     */
    public function showAlunoRegisterForm() {
        // Você pode precisar inicializar variáveis para a view, como $errors, $alunoData, etc.
        require_once __DIR__ . '/../views/auth/register_aluno.php';
    }

    /**
     * Processa a requisição de cadastro de aluno (a ser implementado no Auth_model).
     */
    public function registerAluno() {
        // Este método deve ser implementado no Auth_model, similar ao registerProfessor
        displayErrorPage("Funcionalidade de cadastro de aluno ainda não implementada.", 'index.php?controller=auth&action=showLoginForm');
    }
}
?>