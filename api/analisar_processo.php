<?php
session_start();
include("conexao.php");

// if ($_SESSION["categoria"] != 1) {
//     echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar processos."]);
//     exit;
// }

$id = $_POST["id"];
$status = $_POST["status"];
// $pendencia = isset($_POST["pendencia"]) && !empty($_POST["pendencia"])? $_POST["pendencia"]: null;
$pendencia = $_POST["pendencia"];

// Foto (opcional)
// $foto_nome = null;
// if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
//     $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
//     $foto_nome = uniqid('foto_') . '.' . $ext;
//     $destino = __DIR__ . '/../fotos_processo/' . $foto_nome;

//     if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
//         echo json_encode(["mensagem" => "Falha ao salvar a imagem."]);
//         exit;
//     }
// }

$conexao->begin_transaction();

try {
    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos 
    SET status = ?, pendencia = ?";
    $params = [$status, $pendencia];
    $types = "ss";

    // if ($pendencia) {
    //     $queryProcesso .= ", pendencia = ?";
    //     $paramsUser[] = $pendencia;
    //     $typesUser .= "s";
    // }

    // if ($foto_nome) {
    //     $queryProcesso .= ", logo = ?";
    //     $params[] = $foto_nome;
    //     $types .= "s";
    // }

    $queryProcesso .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryProcesso);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    $conexao->commit();
    echo json_encode(["mensagem" => "Processo alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar processo: " . $e->getMessage()]);
}
?>