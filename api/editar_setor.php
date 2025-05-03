<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar setores."]);
    exit;
}

$id = $_POST["id"];
$sigla = $_POST["sigla"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do setor dinamicamente
    $querySetor = "UPDATE setores 
    SET nome = ?,
    sigla = ?";
    $params = [$nome, $sigla];
    $types = "ss";

    $querySetor .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($querySetor);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Setor alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar setor: " . $e->getMessage()]);
}
?>