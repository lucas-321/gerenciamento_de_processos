<?php
session_start();
include("conexao.php");

// if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
//     echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar usuários."]);
//     exit;
// }

$id = $_POST["id"];
$nome = $_POST["nome"];
$matricula = $_POST["matricula"];
$sexo = $_POST["sexo"];
$cpf = $_POST["cpf"];
$data_nascimento = $_POST["data_nascimento"];
$login = $_POST["login"];
$categoria = (int) $_POST["categoria"];

// Foto (opcional)
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

// Senha (opcional)
$senha = isset($_POST["senha"]) && !empty($_POST["senha"])
    ? password_hash($_POST["senha"], PASSWORD_DEFAULT)
    : null;

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do agente dinamicamente
    $queryAgente = "UPDATE agentes SET nome = ?, matricula = ?, sexo = ?, data_nascimento = ?, cpf = ?";
    $params = [$nome, $matricula, $sexo, $data_nascimento, $cpf];
    $types = "sssss";

    if ($foto_nome) {
        $queryAgente .= ", foto = ?";
        $params[] = $foto_nome;
        $types .= "s";
    }

    $queryAgente .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryAgente);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // Atualiza usuário
    $queryUsuario = "UPDATE usuarios SET login = ?, categoria = ?";
    $paramsUser = [$login, $categoria];
    $typesUser = "si";

    if ($senha) {
        $queryUsuario .= ", senha = ?";
        $paramsUser[] = $senha;
        $typesUser .= "s";
    }

    $queryUsuario .= " WHERE agente_id = ?";
    $paramsUser[] = $id;
    $typesUser .= "i";

    $stmt2 = $conexao->prepare($queryUsuario);
    $stmt2->bind_param($typesUser, ...$paramsUser);
    $stmt2->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Usuário alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar usuário/agente: " . $e->getMessage()]);
}
?>