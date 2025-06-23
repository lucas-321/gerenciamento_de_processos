<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

$id = $_POST["id"];
$status = $_POST["status"];
$pendencia = $_POST["pendencia"];
$agente_id = $_SESSION["agente_id"];

$conexao->begin_transaction();

try {
    // 1. Buscar dados antigos
    $stmtOld = $conexao->prepare("SELECT status, pendencia, n_protocolo, data_processo
    FROM processos
    WHERE id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultado = $stmtOld->get_result();
    $antigo = $resultado->fetch_assoc();

    if (!$antigo) {
        throw new Exception("Processo não encontrado.");
    }
    // Fim da Busca aos antigos

    // Monta a query de UPDATE do processo dinamicamente
    $queryProcesso = "UPDATE processos SET status = ?, pendencia = ? WHERE id = ?";
    $paramsProcesso = [$status, $pendencia, $id];
    $typesProcesso = "ssi";

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

    // Log de alterações
    $alteracoes = [];
    if ($antigo["pendencia"] !== $pendencia) {
        $alteracoes[] = "pendência de \"{$antigo['pendencia']}\" para \"$pendencia\"";
    }
    
    if ($antigo["status"] !== $status) {
        $alteracoes[] = "status de \"{$antigo['status']}\" para \"$status\"";
    }

    if (!empty($alteracoes)) {
        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "analisar";
        $objeto = "processo";
        $n_protocolo = $antigo["n_protocolo"];
        $data_processo = $antigo["data_processo"];
        $ano = date("Y", strtotime($data_processo));
        $detalhes = "$nome_usuario alterou o $objeto $n_protocolo/$ano: $alteracoesTexto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    }
    // Fim do log

    // Commita tudo
    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Análise Registrada."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean(); // Garante que não vaze HTML do Exception
    echo json_encode(["mensagem" => "Erro ao atualizar processo: " . $e->getMessage()]);
}
?>