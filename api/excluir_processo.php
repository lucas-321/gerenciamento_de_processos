<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir processos."]);
    exit;
}

$id = $_POST["id"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos SET ativo = ?";
    $params = [0];
    $types = "i";

    $queryProcesso .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryProcesso);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao deletar processo: " . $e->getMessage()]);
}
?>