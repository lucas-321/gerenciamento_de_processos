<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir setores."]);
    exit;
}

$id = $_POST["id"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do setor dinamicamente
    $querySetor = "UPDATE setores SET ativo = ?";
    $params = [0];
    $types = "i";

    $querySetor .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($querySetor);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Setor deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao deletar setor: " . $e->getMessage()]);
}
?>