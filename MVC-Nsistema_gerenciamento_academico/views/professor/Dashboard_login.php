<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tipo_calculo'])) {
        $tipo_calculo = $_POST['tipo_calculo'];

        if ($tipo_calculo === 'servicos') {
            header("Location: ../servicos-professor/pagina-servicos-professor.php");
            exit();
        } elseif ($tipo_calculo === 'resultados') {
            header("Location: ../servicos-professor/pagina-resultados-alunos-algebrando-estatico.php");
            exit();
        } else {
            echo "<p style='color:red;'>Selecione uma opção válida.</p>";
        }
    } else {
        echo "<p style='color:red;'>Por favor, selecione uma opção no seletor.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Professor</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../../public/css/style.css">
</head>

<body class="servicos_forms">
    <div class="form_container">
        <form class="form" method="post" action="index.php?controller=professor&action=handleSelection">
            <h2>Login Professor</h2>
            <select id="tipo_calculo" name="tipo_calculo" required>
                <option value="">Selecione:</option>
                <option value="servicos">Acessar serviços</option>
                <option value="resultados">Resultados prova matemática modelo</option>
            </select><br><br>

            <button type="submit">Login</button>
        </form>
    </div>
    <a href="?logout=true">Logout -> HomePage</a>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>
<?php
/*
Sugestão: Para seguir a arquitetura MVC, o ideal é que o processamento do formulário (lógica de redirecionamento) seja feito em um Controller, não diretamente na View.

Você pode criar um controller, por exemplo ProfessorController.php, com um método para tratar o login/seleção. No formulário, altere o action para apontar para esse controller, e lá faça o redirecionamento conforme a opção escolhida.

Exemplo de estrutura:
- controllers/ProfessorController.php
- views/professor/Dashboard_login.php

No controller:
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // lógica de redirecionamento
}

Na view:
<form method="post" action="../../controllers/ProfessorController.php">

Assim, a View só exibe o formulário, e o Controller trata a lógica, mantendo o padrão MVC.
*/