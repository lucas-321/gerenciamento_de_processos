<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar assuntos."]);
    exit;
}

$id = $_POST["id"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do assunto dinamicamente
    $queryAssunto = "UPDATE assuntos 
    SET nome = ?";
    $params = [$nome];
    $types = "s";

    $queryAssunto .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryAssunto);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Assunto alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar assunto: " . $e->getMessage()]);
}
?>