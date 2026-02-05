<?php
include 'conexao.php';

$sql = "
    SELECT 
        v.id,
        v.data_visita,
        p.n_protocolo,
        v.status
    FROM vistorias v
    INNER JOIN processos p ON p.id = v.processo
    WHERE v.ativo = 1
";

$result = $conexao->query($sql);

$eventos = [];

while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        'id'    => $row['id'],
        'title' => 'Proc. ' . $row['n_protocolo'],
        'start' => $row['data_visita'],
        'color' => '#0d6efd' // azul bootstrap
    ];
}

echo json_encode($eventos);
