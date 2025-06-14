<?php
include("conexao.php");

$n_protocolo = $_POST["n_protocolo"];
$data_processo = $_POST["data_processo"];

$stmt = $conexao->prepare("SELECT id FROM processos WHERE n_protocolo = ? AND data_processo = ? AND ativo = 1");
$stmt->bind_param("ss", $n_protocolo, $data_processo);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["existe" => true]);
} else {
    echo json_encode(["existe" => false]);
}