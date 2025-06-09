<?php
session_start();
include("conexao.php");

$processo = $_POST["processo"];

$nome_proprietario = $_POST["nome_proprietario"];
$cpf_cnpj = $_POST["cpf_cnpj"];

// Transferência de Titularidade
$endereco_cadastrado = $_POST["endereco_cadastrado"];
$trecho_documento = $_POST["trecho_documento"];

// Relatório de Visita
$data_visita = $_POST["data_visita"];
$nome_fiscal = $_POST["nome_fiscal"];
$matricula_fiscal = $_POST["matricula_fiscal"];
$relatorio_visita = $_POST["relatorio_visita"];

// Gerais
$info_adicionais = $_POST["info_adicionais"];
$setor = $_POST["setor"];
$coordenador = $_POST["coordenador"];

// Dados do usuário
$criado_por = $_SESSION["usuario_id"];

$conexao->begin_transaction();

try {
    $stmt = $conexao->prepare("INSERT INTO despachos (
        processo, nome_proprietario, cpf_cnpj, endereco_cadastrado, trecho_documento,
        data_visita, nome_fiscal, matricula_fiscal, relatorio_visita,
        info_adicionais, setor, coordenador, ativo, created_at, created_by
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)");

    $stmt->bind_param("ssssssssssssi", 
        $processo,
        $nome_proprietario,
        $cpf_cnpj,
        $endereco_cadastrado,
        $trecho_documento,
        $data_visita,
        $nome_fiscal,
        $matricula_fiscal,
        $relatorio_visita,
        $info_adicionais,
        $setor,
        $coordenador,
        $criado_por
    );

    $stmt->execute();
    $conexao->commit();

    echo json_encode(["mensagem" => "Despacho criado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao criar despacho: " . $e->getMessage()]);
}
?>