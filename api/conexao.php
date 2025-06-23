<?php
date_default_timezone_set("America/Sao_Paulo");

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'gestao_processos';

$conexao = new mysqli($host, $user, $pass, $db);
if ($conexao->connect_error) {
    die("Conexão falhou: " . $conexao->connect_error);
}
?>