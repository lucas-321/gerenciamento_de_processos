<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir usuários."]);
    exit;
}

$id = $_POST["id"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do agente dinamicamente
    $queryAgente = "UPDATE agentes SET ativo = ?";
    $params = [0];
    $types = "i";

    $queryAgente .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryAgente);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Atualiza usuário
    $queryUsuario = "UPDATE usuarios SET ativo = ?";
    $paramsUser = [0];
    $typesUser = "i";

    $queryUsuario .= " WHERE agente_id = ?";
    $paramsUser[] = $id;
    $typesUser .= "i";

    $stmt2 = $conexao->prepare($queryUsuario);
    $stmt2->bind_param($typesUser, ...$paramsUser);
    $stmt2->execute();

    // --- Log da alteração ---
    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "deletar";
    $objeto = "usuario";
    $data_atual = date("d/m/Y H:i:s");
    $detalhes = "$nome_usuario deletou o $objeto $nome em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Usuário deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Erro ao deletar usuário: " . $e->getMessage()]);
}
?>