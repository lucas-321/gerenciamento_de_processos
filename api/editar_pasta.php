<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar pastas."]);
    exit;
}

$id = $_POST["id"];
$cor = $_POST["cor"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do pasta dinamicamente
    $queryPasta = "UPDATE pastas 
    SET nome = ?,
    cor = ?";
    $params = [$nome, $cor];
    $types = "ss";

    $queryPasta .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryPasta);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Pasta alterada com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar pasta: " . $e->getMessage()]);
}
?>