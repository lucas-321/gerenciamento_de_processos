<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

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

    // --- Log da alteração ---
    // Buscar processo
    $stmtProcesso = $conexao->prepare("SELECT * FROM processos WHERE id = ?");
    $stmtProcesso->bind_param("i", $id);
    $stmtProcesso->execute();
    $resultado = $stmtProcesso->get_result();
    $processo = $resultado->fetch_assoc();

    if (!$processo) {
        throw new Exception("Processo não encontrado.");
    }
    // Fim da Busca ao processo

    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "finalizar";
    $objeto = "processo";
    $data_atual = date("d/m/Y H:i:s");
    $n_protocolo = $processo["n_protocolo"];
    $data_processo = $processo["data_processo"];
    $ano = date("Y", strtotime($data_processo));
    $detalhes = "$nome_usuario finalizou o $objeto $n_protocolo/$ano em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo finalizado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao finalizar processo: " . $e->getMessage()]);
}
?>