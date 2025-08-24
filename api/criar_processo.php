<?php
ob_start(); 
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2 && $_SESSION["categoria"] != 3) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar processos."]);
    exit;
}

// Dados vindos do formulário
$n_protocolo      = $_POST["n_protocolo"];
$data_processo    = $_POST["data_processo"];
$assunto          = $_POST["assunto"];
$inscricoes       = isset($_POST["inscricao"]) ? $_POST["inscricao"] : [];
$inscricao        = implode(", ", $inscricoes);
$nome_interessado = $_POST["nome_interessado"];
$cpf              = $_POST["cpf"];
$email            = $_POST["email"];
$telefone         = $_POST["telefone"];
$observacoes      = $_POST["observacoes"];
$criado_por       = $_SESSION["usuario_id"];

// === Verificação prévia de duplicidade ===
$check = $conexao->prepare("
    SELECT id 
    FROM processos 
    WHERE n_protocolo = ? 
      AND data_processo = ?
");
$check->bind_param("ss", $n_protocolo, $data_processo);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["mensagem" => "Já existe um processo com este número de protocolo para esta data."]);
    exit;
}

// === Início da transação ===
$conexao->begin_transaction();

try {
    // Inserir processo
    $stmt = $conexao->prepare("
        INSERT INTO processos (
            n_protocolo, data_processo, assunto, inscricao, 
            nome_interessado, cpf_cnpj, email, telefone, observacoes, 
            ativo, created_at, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)
    ");
    $stmt->bind_param(
        "ssissssssi", 
        $n_protocolo, $data_processo, $assunto, $inscricao, 
        $nome_interessado, $cpf, $email, $telefone, $observacoes, $criado_por
    );
    $stmt->execute();

    // Log da alteração
    $nome_usuario = $_SESSION["nome"];
    $id_usuario   = $_SESSION["usuario_id"];
    $tipo         = "criar";
    $objeto       = "processo";
    $ano          = date("Y", strtotime($data_processo));
    $data_atual   = date("d/m/Y H:i:s");
    $detalhes     = "$nome_usuario criou o $objeto $n_protocolo/$ano em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);

    $conexao->commit();
    ob_clean();
    echo json_encode(["mensagem" => "Processo criado com sucesso."]);

} catch (mysqli_sql_exception $e) {
    $conexao->rollback();
    ob_clean();

    if ($e->getCode() == 1062) {
        echo json_encode(["mensagem" => "Já existe um processo com este número de protocolo e data."]);
    } else {
        echo json_encode(["mensagem" => "Erro ao criar processo: " . $e->getMessage()]);
    }
}
