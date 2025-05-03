<?php
session_start();
include("conexao.php");

$data = json_decode(file_get_contents("php://input"), true);
$login = $data["login"];
$senha = $data["senha"];

$sql = "SELECT login, senha, usuarios.id AS u_id, categoria, agente_id, nome, foto
        FROM usuarios
        INNER JOIN agentes ON agentes.id = agente_id
        WHERE login = '$login' 
        AND usuarios.ativo = 1 
        AND habilitado = 1";
$result = $conexao->query($sql);

if ($result && $result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario["senha"])) {
        $_SESSION["usuario_id"] = $usuario["u_id"];
        $_SESSION["categoria"] = $usuario["categoria"];
        $_SESSION["agente_id"] = $usuario["agente_id"];
        $_SESSION["nome"] = $usuario["nome"];
        $_SESSION["foto"] = $usuario["foto"];
        echo json_encode(["sucesso" => true]);
        exit;
    }
}

echo json_encode(["sucesso" => false, "mensagem" => "Login ou senha inválidos."]);