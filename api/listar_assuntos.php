<?php
include("conexao.php");

$sql = "SELECT id, nome FROM assuntos WHERE ativo = 1 ORDER BY nome";
$result = $conexao->query($sql);

$assuntos = [];

while($row = $result->fetch_assoc()) {
    $assuntos[] = $row;
}

echo json_encode($assuntos);
?>