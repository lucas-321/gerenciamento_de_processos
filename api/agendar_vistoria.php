<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require 'conexao.php';
require 'funcoes.php';

/* =============================
   Validação dos dados
============================= */
if (
    empty($_POST['processo']) ||
    empty($_POST['usuario']) ||
    empty($_POST['data_visita']) 
) {
    echo json_encode(['mensagem' => 'Dados insuficientes.']);
    exit;
}

$id_processo  = (int) $_POST['processo'];
$id_fiscal    = (int) $_POST['usuario'];
$data_visita  = $_POST['data_visita'];
$informacoes_adicionais = $_POST['informacoes_adicionais'];
$status       = 'Visita Agendada';

/* =============================
   Início da transação
============================= */
$conexao->begin_transaction();

try {

    /* =============================
       Atualizar status do processo
    ============================= */
    $sqlStatus = "UPDATE processos SET status = ? WHERE id = ?";
    $stmt = $conexao->prepare($sqlStatus);
    $stmt->bind_param("si", $status, $id_processo);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao atualizar status do processo.');
    }
    $stmt->close();

    /* =============================
       Inserir vistoria
    ============================= */
    $sqlVistoria = "INSERT INTO vistorias (processo, data_visita, informacoes_adicionais, fiscal, created_by)
                    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conexao->prepare($sqlVistoria);
    $stmt->bind_param("issii", $id_processo, $data_visita, $informacoes_adicionais, $id_fiscal, $_SESSION['usuario_id']);

    if (!$stmt->execute()) {
        throw new Exception('Erro ao inserir vistoria.');
    }
    $stmt->close();

    /* =============================
       Buscar dados do processo (log)
    ============================= */
    $stmt = $conexao->prepare("SELECT n_protocolo, data_processo FROM processos WHERE id = ?");
    $stmt->bind_param("i", $id_processo);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $processo = $resultado->fetch_assoc();
    $stmt->close();

    if (!$processo) {
        throw new Exception('Processo não encontrado.');
    }

    /* =============================
       Registrar log
    ============================= */
    $nome_usuario = $_SESSION['nome'];
    $id_usuario   = $_SESSION['usuario_id'];

    $ano = date('Y', strtotime($processo['data_processo']));
    $data_atual = date('d/m/Y H:i:s');

    $detalhes = "{$nome_usuario} agendou a vistoria do processo "
              . "{$processo['n_protocolo']}/{$ano} "
              . "para {$data_visita} em {$data_atual}.";

    registrarAtividade(
        $conexao,
        $id_usuario,
        $nome_usuario,
        'agendar',
        'vistoria',
        $detalhes
    );

    /* =============================
       Commit
    ============================= */
    $conexao->commit();

    echo json_encode(['mensagem' => 'Vistoria agendada com sucesso.']);

} catch (Exception $e) {

    /* =============================
       Rollback
    ============================= */
    $conexao->rollback();

    echo json_encode([
        'mensagem' => $e->getMessage()
    ]);
}
