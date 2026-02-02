<?php
include("conexao.php");

// talvez eu use depois mas vou tentar ver isso direto no código
$processo = $_POST['processo'];

$stmt = $conexao->prepare("SELECT id FROM vistorias WHERE processo = ? AND ativo = 1");
$stmt->bind_param("s", $processo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["existe" => true]);
} else {
    echo json_encode(["existe" => false]);
}