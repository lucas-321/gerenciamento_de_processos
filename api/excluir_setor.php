<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir setores."]);
    exit;
}

$id = $_POST["id"];
$nome = $_POST["sigla"];

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

    // --- Log da alteração ---
    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "deletar";
    $objeto = "setor";
    $data_atual = date("d/m/Y H:i:s");
    $detalhes = "$nome_usuario deletou o $objeto $nome em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Setor deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Erro ao deletar setor: " . $e->getMessage()]);
}
?>