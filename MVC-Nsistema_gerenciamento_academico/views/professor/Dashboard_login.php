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
