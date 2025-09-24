<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

// if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
//     echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar processos."]);
//     exit;
// }
$id = $_POST["certidao"]; // necessário para o WHERE

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

$conexao->begin_transaction();

try {
    // 1. Buscar dados antigos
    $stmtOld = $conexao->prepare("SELECT certidoes.*, n_protocolo
    FROM certidoes
    INNER JOIN processos ON processos.id = certidoes.processo
    WHERE certidoes.id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultado = $stmtOld->get_result();
    $antigo = $resultado->fetch_assoc();

    if (!$antigo) {
        throw new Exception("Certidao não encontrada.");
    }
    // Fim da Busca aos antigos

    $querySessao = "UPDATE certidoes 
        SET tipo = ?, data_certidao = ?, nome_proprietario = ?, cpf_cnpj = ?, endereco = ?, numero_porta = ?, valor_venal = ?, trecho_documento = ?, endereco_atual = ?, data_itiv = ?, valor_itiv = ?, aliquota_itiv = ?, valor_transacao = ?, numero_dam = ?, nome_transmitente = ?, cpf_cnpj_transmitente = ?, nome_adquirente = ?, cpf_cnpj_adquirente = ?, data_pagamento_itiv = ?, descricao_metragem = ?, informacoes_adicionais = ?
        WHERE id = ?";

    $params = [$tipo, $data_certidao, $nome_proprietario, $cpf_cnpj, $endereco, $numero_porta, $valor_venal, $trecho_documento, $endereco_atual, $data_itiv, $valor_itiv, $aliquota_itiv, $valor_transacao, $dam, $nome_transmitente, $cpf_cnpj_transmitente, $nome_adquirente, $cpf_cnpj_adquirente, $data_pagamento_itiv, $descricao_metragem, $informacoes_adicionais, $id];
    $types = "sssssssssssssssssssssi";

    $stmt = $conexao->prepare($querySessao);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // 3. Montar log das mudanças
    $n_protocolo = $antigo["n_protocolo"];

    $alteracoes = [];
    if ($antigo["tipo"] !== $tipo) {
        $alteracoes[] = "nº de protocolo de \"{$antigo['tipo']}\" para \"$tipo\"";
    }
    
    if ($antigo["data_certidao"] !== $data_certidao) {
        $alteracoes[] = "data_certidao de \"{$antigo['data_certidao']}\" para \"$data_certidao\"";
    }

    if ($antigo["nome_proprietario"] !== $nome_proprietario) {
        $alteracoes[] = "nome_proprietario de \"{$antigo['n_nome_proprietario']}\" para \"$nome_proprietario_nome\"";
    }

    if ($antigo["endereco"] !== $endereco) {
        $alteracoes[] = "inscrição de \"{$antigo['endereco']}\" para \"$endereco\"";
    }

    if ($antigo["numero_porta"] !== $numero_porta) {
        $alteracoes[] = "nome do interessado de \"{$antigo['numero_porta']}\" para \"$numero_porta\"";
    }

    if ($antigo["numero_porta"] !== $cpf) {
        $alteracoes[] = "CPF ou CNPJ de \"{$antigo['numero_porta']}\" para \"$cpf\"";
    }

    if ($antigo["valor_venal"] !== $valor_venal) {
        $alteracoes[] = "e-mail de \"{$antigo['valor_venal']}\" para \"$valor_venal\"";
    }

    if ($antigo["trecho_documento"] !== $trecho_documento) {
        $alteracoes[] = "trecho_documento do interessado de \"{$antigo['trecho_documento']}\" para \"$trecho_documento\"";
    }

    if ($antigo["endereco_atual"] !== $endereco_atual) {
        $alteracoes[] = "endereço atual de \"{$antigo['endereco_atual']}\" para \"$endereco_atual\"";
    }

    if ($antigo["data_itiv"] !== $data_itiv) {
        $alteracoes[] = "data de lançamento do ITIV de \"{$antigo['data_itiv']}\" para \"$data_itiv\"";
    }

    if ($antigo["valor_itiv"] !== $valor_itiv) {
        $alteracoes[] = "valor do ITIV de \"{$antigo['valor_itiv']}\" para \"$valor_itiv\"";
    }

    if ($antigo["aliquota_itiv"] !== $aliquota_itiv) {
        $alteracoes[] = "alíquota de itiv de \"{$antigo['aliquota_itiv']}\" para \"$aliquota_itiv\"";
    }

    if ($antigo["valor_transacao"] !== $valor_transacao) {
        $alteracoes[] = "valor de transação de \"{$antigo['valor_transacao']}\" para \"$valor_transacao\"";
    }

    if ($antigo["numero_dam"] !== $dam) {
        $alteracoes[] = "número do DAM de \"{$antigo['numero_dam']}\" para \"$dam\"";
    }

    if ($antigo["nome_transmitente"] !== $nome_transmitente) {
        $alteracoes[] = "nome do transmitente de \"{$antigo['nome_transmitente']}\" para \"$nome_transmitente\"";
    }

    if ($antigo["cpf_cnpj_transmitente"] !== $cpf_cnpj_transmitente) {
        $alteracoes[] = "CPF ou CNPJ do transmitente de \"{$antigo['cpf_cnpj_transmitente']}\" para \"$cpf_cnpj_transmitente\"";
    }

    if ($antigo["nome_adquirente"] !== $nome_adquirente) {
        $alteracoes[] = "nome do adquirente de \"{$antigo['nome_adquirente']}\" para \"$nome_adquirente\"";
    }

    if ($antigo["cpf_cnpj_adquirente"] !== $cpf_cnpj_adquirente) {
        $alteracoes[] = "CPF ou CNPJ do adquirente \"{$antigo['cpf_cnpj_adquirente']}\" para \"$cpf_cnpj_adquirente\"";
    }

    if ($antigo["data_pagamento_itiv"] !== $data_pagamento_itiv) {
        $alteracoes[] = "data de pagamento do ITIV de \"{$antigo['data_pagamento_itiv']}\" para \"$data_pagamento_itiv\"";
    }

    if ($antigo["descricao_metragem"] !== $descricao_metragem) {
        $alteracoes[] = "descrição de metragem de \"{$antigo['descricao_metragem']}\" para \"$descricao_metragem\"";
    }

    if ($antigo["informacoes_adicionais"] !== $informacoes_adicionais) {
        $alteracoes[] = "informações adicionais de \"{$antigo['informacoes_adicionais']}\" para \"$informacoes_adicionais\"";
    }


    if (!empty($alteracoes)) {
        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "editar";
        $objeto = "certidão";
        $ano = date("Y", strtotime($data_certidao));
        $detalhes = "$nome_usuario alterou a $objeto $n_protocolo/$ano: $alteracoesTexto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    }
    // Fim do log

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Certidão alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean(); // Garante que não vaze HTML do Exception
    echo json_encode(["mensagem" => "Erro ao atualizar certidão: " . $e->getMessage()]);
}
?>