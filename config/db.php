<?php

$host = "localhost";
$user = "braien";
$password = "1234";
$database = "sgd";

$conn = new mysqli($host, $user, $password, $database);

// Verifica conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

