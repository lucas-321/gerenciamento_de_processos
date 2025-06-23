<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir assuntos."]);
    exit;
}

$id = $_POST["id"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do assunto dinamicamente
    $queryAssunto = "UPDATE assuntos SET ativo = ?";
    $params = [0];
    $types = "i";

    $queryAssunto .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryAssunto);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // --- Log da alteração ---
    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "deletar";
    $objeto = "assunto";
    $data_atual = date("d/m/Y H:i:s");
    $detalhes = "$nome_usuario deletou o $objeto $nome em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    echo json_encode(["mensagem" => "Assunto deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao deletar assunto: " . $e->getMessage()]);
}
?>