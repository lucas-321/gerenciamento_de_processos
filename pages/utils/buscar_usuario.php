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
$sql = "SELECT usuarios.id, nome
        FROM usuarios
        INNER JOIN agentes ON agentes.id = agente_id
        WHERE usuarios.ativo = 1
        AND nome LIKE CONCAT('%', ?, '%')
        AND usuarios.id <> 1
        ORDER BY nome
        LIMIT 20";

$stmt = $conexao->prepare($sql);
$stmt->bind_param('s', $q);
$stmt->execute();
$result = $stmt->get_result();

$usuarios = [];
while ($row = $result->fetch_assoc()) {
    // Você pode enviar só os campos que quiser para o JS
    $usuarios[] = [
        'id'     => $row['id'],
        'nome'   => $row['nome']
    ];
}

// Retorno final
echo json_encode($usuarios);