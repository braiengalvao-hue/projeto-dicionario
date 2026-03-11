<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Se não houver sessão, manda de volta para o login
    header("Location: login.php");
    exit;
}
?>
