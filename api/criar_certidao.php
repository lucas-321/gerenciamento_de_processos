<?php
session_start();
include("conexao.php");
include("funcoes.php");

$processo = $_POST["processo"];
$tipo = $_POST["tipo"];

$nome_proprietario = $_POST["nome_proprietario"];
$cpf_cnpj = $_POST["cpf_cnpj"];
$endereco = $_POST["endereco"];
$data_certidao = $_POST["data_certidao"];

$numero_porta = !empty($_POST["numero"]) ? $_POST["numero"] : null;
$valor_venal = !empty($_POST["valor_venda"]) ? $_POST["valor_venda"] : null;
$trecho_documento = !empty($_POST["trecho_documento"]) ? $_POST["trecho_documento"] : null;
$endereco_atual = !empty($_POST["endereco_atual"]) ? $_POST["endereco_atual"] : null;
$descricao_metragem = !empty($_POST["descricao_metragem"]) ? $_POST["descricao_metragem"] : null;

// ITIV
$data_itiv = $_POST["data_lancamento_itiv"];
$valor_itiv = $_POST["valor_itiv"];
$aliquota_itiv = $_POST["aliquota"];
$valor_transacao = $_POST["valor_transacao"];
$dam = $_POST["dam"];
$nome_transmitente = $_POST["nome_transmitente"];
$cpf_cnpj_transmitente = $_POST["cpf_cnpj_transmitente"];
$nome_adquirente = $_POST["nome_adquirente"];
$cpf_cnpj_adquirente = $_POST["cpf_cnpj_adquirente"];
$data_pagamento_itiv = $_POST["data_pagamento_itiv"];

// Gerais
$informacoes_adicionais = $_POST["informacoes_adicionais"] ?? null;

// Dados do usuário
$criado_por = $_SESSION["usuario_id"];

$conexao->begin_transaction();

//Dados de Processo
$stmtProcess = $conexao->prepare("SELECT n_protocolo, data_processo
FROM processos
WHERE id = ?");
$stmtProcess->bind_param("i", $processo);
$stmtProcess->execute();
$resultado = $stmtProcess->get_result();
$dados_processo = $resultado->fetch_assoc();

$n_protocolo = $dados_processo["n_protocolo"];
$data_processo = $dados_processo["data_processo"];
//Fim

try {
    $stmt = $conexao->prepare("INSERT INTO certidoes (
        processo, tipo, data_certidao, nome_proprietario, cpf_cnpj, endereco, numero_porta, valor_venal, trecho_documento, endereco_atual,
        data_itiv, valor_itiv, aliquota_itiv, valor_transacao, numero_dam, nome_transmitente, cpf_cnpj_transmitente, nome_adquirente, cpf_cnpj_adquirente, data_pagamento_itiv,
        descricao_metragem, informacoes_adicionais, ativo, created_at, criado_por
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)");

    $stmt->bind_param("ssssssssssssssssssssssi", 
        $processo,
        $tipo,
        $data_certidao,
        $nome_proprietario,
        $cpf_cnpj,
        $endereco,
        $numero_porta,
        $valor_venal,
        $trecho_documento,
        $endereco_atual,

        $data_itiv,
        $valor_itiv,
        $aliquota_itiv,
        $valor_transacao,
        $dam,
        $nome_transmitente,
        $cpf_cnpj_transmitente,
        $nome_adquirente,
        $cpf_cnpj_adquirente,
        $data_pagamento_itiv,

        $descricao_metragem,
        $informacoes_adicionais,
        $criado_por
    );

    $stmt->execute();

    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos SET status = ? WHERE id = ?";
    $paramsProcesso = ["Certidão Elaborada", $processo];
    $typesProcesso = "si";

    $stmtProcesso = $conexao->prepare($queryProcesso);
    $stmtProcesso->bind_param($typesProcesso, ...$paramsProcesso);
    $stmtProcesso->execute();
    // Fim

    // Log da alteração
    $nome_usuario = $_SESSION["nome"];
    $id_usuario   = $_SESSION["usuario_id"];
    $tipo         = "criar";
    $objeto       = "certidao";
    $ano          = date("Y", strtotime($data_processo));
    $data_atual   = date("d/m/Y H:i:s");
    $detalhes     = "$nome_usuario criou a $objeto $n_protocolo/$ano em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);

    $conexao->commit();

    echo json_encode(["mensagem" => "Certidão criada com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao criar certidão: " . $e->getMessage()]);
}

?>