<?php
// Retorna JSON sempre
header('Content-Type: application/json; charset=utf-8');

// Inclua a conexão (ajuste o caminho se necessário)
require '../../api/conexao.php';

// Termo digitado
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

// Se não digitar nada, retorna lista vazia
if ($q === '') {
    echo json_encode([]);
    exit;
}

// Consulta preparada para evitar SQL injection
$sql = "SELECT id, nome, numero, letra, assunto, status
        FROM pastas
        WHERE ativo = 1
          AND (
              nome    LIKE CONCAT('%', ?, '%') OR
              letra   LIKE CONCAT('%', ?, '%') OR
              numero  LIKE CONCAT('%', ?, '%') OR
              assunto LIKE CONCAT('%', ?, '%') OR
              status  LIKE CONCAT('%', ?, '%')
          )
        ORDER BY nome
        LIMIT 20";

$stmt = $conexao->prepare($sql);
$stmt->bind_param('sssss', $q, $q, $q, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

$pastas = [];
while ($row = $result->fetch_assoc()) {
    // Você pode enviar só os campos que quiser para o JS
    $pastas[] = [
        'id'     => $row['id'],
        'nome'   => $row['nome'],
        'numero' => $row['numero'],
        'letra'  => $row['letra'],
        'assunto'=> $row['assunto'],
        'status' => $row['status']
    ];
}

// Retorno final
echo json_encode($pastas);