<?php
session_start();
include("conexao.php");

header('Content-Type: application/json'); // Para garantir que o navegador entenda o JSON

    if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
        echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar assuntos."]);
        exit;
    }

    if (!isset($_POST["novo_assunto"]) || empty(trim($_POST["novo_assunto"]))) {
        echo json_encode(["mensagem" => "O campo de assunto é obrigatório."]);
        exit;
    }

    $novo_assunto = trim($_POST["novo_assunto"]);
    $criado_por = $_SESSION["usuario_id"];

    $conexao->begin_transaction();

    try {
        $stmt = $conexao->prepare("INSERT INTO assuntos (nome, ativo, created_at, criado_por) VALUES (?, 1, NOW(), ?)");
        $stmt->bind_param("si", $novo_assunto, $criado_por);
        $stmt->execute();

        $novo_id = $conexao->insert_id;

        $conexao->commit();
        echo json_encode(["mensagem" => "Assunto criado com sucesso.", "novo_id" => $novo_id]);
    } catch (Exception $e) {
        $conexao->rollback();
        echo json_encode(["mensagem" => "Erro ao criar Assunto: " . $e->getMessage()]);
    }
    
?>