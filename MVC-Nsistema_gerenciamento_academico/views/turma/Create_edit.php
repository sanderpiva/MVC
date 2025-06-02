<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?= isset($turma) ? 'Atualizar' : 'Cadastrar' ?> Turma</title>
    <link rel="stylesheet" href="../../../css/style.css">
</head>
<body class="servicos_forms">
<div class="form_container">
    <form class="form" action="index.php?controller=turma" method="post">
        <h2>Formulário: <?= isset($turma) ? 'Atualizar' : 'Cadastrar' ?> Turma</h2>
        <hr>

        <label for="codigoTurma">Código Turma:</label>
        <input type="text" name="codigoTurma" id="codigoTurma" value="<?= htmlspecialchars($turma['codigoTurma'] ?? '') ?>" required>

        <label for="nomeTurma">Nome da Turma:</label>
        <input type="text" name="nomeTurma" id="nomeTurma" value="<?= htmlspecialchars($turma['nomeTurma'] ?? '') ?>" required>

        <?php if (isset($turma)): ?>
            <input type="hidden" name="id_turma" value="<?= htmlspecialchars($turma['id_turma']) ?>">
            <input type="hidden" name="action" value="update">
        <?php else: ?>
            <input type="hidden" name="action" value="create">
        <?php endif; ?>

        <button type="submit"><?= isset($turma) ? 'Atualizar' : 'Cadastrar' ?></button>
    </form>
    <hr>
    
    <?php if (isset($turma)): ?>
        <a href="index.php?controller=turma&action=list">Voltar à lista</a>
    <?php else: ?>
        <a href="index.php?controller=professor&action=showServicesPage">Serviços</a>
    <?php endif; ?>
</div>
</body>
</html>
