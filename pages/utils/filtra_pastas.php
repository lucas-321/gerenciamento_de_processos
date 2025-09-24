<?php
include('../api/conexao.php');

$numero = $_GET['numero'] ?? null;
$letra  = $_GET['letra'] ?? null;
$assunto= $_GET['assunto'] ?? null;
$status = $_GET['status'] ?? null;

$where = "WHERE ativo=1";
$params = [];

if($numero) $where .= " AND numero=".(int)$numero;
if($letra)  $where .= " AND letra='".mysqli_real_escape_string($conexao,$letra)."'";
if($assunto)$where .= " AND assunto='".mysqli_real_escape_string($conexao,$assunto)."'";
if($status) $where .= " AND status='".mysqli_real_escape_string($conexao,$status)."'";

$sql = "SELECT id,nome,numero,letra,assunto,status FROM pastas $where ORDER BY nome";
$res = mysqli_query($conexao,$sql);

$data = [];
while($r = mysqli_fetch_assoc($res)) $data[] = $r;

header('Content-Type: application/json');
echo json_encode($data);
