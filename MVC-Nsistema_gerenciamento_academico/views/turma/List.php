<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Turmas</title>
    <link rel="stylesheet" href="../../../css/style.css">
</head>
<body class="servicos_forms">
<h2>Lista de Turmas</h2>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($turmas as $turma): ?>
            <tr>
                <td><?= htmlspecialchars($turma['codigoTurma']) ?></td>
                <td><?= htmlspecialchars($turma['nomeTurma']) ?></td>
                <td>
                    <a href="index.php?controller=turma&action=showEditForm&id=<?= $turma['id_turma'] ?>">Editar</a>
                    
                    <a href="index.php?controller=turma&action=deleteTurma&id=<?= $turma['id_turma'] ?>" onclick="return confirm('Tem certeza ? id = ' + '<?= $turma['id_turma'] ?>');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<br>
<a href="index.php?controller=professor&action=showServicesPage">Servicos</a>
</body>
</html>
