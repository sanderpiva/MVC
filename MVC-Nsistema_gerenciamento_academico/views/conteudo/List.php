<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lista de Conteúdos</title>
    <link rel="stylesheet" href="/public/css/style.css"> <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .message-success {
            color: green;
            font-weight: bold;
        }
        .message-error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body class="servicos_forms">
    <div class="form_container">
        <h2>Lista de Conteúdos</h2>
        <hr>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message-success"><?php echo htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?></p>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <p class="message-error"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Descrição</th>
                    <th>Data Postagem</th>
                    <th>Professor</th>
                    <th>Disciplina</th>
                    <th>Tipo</th>
                    <th>ID Disciplina</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($conteudos)): ?>
                    <?php foreach ($conteudos as $conteudo): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($conteudo['id_conteudo']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['codigoConteudo']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['titulo']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['data_postagem']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['professor']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['disciplina']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['tipo_conteudo']); ?></td>
                            <td><?php echo htmlspecialchars($conteudo['Disciplina_id_disciplina']); ?></td>
                            <td>
                                <a href="/conteudo?action=edit&id=<?php echo htmlspecialchars($conteudo['id_conteudo']); ?>">Editar</a> |
                                <a href="/conteudo?action=delete&id=<?php echo htmlspecialchars($conteudo['id_conteudo']); ?>" onclick="return confirm('Tem certeza que deseja excluir este conteúdo?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">Nenhum conteúdo encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <hr>
        <a href="/conteudo?action=create">Adicionar Novo Conteúdo</a>
    </div>
    <a href="/dashboard">Voltar ao Dashboard</a> <hr>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>