<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Web - <?php echo $isUpdating ? 'Atualizar' : 'Cadastro'; ?> Conteúdo</title>
    <link rel="stylesheet" href="/public/css/style.css"> </head>
<body class="servicos_forms">

    <div class="form_container">
        <form class="form" action="/conteudo" method="post"> <h2>Formulário: <?php echo $isUpdating ? 'Atualizar' : 'Cadastro'; ?> Conteúdo</h2>
            <hr>

            <?php if (!empty($errors)): ?>
                <div style='color: red;'>
                    <?php foreach ($errors as $error): ?>
                        <p><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
                <hr>
            <?php endif; ?>

            <label for="codigoConteudo">Código do conteúdo:</label>
            <?php if ($isUpdating): ?>
                <input type="text" name="codigoConteudo" id="codigoConteudo" placeholder="Digite o código" value="<?php echo htmlspecialchars($conteudoData['codigoConteudo'] ?? ''); ?>" required>
                <input type="hidden" name="id_conteudo" value="<?php echo htmlspecialchars($conteudoData['id_conteudo'] ?? ''); ?>">
            <?php else: ?>
                <input type="text" name="codigoConteudo" id="codigoConteudo" placeholder="Digite o código" value="<?php echo htmlspecialchars($conteudoData['codigoConteudo'] ?? ''); ?>" required>
            <?php endif; ?>
            <hr>

            <label for="tituloConteudo">Título:</label>
            <input type="text" name="tituloConteudo" id="tituloConteudo" placeholder="Digite o título" value="<?php echo htmlspecialchars($conteudoData['titulo'] ?? ''); ?>" required>
            <hr>

            <label for="descricaoConteudo">Descrição do conteúdo:</label>
            <input type="text" name="descricaoConteudo" id="descricaoConteudo" placeholder="Digite a descrição" value="<?php echo htmlspecialchars($conteudoData['descricao'] ?? ''); ?>" required>
            <hr>

            <label for="data_postagem">Data de postagem:</label>
            <input type="date" name="data_postagem" id="data_postagem" value="<?php echo htmlspecialchars($conteudoData['data_postagem'] ?? ''); ?>" required>
            <hr>

            <label for="professor">Professor:</label>
            <input type="text" name="professor" id="professor" placeholder="Digite o autor" value="<?php echo htmlspecialchars($conteudoData['professor'] ?? ''); ?>" required>
            <hr>

            <label for="disciplina">Disciplina:</label>
            <input type="text" name="disciplina" id="disciplina" placeholder="Digite a disciplina" value="<?php echo htmlspecialchars($conteudoData['disciplina'] ?? ''); ?>" required>
            <hr>

            <label for="tipo_conteudo">Tipo de conteúdo:</label>
            <input type="text" name="tipo_conteudo" id="tipo_conteudo" placeholder="Digite o tipo" value="<?php echo htmlspecialchars($conteudoData['tipo_conteudo'] ?? ''); ?>" required>
            <hr>

            <label for="id_disciplina">Código da disciplina:</label>
            <?php if ($isUpdating): ?>
                <input type="text" value="<?php echo htmlspecialchars($conteudoData['nomeDisciplina'] ?? ''); ?>" readonly required>
                <input type="hidden" name="id_disciplina" value="<?php echo htmlspecialchars($conteudoData['Disciplina_id_disciplina'] ?? ''); ?>">
            <?php else: ?>
                <select name="id_disciplina" required>
                    <option value="">Selecione código da disciplina (Professor)</option>
                    <?php foreach ($disciplinas as $disciplina): ?>
                        <?php
                            $professorId = $disciplina['Professor_id_professor'] ?? null;
                            $professorNome = $professorsLookup[$professorId] ?? 'Professor Desconhecido';
                            $selected = (isset($conteudoData['Disciplina_id_disciplina']) && $conteudoData['Disciplina_id_disciplina'] == $disciplina['id_disciplina']) ? 'selected' : '';
                        ?>
                        <option value="<?= htmlspecialchars($disciplina['id_disciplina']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($disciplina['codigoDisciplina']) . ' (' . htmlspecialchars($professorNome) . ')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>

            <button type="submit"><?php echo $isUpdating ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>

        <hr>
    </div>
    <a href="/dashboard">Voltar ao Dashboard</a> <hr>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>