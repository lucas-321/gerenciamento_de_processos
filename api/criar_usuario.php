<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas administradores podem cadastrar usuários."]);
    exit;
}

// Dados do agente vindos do formulário
$nome = $_POST["nome"];
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

$conexao->begin_transaction();

try {
    // 1. Inserir agente
    $stmt = $conexao->prepare("INSERT INTO agentes (nome, sexo, data_nascimento, cpf, foto, ativo, created_at, criado_por) VALUES (?, ?, ?, ?, ?, 1, NOW(), ?)");
    $stmt->bind_param("sssssi", $nome, $sexo, $data_nascimento, $cpf, $foto_nome, $criado_por);
    $stmt->execute();
    $agente_id = $stmt->insert_id;

    // 2. Inserir usuário com o agente_id
    $stmt2 = $conexao->prepare("INSERT INTO usuarios (login, senha, categoria, agente_id, habilitado, ativo, created_at, criado_por) VALUES (?, ?, ?, ?, 1, 1, NOW(), ?)");
    $stmt2->bind_param("ssiii", $login, $senha, $categoria, $agente_id, $criado_por);
    $stmt2->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Usuário criado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao criar usuário/agente: " . $e->getMessage()]);
}