<?php
// controllers/AuthController.php

// Inclui o modelo que contém a lógica de autenticação e validação
require_once __DIR__ . '/../models/auth-model.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    /**
     * Exibe o formulário de login (página inicial).
     */
    public function showLoginForm() {
        // Apenas carrega a view. Nenhuma lógica de negócio aqui.
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Processa a requisição de login.
     * Recebe dados POST, chama o modelo para autenticar e redireciona.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $senhaDigitada = $_POST['senha'] ?? '';

            // Validação simples dos campos
            if (empty($login) || empty($senhaDigitada)) {
                $this->displayErrorPage("Por favor, preencha todos os campos de login e senha.", '/');
            }

            // Chama o modelo para autenticar o usuário
            $user = $this->authModel->authenticate($login, $senhaDigitada);

            if ($user) {
                // Inicia a sessão (já pode estar iniciada pelo index.php, mas é bom garantir)
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                // Armazena os dados do usuário na sessão
                $_SESSION['logado'] = true;
                $_SESSION['tipo_usuario'] = $user['type'];
                $_SESSION['id_usuario'] = $user['data']['id_' . $user['type']];
                $_SESSION['nome_usuario'] = $user['data']['nome'];
                $_SESSION['email_usuario'] = $user['data']['email'];

                // Redireciona com base no tipo de usuário
                if ($user['type'] === 'aluno') {
                    $_SESSION['nome_turma'] = $user['data']['nomeTurma'] ?? 'N/A';
                    header("Location: /aluno-selecao-atividade");
                } else { // Professor
                    header("Location: /professor-dashboard");
                }
                exit(); // Interrompe a execução após o redirecionamento
            } else {
                // Se a autenticação falhar
                $this->displayErrorPage("Login ou senha inválidos. Por favor, tente novamente.", '/');
            }
        } else {
            // Se for um GET request direto para /login (sem POST), redireciona para a home
            header("Location: /");
            exit();
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();   // Remove todas as variáveis de sessão
        session_destroy(); // Destrói a sessão
        $this->displayErrorPage("Você foi desconectado com sucesso!", '/');
    }

    /**
     * Exibe o formulário de cadastro de professor.
     */
    public function showProfessorRegisterForm() {
        // Variáveis necessárias para a view (para evitar erros se não houver dados POST)
        $isUpdating = false;
        $professorData = []; // Para preencher o formulário vazio
        $errors = "";        // Para exibir mensagens de erro
        require_once __DIR__ . '/../views/auth/register_professor.php';
    }

    /**
     * Processa a requisição de cadastro de professor.
     * Recebe dados POST, valida e chama o modelo para registrar.
     */
    public function registerProfessor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Valida os dados usando o método do modelo
            $errors = $this->authModel->validateProfessorData($_POST);

            if (!empty($errors)) {
                // Se houver erros, reexibe o formulário com as mensagens de erro e os dados preenchidos
                $isUpdating = false;
                $professorData = $_POST; // Preserva os dados inválidos para reexibir no formulário
                require_once __DIR__ . '/../views/auth/register_professor.php';
                return; // Interrompe a execução
            }

            // Se a validação passar, tenta registrar o professor
            if ($this->authModel->registerProfessor($_POST)) {
                // Sucesso no cadastro: exibe mensagem e botão para voltar
                echo "<p>Professor cadastrado com sucesso!</p>";
                echo '<button onclick="window.location.href=\'/\'">Voltar para o Menu</button>';
            } else {
                // Erro no banco de dados durante o cadastro
                $this->displayErrorPage("Erro ao cadastrar professor. Por favor, tente novamente.", '/');
            }
        } else {
            // Se for um GET request direto para /cadastro-professor (sem POST), redireciona para a página do formulário GET
            header("Location: /cadastro-professor");
            exit();
        }
    }

    /**
     * Exibe o formulário de cadastro de aluno (a ser implementado).
     */
    public function showAlunoRegisterForm() {
        // Você precisaria de um AlunoModel e de um formulário específico para alunos.
        require_once __DIR__ . '/../views/auth/register_aluno.php';
    }

    /**
     * Processa a requisição de cadastro de aluno (a ser implementado).
     */
    public function registerAluno() {
        // Lógica de cadastro de aluno aqui, similar ao registerProfessor, usando um AlunoModel
        $this->displayErrorPage("Funcionalidade de cadastro de aluno ainda não implementada.", '/');
    }

    /**
     * Método auxiliar para exibir uma página de erro genérica.
     * @param string $message A mensagem de erro a ser exibida.
     * @param string $homeUrl A URL para a página inicial (botão de retorno).
     */
    private function displayErrorPage($message, $homeUrl) {
        $errorMessage = $message; // A view 'error.php' espera esta variável
        $homeUrl = $homeUrl;     // A view 'error.php' espera esta variável
        require_once __DIR__ . '/../views/auth/error.php';
        exit();
    }
}