<?php
session_start();

if (!isset($_SESSION["usuario_id"]) || $_SESSION["categoria"] != 1) {
    header("Location: login.php");
    exit;
}