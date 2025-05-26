<?php
include("conexao.php");

$login = $_GET["login"] ?? '';
$idAtual = $_GET["id"] ?? null;

if (!$login) {
    echo json_encode(["disponivel" => false, "mensagem" => "Login não informado."]);
    exit;
}

if ($idAtual) {
    // Verifica se o login já pertence a outro usuário
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE login = ? AND id != ? AND ativo = 1");
    $stmt->bind_param("si", $login, $idAtual);
} else {
    // Verificação padrão (cadastro)
    $stmt = $conexao->prepare("SELECT id FROM usuarios WHERE login = ? AND ativo = 1");
    $stmt->bind_param("s", $login);
}

$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["disponivel" => false, "mensagem" => "Este login já está em uso."]);
} else {
    echo json_encode(["disponivel" => true, "mensagem" => "Login disponível."]);
}