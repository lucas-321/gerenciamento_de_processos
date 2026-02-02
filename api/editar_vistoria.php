<?php
ob_start();
session_start();

include("conexao.php");
include("funcoes.php");

/*
// Controle de acesso (se quiser reativar)
if (!in_array($_SESSION["categoria"] ?? null, [1, 2, 3])) {
    echo json_encode(["mensagem" => "Acesso negado."]);
    exit;
}
*/

$id = (int) ($_POST["id"] ?? 0);

$id_processo = (int) ($_POST['processo'] ?? 0);
$id_fiscal   = (int) ($_POST['usuario'] ?? 0);
$data_visita = $_POST['data_visita'] ?? null;
$data_processo = $_POST['data_processo'] ?? null;
$n_protocolo = $_POST['n_protocolo'] ?? null;
$informacoes_adicionais = $_POST['informacoes_adicionais'] ?? null;

$conexao->begin_transaction();

try {

    /* =============================
       1. Buscar dados antigos
    ============================= */
    $stmtOld = $conexao->prepare("
        SELECT 
            vistorias.processo,
            vistorias.data_visita,
            vistorias.informacoes_adicionais,
            vistorias.fiscal,
            agentes.nome AS agente
        FROM vistorias
        INNER JOIN usuarios ON vistorias.fiscal = usuarios.id
        INNER JOIN agentes ON usuarios.agente_id = agentes.id
        WHERE vistorias.id = ?
    ");

    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $antigo = $stmtOld->get_result()->fetch_assoc();
    $stmtOld->close();

    if (!$antigo) {
        throw new Exception("Vistoria não encontrada.");
    }

    /* =============================
       2. Buscar nome do novo fiscal
    ============================= */
    $stmtFiscal = $conexao->prepare("
        SELECT agentes.nome
        FROM agentes
        INNER JOIN usuarios ON agentes.id = usuarios.agente_id
        WHERE usuarios.id = ?
    ");
    $stmtFiscal->bind_param("i", $id_fiscal);
    $stmtFiscal->execute();
    $resultadoFiscal = $stmtFiscal->get_result()->fetch_assoc();
    $stmtFiscal->close();

    if (!$resultadoFiscal) {
        throw new Exception("Fiscal não encontrado.");
    }

    $nome_fiscal = $resultadoFiscal['nome'];

    /* =============================
       3. Atualizar vistoria
    ============================= */
    $queryUpdate = "
        UPDATE vistorias 
        SET 
            processo = ?, 
            data_visita = ?, 
            informacoes_adicionais = ?, 
            fiscal = ?
        WHERE id = ?
    ";

    $stmt = $conexao->prepare($queryUpdate);
    $stmt->bind_param(
        "issii",
        $id_processo,
        $data_visita,
        $informacoes_adicionais,
        $id_fiscal,
        $id
    );

    if (!$stmt->execute()) {
        throw new Exception("Erro ao atualizar vistoria.");
    }

    $stmt->close();

    /* =============================
       4. Montar log de alterações
    ============================= */
    $alteracoes = [];

    if ($antigo['data_visita'] !== $data_visita) {
        $alteracoes[] = "data_visita de \"{$antigo['data_visita']}\" para \"$data_visita\"";
    }

    if ((int)$antigo['fiscal'] !== (int)$id_fiscal) {
        $alteracoes[] = "Servidor Responsável de \"{$antigo['agente']}\" para \"$nome_fiscal\"";
    }

    if ($antigo['informacoes_adicionais'] !== $informacoes_adicionais) {
        $alteracoes[] = "Informações Adicionais alteradas";
    }

    if (!empty($alteracoes)) {

        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");

        $nome_usuario = $_SESSION["nome"] ?? 'Sistema';
        $id_usuario = $_SESSION["usuario_id"] ?? null;

        $tipo = "editar";
        $objeto = "processo";

        $ano = $data_processo ? date("Y", strtotime($data_processo)) : date("Y");

        $detalhes = "$nome_usuario alterou o $objeto $n_protocolo/$ano: $alteracoesTexto em $data_atual.";

        registrarAtividade(
            $conexao,
            $id_usuario,
            $nome_usuario,
            $tipo,
            $objeto,
            $detalhes
        );
    }

    /* =============================
       5. Finalizar
    ============================= */
    $conexao->commit();

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode(["mensagem" => "Processo alterado com sucesso."]);

} catch (Exception $e) {

    $conexao->rollback();

    if (ob_get_length()) {
        ob_clean();
    }

    echo json_encode([
        "mensagem" => "Erro ao atualizar processo: " . $e->getMessage()
    ]);
}
