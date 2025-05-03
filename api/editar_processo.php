<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar processos."]);
    exit;
}
$id = $_POST["id"]; // necessário para o WHERE

$n_protocolo = $_POST["n_protocolo"];
$data_processo = $_POST["data_processo"];
$assunto = $_POST["assunto"];
$inscricoes = isset($_POST["inscricao"]) ? $_POST["inscricao"] : [];
$inscricao = implode(", ", $inscricoes);
$nome_interessado = $_POST["nome_interessado"];
$cpf = $_POST["cpf_cnpj"]; // mas no update será 'cpf_cnpj'
$email = $_POST["email"];
$telefone = $_POST["telefone"];
$observacoes = $_POST["observacoes"];

$conexao->begin_transaction();

try {
    $querySessao = "UPDATE processos 
        SET n_protocolo = ?, data_processo = ?, assunto = ?, inscricao = ?, nome_interessado = ?, cpf_cnpj = ?, email = ?, telefone = ?, observacoes = ? 
        WHERE id = ?";

    $params = [$n_protocolo, $data_processo, $assunto, $inscricao, $nome_interessado, $cpf, $email, $telefone, $observacoes, $id];
    $types = "isissssssi";

    $stmt = $conexao->prepare($querySessao);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar processo: " . $e->getMessage()]);
}
?>