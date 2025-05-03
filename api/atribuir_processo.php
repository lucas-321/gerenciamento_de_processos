<?php
session_start();
include("conexao.php");

// Verifica se os campos esperados foram enviados
if (!isset($_POST['id'], $_POST['destino'])) {
    echo json_encode(["mensagem" => "Dados insuficientes."]);
    exit;
}

$id_processo = $_POST['id'];
$destino = $_POST['destino']; // "usuario", "setor" ou "pasta"

// Pegar o ID selecionado
$destino_id = null;
if ($destino == 'usuario' && isset($_POST['usuario'])) {
    $destino_id = $_POST['usuario'];
} elseif ($destino == 'setor' && isset($_POST['setor'])) {
    $destino_id = $_POST['setor'];
} elseif ($destino == 'pasta' && isset($_POST['pasta'])) {
    $destino_id = $_POST['pasta'];
} else {
    echo json_encode(["mensagem" => "Destino ou ID inválido."]);
    exit;
}

// ID de quem está localizando
$localizado_por_id = $_SESSION['agente_id'] ?? null;
if (!$localizado_por_id) {
    echo json_encode(["mensagem" => "Usuário não autenticado."]);
    exit;
}

// Inserção no banco
$sql = "INSERT INTO localizacoes (id_processo, destino_tipo, destino_id, localizado_por_id, localizado_em, ativo)
        VALUES (?, ?, ?, ?, NOW(), 1)";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("isii", $id_processo, $destino, $destino_id, $localizado_por_id);

if ($stmt->execute()) {
    echo json_encode(["mensagem" => "Processo atribuído com sucesso."]);
} else {
    echo json_encode(["mensagem" => "Erro ao atribuir processo."]);
}
?>