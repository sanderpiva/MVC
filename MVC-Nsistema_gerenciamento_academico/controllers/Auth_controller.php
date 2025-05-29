<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// controllers/Auth_controller.php

require_once __DIR__ . '/../models/Auth_model.php';

class Auth_controller {
    private $authModel;

    public function __construct() {
        $this->authModel = new Auth_model();
    }

    /**
     * Exibe o formulário de login (página inicial).
     */
    public function showLoginForm() {
        // Agora carrega a view de login
        require_once __DIR__ . '/../views/auth/Login.php';
    }

    /**
     * Processa a requisição de login.
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = $_POST['login'] ?? '';
            $senhaDigitada = $_POST['senha'] ?? '';

            if (empty($login) || empty($senhaDigitada)) {
                // Usa a função global displayErrorPage do index.php
                displayErrorPage("Por favor, preencha todos os campos de login e senha.", '/');
            }

            $user = $this->authModel->authenticate($login, $senhaDigitada);

            if ($user) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['logado'] = true;
                $_SESSION['tipo_usuario'] = $user['type'];
                $_SESSION['id_usuario'] = $user['data']['id_' . $user['type']];
                $_SESSION['nome_usuario'] = $user['data']['nome'];
                $_SESSION['email_usuario'] = $user['data']['email'];

                if ($user['type'] === 'aluno') {
                    $_SESSION['nome_turma'] = $user['data']['nomeTurma'] ?? 'N/A';
                    redirect('/aluno-selecao-atividade'); // Redireciona via rota MVC
                } else { // Professor
                    redirect('/professor-dashboard'); // Redireciona via rota MVC
                }
            } else {
                displayErrorPage("Login ou senha inválidos. Por favor, tente novamente.", '/');
            }
        } else {
            redirect('/'); // Redireciona para a home (showLoginForm)
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        displayErrorPage("Você foi desconectado com sucesso!", '/');
    }

    /**
     * Exibe o formulário de cadastro de professor.
     */
    public function showProfessorRegisterForm() {
        $isUpdating = false;
        $professorData = [];
        $errors = "";
        require_once __DIR__ . '/../views/auth/register_professor.php';
    }

    /**
     * Processa a requisição de cadastro de professor.
     */
    public function registerProfessor() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $this->authModel->validateProfessorData($_POST);

            if (!empty($errors)) {
                $isUpdating = false;
                $professorData = $_POST;
                require_once __DIR__ . '/../views/auth/register_professor.php';
                return;
            }

            if ($this->authModel->registerProfessor($_POST)) {
                echo "<p>Professor cadastrado com sucesso!</p>";
                echo '<button onclick="window.location.href=\'/\'">Voltar para o Menu</button>';
            } else {
                displayErrorPage("Erro ao cadastrar professor. Por favor, tente novamente.", '/');
            }
        } else {
            redirect('/cadastro-professor');
        }
    }

    /**
     * Exibe o formulário de cadastro de aluno.
     */
    public function showAlunoRegisterForm() {
        require_once __DIR__ . '/../views/auth/register_aluno.php';
    }

    /**
     * Processa a requisição de cadastro de aluno.
     */
    public function registerAluno() {
        // Lógica de cadastro de aluno aqui, similar ao registerProfessor, usando um AlunoModel
        // Você precisará criar um AlunoModel e implementar a lógica de validação e registro
        displayErrorPage("Funcionalidade de cadastro de aluno ainda não implementada.", '/');
    }

    /**
     * Método auxiliar para exibir uma página de erro genérica.
     * Importante: Estas funções displayErrorPage e redirect são globais no index.php.
     * Para usá-las dentro de um método de classe, você pode:
     * 1. Passá-las como argumentos para o construtor do Controller (injeção de dependência).
     * 2. Definir as funções como métodos estáticos ou em um helper e chamá-las com `self::` ou `Helper::`.
     * 3. Chamá-las diretamente (como feito aqui, mas cuidado com o escopo global).
     * Para este exemplo, vou mantê-las globais conforme seu `index.php` anterior,
     * mas é uma boa prática considerá-las como helpers ou injetá-las.
     */
    private function displayErrorPage($message, $homeUrl) {
        global $errorMessage, $homeUrl; // Declara como global para serem acessíveis pela view
        $errorMessage = $message;
        $homeUrl = $homeUrl;
        require_once __DIR__ . '/../views/auth/error.php';
        exit();
    }
}