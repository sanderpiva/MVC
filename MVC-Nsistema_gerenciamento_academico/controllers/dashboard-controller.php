<?php
// controllers/DashboardController.php

// Não precisa de um modelo por enquanto, pois só gerencia redirecionamentos de views
// (mas poderia interagir com modelos se houvesse dados a exibir no dashboard).

class DashboardController {

    public function __construct() {
        // Garante que a sessão esteja iniciada para verificar autenticação
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // Se o usuário não estiver logado, redireciona para a página inicial
        if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
            $this->redirect('/');
        }
    }

    /**
     * Exibe o dashboard principal para professores.
     */
    public function showProfessorDashboard() {
        // Garante que apenas professores logados acessem este dashboard
        if ($_SESSION['tipo_usuario'] !== 'professor') {
            $this->displayErrorPage("Acesso negado. Apenas professores podem acessar esta página.", '/');
        }
        require_once __DIR__ . '/../views/professor/dashboard.php';
    }

    /**
     * Exibe a página de seleção de atividades para alunos.
     */
    public function showAlunoSelection() {
        // Garante que apenas alunos logados acessem esta página
        if ($_SESSION['tipo_usuario'] !== 'aluno') {
            $this->displayErrorPage("Acesso negado. Apenas alunos podem acessar esta página.", '/');
        }
        require_once __DIR__ . '/../views/aluno/selection.php';
    }

    /**
     * Lida com a seleção de tipo de atividade pelo aluno.
     * Recebe dados POST e redireciona para o dashboard apropriado.
     */
    public function handleAlunoActivitySelection() {
        // Segurança: garanta que apenas alunos logados acessem
        if ($_SESSION['tipo_usuario'] !== 'aluno') {
            $this->displayErrorPage("Acesso negado.", '/');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_atividade = $_POST['tipo_atividade'] ?? '';

            if ($tipo_atividade === 'dinamica') {
                header("Location: /aluno-dashboard-dinamica");
            } elseif ($tipo_atividade === 'estatica') {
                header("Location: /aluno-dashboard-algebrando");
            } else {
                // Se a opção for inválida, reexibe a página de seleção com uma mensagem de erro
                $errorMessage = "Selecione uma opção válida."; // Variável para a view
                require_once __DIR__ . '/../views/aluno/selection.php';
            }
            exit(); // Interrompe a execução após redirecionamento ou renderização da view
        } else {
            // Se for um GET request direto para esta rota POST, redireciona para a página de seleção GET
            header("Location: /aluno-selecao-atividade");
            exit();
        }
    }

    /**
     * Exibe o dashboard de atividades dinâmicas para alunos (a ser implementado).
     */
    public function showAlunoDynamicDashboard() {
        if ($_SESSION['tipo_usuario'] !== 'aluno') {
            $this->displayErrorPage("Acesso negado.", '/');
        }
        // Exemplo:
        echo "<h1>Dashboard de Atividades Dinâmicas do Aluno</h1><p>Bem-vindo, " . htmlspecialchars($_SESSION['nome_usuario']) . "!</p><a href='/aluno-selecao-atividade'>Voltar</a>";
        // require_once __DIR__ . '/../views/aluno/dashboard_dinamica.php';
    }

    /**
     * Exibe o dashboard de atividades Algebrando para alunos (a ser implementado).
     */
    public function showAlunoAlgebrandoDashboard() {
        if ($_SESSION['tipo_usuario'] !== 'aluno') {
            $this->displayErrorPage("Acesso negado.", '/');
        }
        // Exemplo:
        echo "<h1>Dashboard de Atividades Algebrando do Aluno</h1><p>Bem-vindo, " . htmlspecialchars($_SESSION['nome_usuario']) . "!</p><a href='/aluno-selecao-atividade'>Voltar</a>";
        // require_once __DIR__ . '/../views/aluno/dashboard_algebrando.php';
    }


    /**
     * Método auxiliar para redirecionamento.
     * @param string $url A URL para redirecionar.
     */
    private function redirect($url) {
        header("Location: " . $url);
        exit();
    }

    /**
     * Método auxiliar para exibir uma página de erro genérica.
     * @param string $message A mensagem de erro a ser exibida.
     * @param string $homeUrl A URL para a página inicial (botão de retorno).
     */
    private function displayErrorPage($message, $homeUrl) {
        // Define as variáveis que a view 'error.php' espera
        $errorMessage = $message;
        $homeUrl = $homeUrl;
        require_once __DIR__ . '/../views/auth/error.php';
        exit();
    }
}