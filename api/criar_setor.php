<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

header('Content-Type: application/json'); // Para garantir que o navegador entenda o JSON

    if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
        echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar setores."]);
        exit;
    }

    $nome = trim($_POST["nome"]);
    $sigla = trim($_POST["sigla"]);
    $criado_por = $_SESSION["usuario_id"];

    $conexao->begin_transaction();

    try {
        $stmt = $conexao->prepare("INSERT INTO setores (nome, sigla, ativo, created_at, criado_por) VALUES (?, ?,  1, NOW(), ?)");
        $stmt->bind_param("ssi", $nome, $sigla, $criado_por);
        $stmt->execute();

        $novo_id = $conexao->insert_id;

        // --- Log da alteração ---
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "criar";
        $objeto = "setor";
        $data_atual = date("d/m/Y H:i:s");
        $detalhes = "$nome_usuario criou o $objeto $sigla em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
        // --- Fim do log ---

        $conexao->commit();
        // Limpa qualquer saída antes do JSON
        ob_clean();

        echo json_encode(["mensagem" => "Setor criado com sucesso.", "novo_id" => $novo_id]);
    } catch (Exception $e) {
        $conexao->rollback();
        ob_clean(); // Garante que não vaze HTML do Exception
        echo json_encode(["mensagem" => "Erro ao criar setor: " . $e->getMessage()]);
    }
    
?>