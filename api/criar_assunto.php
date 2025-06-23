<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

header('Content-Type: application/json');

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
    // Inserção do assunto
        $stmt = $conexao->prepare("INSERT INTO assuntos (nome, ativo, created_at, criado_por) VALUES (?, 1, NOW(), ?)");
        $stmt->bind_param("si", $novo_assunto, $criado_por);
        $stmt->execute();

        $novo_id = $conexao->insert_id;

        // --- Log da alteração ---
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "criar";
        $objeto = "assunto";
        $data_atual = date("d/m/Y H:i:s");
        $detalhes = "$nome_usuario criou o assunto $novo_assunto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
        // --- Fim do log ---

        $conexao->commit();
        
        // Limpa qualquer saída antes do JSON
        ob_clean();
        echo json_encode(["mensagem" => "Assunto criado com sucesso.", "novo_id" => $novo_id]);
} catch (Exception $e) {
        $conexao->rollback();
        ob_clean(); // Garante que não vaze HTML do Exception
        echo json_encode(["mensagem" => "Erro ao criar Assunto: " . $e->getMessage()]);
}
?>
