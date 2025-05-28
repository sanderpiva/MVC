<?php
// models/conexao.php

// Dados de conexão
$servidor = 'localhost';
$usuario = 'root';
$senha = '';
$banco = 'gerenciamento_academico_completo';

// Tenta estabelecer a conexão PDO
try {
    $dsn = "mysql:host=$servidor;dbname=$banco;charset=utf8";
    $conexao = new PDO($dsn, $usuario, $senha);
    $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexão com o SGBD estabelecida com sucesso!"; // Remova em produção
} catch (PDOException $e) {
    // Em um ambiente MVC, é melhor não "morrer" aqui, mas lançar uma exceção ou
    // logar o erro e exibir uma página amigável ao usuário.
    // Por enquanto, para seguir o seu script, manteremos o die().
    die("Erro na conexão com o servidor: " . $e->getMessage());
}
?>