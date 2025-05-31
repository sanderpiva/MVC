<?php
// controllers/Dashboard_controller.php

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Não precisa de um modelo por enquanto neste exemplo, pois só gerencia redirecionamentos de views
// Mas se houvesse dados a exibir no dashboard, o modelo seria incluído aqui.
// require_once __DIR__ . '/../models/Dashboard_model.php';

class Dashboard_controller {

    public function __construct() {
        // Garante que o usuário esteja logado antes de acessar qualquer método neste controller
        // Usa a função global requireAuth() do index.php
        requireAuth();
    }

    /**
     * Exibe o dashboard principal para professores.
     */
    public function showProfessorDashboard() {
        // Garante que apenas professores logados acessem este dashboard
        // Usa a função global requireAuth() com o tipo de usuário esperado
        echo "<p>Autenticando professor:</p>";
                
        
        requireAuth('professor');
        // Carrega a view do dashboard do professor
        require_once __DIR__ . '/../views/professor/Dashboard_login.php';
    }

    /**
     * Exibe a página de seleção de atividades para alunos.
     */
    public function showAlunoSelection() {
        // Garante que apenas alunos logados acessem esta página
        requireAuth('aluno');
        // Carrega a view de seleção de atividades do aluno
        require_once __DIR__ . '/../views/aluno/selection.php';
    }

    /**
     * Lida com a seleção de tipo de atividade pelo aluno (geralmente via POST).
     * Redireciona para o dashboard apropriado (dinâmico ou estático).
     */
    public function handleAlunoActivitySelection() {
        // Segurança: garanta que apenas alunos logados acessem esta ação
        requireAuth('aluno');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_atividade = $_POST['tipo_atividade'] ?? '';

            if ($tipo_atividade === 'dinamica') {
                // Redireciona para o dashboard de atividades dinâmicas via roteador principal
                redirect('index.php?controller=dashboard&action=showAlunoDynamicDashboard');
            } elseif ($tipo_atividade === 'estatica') {
                // Redireciona para o dashboard de atividades estáticas via roteador principal
                redirect('index.php?controller=dashboard&action=showAlunoAlgebrandoDashboard');
            } else {
                // Se a opção for inválida, exibe uma mensagem de erro e reexibe a página de seleção
                $errorMessage = "Selecione uma opção válida."; // Variável para ser usada na view selection.php
                require_once __DIR__ . '/../views/aluno/selection.php';
            }
        } else {
            // Se for um GET request direto para esta rota POST, redireciona para a página de seleção GET
            redirect('index.php?controller=dashboard&action=showAlunoSelection');
        }
    }

    /**
     * Exibe o dashboard de atividades dinâmicas para alunos (a ser implementado).
     */
    public function showAlunoDynamicDashboard() {
        requireAuth('aluno');
        // Exemplo simples de conteúdo. Em um projeto real, você carregaria uma view PHP aqui.
        echo "<h1>Dashboard de Atividades Dinâmicas do Aluno</h1>";
        echo "<p>Bem-vindo, " . htmlspecialchars($_SESSION['nome_usuario']) . "!</p>";
        echo '<p>Aqui você verá suas atividades dinâmicas.</p>';
        echo '<a href="index.php?controller=dashboard&action=showAlunoSelection">Voltar para a seleção de atividades</a>';
        // Ex: require_once __DIR__ . '/../views/aluno/dashboard_dinamica.php';
    }

    /**
     * Exibe o dashboard de atividades Algebrando para alunos (a ser implementado).
     */
    public function showAlunoAlgebrandoDashboard() {
        requireAuth('aluno');
        // Exemplo simples de conteúdo. Em um projeto real, você carregaria uma view PHP aqui.
        echo "<h1>Dashboard de Atividades Algebrando do Aluno</h1>";
        echo "<p>Bem-vindo, " . htmlspecialchars($_SESSION['nome_usuario']) . "!</p>";
        echo '<p>Aqui você verá suas atividades Algebrando.</p>';
        echo '<a href="index.php?controller=dashboard&action=showAlunoSelection">Voltar para a seleção de atividades</a>';
        // Ex: require_once __DIR__ . '/../views/aluno/dashboard_algebrando.php';
    }
}
?>