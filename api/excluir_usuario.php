<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem excluir usuários."]);
    exit;
}

$id = $_POST["id"];

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

    $conexao->commit();
    echo json_encode(["mensagem" => "Usuário deletado com sucesso!"]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao deletar usuário: " . $e->getMessage()]);
}
?>