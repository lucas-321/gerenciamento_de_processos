<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar processos."]);
    exit;
}

// Dados do sessão vindos do formulário
$n_protocolo = $_POST["n_protocolo"];
$data_processo = $_POST["data_processo"];
$assunto = $_POST["assunto"];
// $inscricao = $_POST["inscricao"];
$inscricoes = isset($_POST["inscricao"]) ? $_POST["inscricao"] : [];
$inscricao = implode(", ", $inscricoes);
$nome_interessado = $_POST["nome_interessado"];
$cpf = $_POST["cpf"];
$email = $_POST["email"];
$telefone = $_POST["telefone"];
$observacoes = $_POST["observacoes"];

// Dados do usuário
$criado_por = $_SESSION["usuario_id"];

$conexao->begin_transaction();

try {
    // 1. Inserir processo
    $stmt = $conexao->prepare("INSERT INTO processos (n_protocolo, data_processo, assunto, inscricao, nome_interessado, cpf_cnpj, email, telefone, observacoes, ativo, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)");
    $stmt->bind_param("ssissssssi", $n_protocolo, $data_processo, $assunto, $inscricao, $nome_interessado, $cpf, $email, $telefone, $observacoes, $criado_por);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo criado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao criar processo: " . $e->getMessage()]);
}