<?php
session_start();
include("conexao.php");

$id = $_POST["id"];
$status = $_POST["status"];
$pendencia = $_POST["pendencia"];
$agente_id = $_SESSION["agente_id"];

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
    $queryProcesso = "UPDATE processos SET status = ?, pendencia = ? WHERE id = ?";
    $paramsProcesso = [$status, $pendencia, $id];
    $typesProcesso = "ssi";

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

    $stmtProcesso = $conexao->prepare($queryProcesso);
    $stmtProcesso->bind_param($typesProcesso, ...$paramsProcesso);
    $stmtProcesso->execute();

    $queryLocalizacoes = "UPDATE localizacoes 
        SET recebido_em = NOW() 
        WHERE destino_tipo = 'usuario' 
        AND destino_id = ? 
        AND id_processo = ?
        AND recebido_em IS NULL";
    $paramsLocalizacao = [$agente_id, $id];
    $typesLocalizacao = "ii";

    $stmtLocalizacao = $conexao->prepare($queryLocalizacoes);
    $stmtLocalizacao->bind_param($typesLocalizacao, ...$paramsLocalizacao);
    $stmtLocalizacao->execute();

    // --- Log da alteração ---
    $acao = "Atualização de status e pendência do processo";
    $detalhes = "Status: $status; Pendência: $pendencia";
    
    $queryLog = "INSERT INTO logs_processo (id_processo, agente_id, acao, detalhes) 
                    VALUES (?, ?, ?, ?)";
    $paramsLog = [$id, $agente_id, $acao, $detalhes];
    $typesLog = "iiss";

    $stmtLog = $conexao->prepare($queryLog);
    $stmtLog->bind_param($typesLog, ...$paramsLog);
    $stmtLog->execute();

    // Commita tudo
    $conexao->commit();
    echo json_encode(["mensagem" => "Análise Registrada."]);

} catch (Exception $e) {
    $conexao->rollback();
    echo json_encode(["mensagem" => "Erro ao atualizar processo: " . $e->getMessage()]);
}
?>