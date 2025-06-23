<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

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

$nome_assunto = $_POST["nome_assunto"];



$conexao->begin_transaction();

try {
    // 1. Buscar dados antigos
    $stmtOld = $conexao->prepare("SELECT n_protocolo, data_processo, assunto, inscricao, nome_interessado, cpf_cnpj, email, telefone, observacoes, assuntos.nome AS n_assunto
    FROM processos
    INNER JOIN assuntos ON assuntos.id = processos.assunto
    WHERE processos.id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultado = $stmtOld->get_result();
    $antigo = $resultado->fetch_assoc();

    if (!$antigo) {
        throw new Exception("Processo não encontrado.");
    }
    // Fim da Busca aos antigos

    $querySessao = "UPDATE processos 
        SET n_protocolo = ?, data_processo = ?, assunto = ?, inscricao = ?, nome_interessado = ?, cpf_cnpj = ?, email = ?, telefone = ?, observacoes = ? 
        WHERE id = ?";

    $params = [$n_protocolo, $data_processo, $assunto, $inscricao, $nome_interessado, $cpf, $email, $telefone, $observacoes, $id];
    $types = "isissssssi";

    $stmt = $conexao->prepare($querySessao);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // 3. Montar log das mudanças
    $alteracoes = [];
    if ($antigo["n_protocolo"] !== $n_protocolo) {
        $alteracoes[] = "nº de protocolo de \"{$antigo['n_protocolo']}\" para \"$n_protocolo\"";
    }
    
    if ($antigo["data_processo"] !== $data_processo) {
        $alteracoes[] = "data_processo de \"{$antigo['data_processo']}\" para \"$data_processo\"";
    }

    if ($antigo["assunto"] !== $assunto) {
        $alteracoes[] = "assunto de \"{$antigo['n_assunto']}\" para \"$assunto_nome\"";
    }

    if ($antigo["inscricao"] !== $inscricao) {
        $alteracoes[] = "inscrição de \"{$antigo['inscricao']}\" para \"$inscricao\"";
    }

    if ($antigo["nome_interessado"] !== $nome_interessado) {
        $alteracoes[] = "nome do interessado de \"{$antigo['nome_interessado']}\" para \"$nome_interessado\"";
    }

    if ($antigo["cpf_cnpj"] !== $cpf) {
        $alteracoes[] = "CPF ou CNPJ de \"{$antigo['cpf_cnpj']}\" para \"$cpf\"";
    }

    if ($antigo["email"] !== $email) {
        $alteracoes[] = "e-mail de \"{$antigo['email']}\" para \"$email\"";
    }

    if ($antigo["telefone"] !== $telefone) {
        $alteracoes[] = "telefone do interessado de \"{$antigo['telefone']}\" para \"$telefone\"";
    }

    if ($antigo["observacoes"] !== $observacoes) {
        $alteracoes[] = "observações do interessado de \"{$antigo['observacoes']}\" para \"$observacoes\"";
    }

    if (!empty($alteracoes)) {
        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "editar";
        $objeto = "processo";
        $ano = date("Y", strtotime($data_processo));
        $detalhes = "$nome_usuario alterou o $objeto $n_protocolo/$ano: $alteracoesTexto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    }
    // Fim do log

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Processo alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean(); // Garante que não vaze HTML do Exception
    echo json_encode(["mensagem" => "Erro ao atualizar processo: " . $e->getMessage()]);
}
?>