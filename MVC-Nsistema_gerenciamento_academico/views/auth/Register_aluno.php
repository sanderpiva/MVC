<?php
require_once __DIR__ . '/../../models/conexao.php';

$isUpdating = false;
$alunoData = [];
$errors = "";

// Verifica se é edição
if (isset($_GET['id_aluno']) && !empty($_GET['id_aluno'])) {
    $isUpdating = true;
    $idAlunoToUpdate = filter_input(INPUT_GET, 'id_aluno', FILTER_SANITIZE_NUMBER_INT);

    if ($idAlunoToUpdate === false || $idAlunoToUpdate === null) {
        $errors = "<p style='color:red;'>ID de aluno inválido.</p>";
    } else {
        $stmt = $conexao->prepare("SELECT registroAluno, nome, email, endereco, telefone FROM aluno WHERE id_aluno = :id");
        $stmt->execute([':id' => $idAlunoToUpdate]);
        $alunoData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$alunoData) {
            $errors = "<p style='color:red;'>Aluno com ID $idAlunoToUpdate não encontrado.</p>";
            $isUpdating = false;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title><?php echo $isUpdating ? 'Atualizar Aluno' : 'Cadastro Aluno'; ?></title>
    <link rel="stylesheet" href="../../public/css/style.css">
</head>
<body class="servicos_forms">

    <div class="form_container">
        <form class="form" action="<?php echo $isUpdating ? '../../controllers/aluno/atualizar.php' : '../../controllers/aluno/cadastrar.php'; ?>" method="POST">
            <h2><?php echo $isUpdating ? 'Atualizar Aluno' : 'Cadastro Aluno'; ?></h2>
            <hr>

            <label for="registroAluno">Registro:</label>
            <input type="text" name="registroAluno" id="registroAluno" placeholder="Digite o registro" value="<?php echo htmlspecialchars($alunoData['registroAluno'] ?? ''); ?>" required>
            <?php if ($isUpdating): ?>
                <input type="hidden" name="id_aluno" value="<?php echo htmlspecialchars($_GET['id_aluno'] ?? ''); ?>">
            <?php endif; ?>
            <hr>

            <label for="nomeAluno">Nome:</label>
            <input type="text" name="nomeAluno" id="nomeAluno" placeholder="Digite o nome" value="<?php echo htmlspecialchars($alunoData['nome'] ?? ''); ?>" required>
            <hr>

            <label for="emailAluno">Login/Email:</label>
            <input type="email" name="emailAluno" id="emailAluno" placeholder="Digite o email" value="<?php echo htmlspecialchars($alunoData['email'] ?? ''); ?>" required>
            <hr>

            <label for="enderecoAluno">Endereço:</label>
            <input type="text" name="enderecoAluno" id="enderecoAluno" placeholder="Digite o endereço" value="<?php echo htmlspecialchars($alunoData['endereco'] ?? ''); ?>" required>
            <hr>

            <label for="telefoneAluno">Telefone:</label>
            <input type="text" name="telefoneAluno" id="telefoneAluno" placeholder="Digite o telefone" value="<?php echo htmlspecialchars($alunoData['telefone'] ?? ''); ?>" required>
            <hr>

            <?php if ($isUpdating): ?>
                <label for="novaSenha">Nova Senha:</label>
                <input type="password" id="novaSenha" name="novaSenha" placeholder="Digite a nova senha (opcional)">
                <input type="hidden" name="acao" value="atualizar">
            <?php else: ?>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" placeholder="Digite a senha" required>
                <input type="hidden" name="acao" value="cadastrar">
            <?php endif; ?>

            <br><br>
            <button type="submit"><?php echo $isUpdating ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>

        <?php if (!empty($errors)): ?>
            <?php echo $errors; ?>
        <?php endif; ?>
    </div>
    <a href="<?php echo $isUpdating ? '../../controllers/aluno/listar.php' : '../../index.php'; ?>">
        <?php echo $isUpdating ? 'Voltar para Alunos' : 'Home Page'; ?>
    </a>
</body>
</html>
