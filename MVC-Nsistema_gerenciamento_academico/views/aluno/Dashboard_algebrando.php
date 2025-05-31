<?php
// app/views/aluno/Dashboard_algebrando_estatico.php

// Variáveis que serão passadas pelo controller para a view
// Para evitar "Undefined variable", inicialize-as se não tiver certeza que virão do controller.
$pa_status = $pa_status ?? 0;
$pg_status = $pg_status ?? 0;
$porcentagem_status = $porcentagem_status ?? 0;
$proporcao_status = $proporcao_status ?? 0;
$all_activities_completed = $all_activities_completed ?? false;
$error_message = $error_message ?? ''; // Para exibir mensagens de erro
?>

<!DOCTYPE html>
<html>
<head>
    <title>Atividades/Provas - Algebrando</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/style.css"> </head>
<body class="home">
    <h1> Atividades/Provas - Algebrando </h1><br>

    <?php if (!empty($error_message)): ?>
        <p style="color:red; text-align: center;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php endif; ?>

    <div id="cards-container">
        <div class="card">
            <a href="index.php?controller=aluno&action=viewPA">
                <img src="/img/i_pa.png" alt="Progressão Aritmética">
            </a>
            <?php echo $pa_status == 1 ? '<img class="check" src="/img/checked1.png" alt="Concluído">' : "Não visto"; ?>
        </div>
        <div class="card">
            <a href="index.php?controller=aluno&action=viewPG">
                <img src="/img/i_pg.png" alt="Progressão Geométrica">
            </a>
            <?php echo $pg_status == 1 ? '<img class="check" src="/img/checked1.png" alt="Concluído">' : "Não visto"; ?>
        </div>
        <div class="card">
            <a href="index.php?controller=aluno&action=viewPorcentagem">
                <img src="/img/i_porcentagem.png" alt="Porcentagem">
            </a>
            <?php echo $porcentagem_status == 1 ? '<img class="check" src="/img/checked1.png" alt="Concluído">' : "Não visto"; ?>
        </div>
        <div class="card">
            <a href="index.php?controller=aluno&action=viewProporcao">
                <img src="/img/i_proporcao.png" alt="Proporção">
            </a>
            <?php echo $proporcao_status == 1 ? '<img class="check" src="/img/checked1.png" alt="Concluído">' : "Não visto"; ?>
        </div>
    </div><br><br><br>

    <div class="btn_prova">
        <?php if ($all_activities_completed): ?>
            <form method="post" action="index.php?controller=aluno&action=startEstaticoProva">
                <button type="submit" name="fazer_prova" class="btn_prova">Fazer Prova</button>
            </form>
        <?php else: ?>
            <button class="btn_prova" onclick="mostrarMensagem()">Fazer Prova</button>
            <p id="mensagem-erro" style="color: red; display: none;">Você não fez todas as tarefas!</p>
        <?php endif; ?>
    </div>

    <div class="btn_home">
        <a href="index.php?controller=auth&action=logout" class="btn_home">Logout -> HomePage</a>
    </div>

    <script>
        function mostrarMensagem() {
            document.getElementById('mensagem-erro').style.display = 'block';
        }
    </script>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>