<?php
// controllers/Professor_controller.php

// Ativa a exibição de erros (para depuração, pode ser removido em produção)
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

class Professor_controller
{
    /**
     * Exibe o dashboard do professor (formulário de seleção de login).
     * Esta é a ação inicial que mostra o Dashboard_login.php.
     */
    public function showDashboard()
    {
        echo "<h1>Bem-vindo ao Dashboard do Professor</h1>";
        // Certifique-se de que este caminho está correto para a sua view do formulário inicial
        require_once __DIR__ . '/../views/professor/Dashboard_login.php';
    }

    /**
     * Exibe a página principal de serviços do professor.
     * Esta ação renderizará Dashboard_servicos.php.
     */
    public function showServicesPage()
    {
        // Caminho da view para os serviços do professor
        require_once __DIR__ . '/../views/professor/Dashboard_servicos.php';
    }

    /**
     * Exibe a página de resultados dos alunos.
     * Assumindo que esta view foi movida para views/professor/Dashboard_resultados.php
     * ou para um caminho similar. Ajuste conforme a localização real.
     */
    public function showResultsPage()
    {
        echo "<h1>Página de Resultados dos Alunos</h1>";
        // Caminho da view para os resultados dos alunos
        require_once __DIR__ . '/../views/professor/Dashboard_resultados.php'; // ATENÇÃO: Verifique este caminho
    }


    /**
     * Processa a seleção do professor (serviços ou resultados).
     * Este método é acionado via POST.
     */
    public function handleSelection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_calculo = $_POST['tipo_calculo'] ?? '';

            if ($tipo_calculo === 'servicos') {
                // Redireciona para a AÇÃO 'showServicesPage' dentro do MESMO controlador
                header("Location: index.php?controller=professor&action=showServicesPage");
                exit();
            } elseif ($tipo_calculo === 'resultados') {
                // Redireciona para a AÇÃO 'showResultsPage' dentro do MESMO controlador
                header("Location: index.php?controller=professor&action=showResultsPage");
                exit();
            } else {
                // Opção inválida, exibe o dashboard de login com mensagem de erro
                $error = "Selecione uma opção válida.";
                require_once __DIR__ . '/../views/professor/Dashboard_login.php';
            }
        } else {
            // Se não for POST (ex: alguém acessou handleSelection via GET),
            // exibe o dashboard de login, talvez com uma mensagem.
            $error = "Requisição inválida."; // Mensagem mais apropriada para GET em um handler POST
            require_once __DIR__ . '/../views/professor/Dashboard_login.php';
        }
    }
}
?>