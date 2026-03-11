<?php
session_start();
require_once '../config/db.php'; // Ajustado conforme imagem
header('Content-Type: application/json');


if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(["success" => false, "message" => "Acesso negado."]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $sql = "SELECT * FROM turmas";
        $result = $conn->query($sql);
        $turmas = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) { $turmas[] = $row; }
        }
        echo json_encode(["success" => true, "data" => $turmas]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));
        if (!isset($data->nome_turma)) {
            echo json_encode(["success" => false, "message" => "Nome da turma obrigatório."]);
            exit;
        }
        $nome = $conn->real_escape_string($data->nome_turma);
        $sql = "INSERT INTO turmas (nome_turma) VALUES ('$nome')";
        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "id_turma" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        break;

    case "PUT":
        $data = json_decode(file_get_contents("php://input"));
        $id = (int)$data->id_turma;
        $nome = $conn->real_escape_string($data->nome_turma);
        $sql = "UPDATE turmas SET nome_turma = '$nome' WHERE id_turma = $id";
        echo json_encode(["success" => $conn->query($sql)]);
        break;

    case "DELETE":
        $data = json_decode(file_get_contents("php://input"));
        $id = (int)$data->id_turma;
        $sql = "DELETE FROM turmas WHERE id_turma = $id";
        echo json_encode(["success" => $conn->query($sql)]);
        break;
}