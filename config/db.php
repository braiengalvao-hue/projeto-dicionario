<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "sgd";

$conn = new mysqli($host, $user, $password, $database);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

