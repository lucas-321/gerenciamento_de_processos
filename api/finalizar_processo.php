<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2  && $_SESSION["categoria"] != 3 ) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem finalizar processos."]);
    exit;
}

$id = $_POST["id"];
$status = $_POST["status"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos 
    SET status = ?";
    $params = [$status];
    $types = "s";

    $queryProcesso .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryProcesso);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo finalizado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao finalizar processo: " . $e->getMessage()]);
}
?>