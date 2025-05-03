<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}
?>
<h1>Bem-vindo ao Painel</h1>
<p><a href="pages/cadastro_usuario.php">Cadastrar novo usuário</a></p>
<p><a href="logout.php">Sair</a></p>