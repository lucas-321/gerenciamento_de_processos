<?php
ob_start(); // <- inicia buffer de saída
session_start();
include("conexao.php");
include("funcoes.php");

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar pastas."]);
    exit;
}

$id = $_POST["id"];
$cor = $_POST["cor"];
$nome = $_POST["nome"];

$conexao->begin_transaction();

try {
    // 1. Buscar dados antigos
    $stmtOld = $conexao->prepare("SELECT nome, cor FROM pastas WHERE id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultado = $stmtOld->get_result();
    $antigo = $resultado->fetch_assoc();

    if (!$antigo) {
        throw new Exception("Pasta não encontrado.");
    }
    // Fim da Busca aos antigos

    // Monta a query de UPDATE do pasta dinamicamente
    $queryPasta = "UPDATE pastas 
    SET nome = ?,
    cor = ?";
    $params = [$nome, $cor];
    $types = "ss";

    $queryPasta .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryPasta);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // 3. Montar log das mudanças
    $alteracoes = [];
    if ($antigo["nome"] !== $nome) {
        $alteracoes[] = "nome de \"{$antigo['nome']}\" para \"$nome\"";
    }
    if ($antigo["cor"] !== $cor) {
        $alteracoes[] = "cor de \"{$antigo['cor']}\" para \"$cor\"";
    }

    if (!empty($alteracoes)) {
        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "editar";
        $objeto = "pasta";
        $detalhes = "$nome_usuario alterou a $objeto $nome: $alteracoesTexto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    }
    // Fim do log

    $conexao->commit();
    // Limpa qualquer saída antes do JSON
    ob_clean();
    echo json_encode(["mensagem" => "Pasta alterada com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean(); // Garante que não vaze HTML do Exception
    echo json_encode(["mensagem" => "Erro ao atualizar pasta: " . $e->getMessage()]);
}
?>