<?php

class Aluno_controller
{
    public function showDashboard()
    {
        echo "<h1>Bem-vindo ao Dashboard do Aluno</h1>";
        require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
    }

    public function showDynamicServicesPage()
    {
        require_once __DIR__ . '/../views/aluno/Dashboard_dinamico.php';
    }

    public function showStaticServicesPage()
    {
        echo "<h1>Página de Resultados dos Alunos</h1>";
        require_once __DIR__ . '/../views/aluno/Dashboard_algebrando.php'; // ATENÇÃO: Verifique este caminho
    }


    public function handleSelection()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo_atividade = $_POST['tipo_atividade'] ?? '';

            if ($tipo_atividade === 'dinamica') {
                
                header("Location: index.php?controller=aluno&action=showDynamicServicesPage");
                exit();
            } elseif ($tipo_atividade === 'estatica') {
                // Redireciona para a AÇÃO 'showResultsPage' dentro do MESMO controlador
                header("Location: index.php?controller=aluno&action=showStaticServicesPage");
                exit();
            } else {
                // Opção inválida, exibe o dashboard de login com mensagem de erro
                $error = "Selecione uma opção válida.";
                require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
            }
        } else {
            // Se não for POST (ex: alguém acessou handleSelection via GET),
            // exibe o dashboard de login, talvez com uma mensagem.
            $error = "Requisição inválida."; // Mensagem mais apropriada para GET em um handler POST
            require_once __DIR__ . '/../views/aluno/Dashboard_login.php';
        }
    }
}
?>