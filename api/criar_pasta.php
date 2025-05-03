<?php
session_start();
include("conexao.php");

header('Content-Type: application/json'); // Para garantir que o navegador entenda o JSON

    if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
        echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar pastas."]);
        exit;
    }

    $nome = trim($_POST["nome"]);
    $cor = trim($_POST["cor"]);
    $criado_por = $_SESSION["usuario_id"];

    $conexao->begin_transaction();

    try {
        $stmt = $conexao->prepare("INSERT INTO pastas (nome, cor, ativo, created_at, criado_por) VALUES (?, ?,  1, NOW(), ?)");
        $stmt->bind_param("ssi", $nome, $cor, $criado_por);
        $stmt->execute();

        $novo_id = $conexao->insert_id;

        $conexao->commit();
        echo json_encode(["mensagem" => "Pasta criada com sucesso.", "novo_id" => $novo_id]);
    } catch (Exception $e) {
        $conexao->rollback();
        echo json_encode(["mensagem" => "Erro ao criar pasta: " . $e->getMessage()]);
    }
    
?>