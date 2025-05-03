<?php
session_start();
include("conexao.php");

if ($_SESSION["categoria"] != 1) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar partidos."]);
    exit;
}

$id = $_POST["id"];
$sigla = $_POST["sigla"];
$nome = $_POST["nome"];
$numero = $_POST["numero"];

// Foto (opcional)
$foto_nome = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = uniqid('foto_') . '.' . $ext;
    $destino = __DIR__ . '/../fotos_partido/' . $foto_nome;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo json_encode(["mensagem" => "Falha ao salvar a imagem."]);
        exit;
    }
}

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do partido dinamicamente
    $queryPartido = "UPDATE partidos 
    SET sigla = ?, nome = ?, numero = ?";
    $params = [$sigla, $nome, $numero];
    $types = "ssi";

    if ($foto_nome) {
        $queryPartido .= ", logo = ?";
        $params[] = $foto_nome;
        $types .= "s";
    }

    $queryPartido .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryPartido);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Partido alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar partido: " . $e->getMessage()]);
}
?>