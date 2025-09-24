<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir certidões."]);
    exit;
}

$id = $_POST["id"];
$processo = $_POST["processo"];
$nome = $_POST["n_protocolo"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE certidoes SET ativo = ?";
    $params = [0];
    $types = "i";

    $queryProcesso .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryProcesso);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos SET status = ? WHERE id = ?";
    $paramsProcesso = ["Sob Análise", $processo];
    $typesProcesso = "si";

    $stmtProcesso = $conexao->prepare($queryProcesso);
    $stmtProcesso->bind_param($typesProcesso, ...$paramsProcesso);
    $stmtProcesso->execute();
    // Fim

    // --- Log da alteração ---
    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "deletar";
    $objeto = "certidão";
    $data_atual = date("d/m/Y H:i:s");
    $detalhes = "$nome_usuario deletou a $objeto $nome em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Certidão deletada com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Erro ao deletar certidão: " . $e->getMessage()]);
}
?>