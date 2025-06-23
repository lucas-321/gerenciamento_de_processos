<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar usuários."]);
    exit;
}

// Dados do agente vindos do formulário
$nome = $_POST["nome"];
$matricula = $_POST["matricula"];
$sexo = $_POST["sexo"];
$cpf = $_POST["cpf"];
$data_nascimento = $_POST["data_nascimento"];

// Dados do usuário
$login = $_POST["login"];
$senha = password_hash($_POST["senha"], PASSWORD_DEFAULT);
$categoria = $_POST["categoria"];
$criado_por = $_SESSION["usuario_id"];

// Upload da foto
$foto_nome = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = uniqid('foto_') . '.' . $ext;
    $destino = __DIR__ . '/../fotos_perfil/' . $foto_nome;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo json_encode(["mensagem" => "Falha ao salvar a imagem."]);
        exit;
    }
}

//Verifica se o login já foi cadastrado
$stmt_verifica = $conexao->prepare("SELECT id FROM usuarios WHERE login = ? AND ativo = 1");
$stmt_verifica->bind_param("s", $login);
$stmt_verifica->execute();
$stmt_verifica->store_result();

if ($stmt_verifica->num_rows > 0) {
    echo json_encode(["mensagem" => "Este login já está em uso. Por favor, escolha outro."]);
    exit;
}
//Fim

$conexao->begin_transaction();

try {
    // 1. Inserir agente
    $stmt = $conexao->prepare("INSERT INTO agentes (nome, matricula, sexo, data_nascimento, cpf, foto, ativo, created_at, criado_por) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), ?)");
    $stmt->bind_param("ssssssi", $nome, $matricula, $sexo, $data_nascimento, $cpf, $foto_nome, $criado_por);
    $stmt->execute();
    $agente_id = $stmt->insert_id;

    // 2. Inserir usuário com o agente_id
    $stmt2 = $conexao->prepare("INSERT INTO usuarios (login, senha, categoria, agente_id, habilitado, ativo, created_at, criado_por) VALUES (?, ?, ?, ?, 1, 1, NOW(), ?)");
    $stmt2->bind_param("ssiii", $login, $senha, $categoria, $agente_id, $criado_por);
    $stmt2->execute();

    // --- Log da alteração ---
    $nome_usuario = $_SESSION["nome"];
    $id_usuario = $_SESSION["usuario_id"];
    $tipo = "criar";
    $objeto = "usuario";
    $data_atual = date("d/m/Y H:i:s");
    $detalhes = "$nome_usuario criou o $objeto $nome em $data_atual.";

    registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    // --- Fim do log ---

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Usuário criado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean(); // Garante que não vaze HTML do Exception
    echo json_encode(["mensagem" => "Erro ao criar usuário/agente: " . $e->getMessage()]);
}