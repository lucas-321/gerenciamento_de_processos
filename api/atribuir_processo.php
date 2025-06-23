<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

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

    // Buscar destino
    $stmtNomeDestino = $conexao->prepare("SELECT login, nome 
    FROM usuarios
    INNER JOIN agentes ON agente_id = agentes.id
    WHERE agentes.id = ?");
    $stmtNomeDestino->bind_param("i", $destino_id);
    $stmtNomeDestino->execute();
    $resultado = $stmtNomeDestino->get_result();
    $nome_destino = $resultado->fetch_assoc();

    if (!$nome_destino) {
        throw new Exception("Destino não encontrado.");
    }

    $nome = $nome_destino['nome'];

} elseif ($destino == 'setor' && isset($_POST['setor'])) {
    $destino_id = $_POST['setor'];

    // Buscar destino
    $stmtNomeDestino = $conexao->prepare("SELECT nome FROM setores WHERE id = ?");
    $stmtNomeDestino->bind_param("i", $destino_id);
    $stmtNomeDestino->execute();
    $resultado = $stmtNomeDestino->get_result();
    $nome_destino = $resultado->fetch_assoc();

    if (!$nome_destino) {
        throw new Exception("Destino não encontrado.");
    }

    $nome = $nome_destino['nome'];

} elseif ($destino == 'pasta' && isset($_POST['pasta'])) {
    $destino_id = $_POST['pasta'];

    // Buscar destino
    $stmtNomeDestino = $conexao->prepare("SELECT nome FROM pastas WHERE id = ?");
    $stmtNomeDestino->bind_param("i", $destino_id);
    $stmtNomeDestino->execute();
    $resultado = $stmtNomeDestino->get_result();
    $nome_destino = $resultado->fetch_assoc();

    if (!$nome_destino) {
        throw new Exception("Destino não encontrado.");
    }

    $nome = $nome_destino['nome'];

} else {
    echo json_encode(["mensagem" => "Destino ou ID inválido."]);
    exit;
}

//Alterar as demais localizações do processo
    $queryAssunto = "UPDATE localizacoes 
    SET atual = 0";

    $queryAssunto .= " WHERE id_processo = ?";
    $params[] = $id_processo;
    $types .= "i";

    $stmt = $conexao->prepare($queryAssunto);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

// ID de quem está localizando
$localizado_por_id = $_SESSION['agente_id'] ?? null;
if (!$localizado_por_id) {
    echo json_encode(["mensagem" => "Usuário não autenticado."]);
    exit;
}

// Inserção no banco
$sql = "INSERT INTO localizacoes (id_processo, destino_tipo, destino_id, localizado_por_id, localizado_em, ativo)
        VALUES (?, ?, ?, ?, NOW(), 1)";

// --- Log da alteração ---
// Buscar processo
$stmtProcesso = $conexao->prepare("SELECT * FROM processos WHERE id = ?");
$stmtProcesso->bind_param("i", $id_processo);
$stmtProcesso->execute();
$resultado = $stmtProcesso->get_result();
$processo = $resultado->fetch_assoc();

if (!$processo) {
    throw new Exception("Processo não encontrado.");
}
// Fim da Busca ao processo

$nome_usuario = $_SESSION["nome"];
$id_usuario = $_SESSION["usuario_id"];
$tipo = "atribuir";
$objeto = "processo";
$data_atual = date("d/m/Y H:i:s");
$n_protocolo = $processo["n_protocolo"];
$data_processo = $processo["data_processo"];
$ano = date("Y", strtotime($data_processo));
$detalhes = "$nome_usuario atribuiu o $objeto $n_protocolo/$ano para $nome em $data_atual.";

registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
// --- Fim do log ---

$stmt = $conexao->prepare($sql);
$stmt->bind_param("isii", $id_processo, $destino, $destino_id, $localizado_por_id);

if ($stmt->execute()) {
    echo json_encode(["mensagem" => "Processo atribuído com sucesso."]);
} else {
    echo json_encode(["mensagem" => "Erro ao atribuir processo."]);
}
?>