<?php
session_start();
require_once '../config/db.php'; 
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

// Captura a categoria enviada pelo JS (ex: 'port' ou 'mat')
$categoria = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';

switch ($method) {

    case "GET":
        $sql = "SELECT 
                    termos.id_termo, 
                    termos.nome_termo, 
                    termos.descricao_termo, 
                    termos.cat_termo, 
                    termos.exemplo_termo, 
                    termos.foto_termo, 
                    termos.data_criacao, 
                    termos.status_termo, 
                    termos.nome_aluno, 
                    turmas.nome_turma, 
                    usuarios.nome_professor 
                FROM termos 
                INNER JOIN turmas ON termos.turmas_id_turma = turmas.id_turma 
                LEFT JOIN usuarios ON termos.id_moderador = usuarios.id_usuario 
                WHERE termos.status_termo = 'aprovado'";

        // Se o JavaScript passar ?cat=port ou ?cat=mat, adicionamos o filtro
        if ($categoria !== '') {
            $sql .= " AND termos.cat_termo = '$categoria'";
        }

        $sql .= " ORDER BY termos.nome_termo ASC";

        $result = $conn->query($sql);
        $termos = [];

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $termos[] = $row;
            }
        }

        echo json_encode([
            "success" => true,
            "data" => $termos
        ]);
        break;

    case "POST":
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->nome_termo) || !isset($data->descricao_termo) || !isset($data->turmas_id_turma)) {
            echo json_encode(["success" => false, "message" => "Dados incompletos."]);
            exit;
        }

        $nome_termo = $conn->real_escape_string($data->nome_termo);
        $descricao = $conn->real_escape_string($data->descricao_termo);
        $categoria_post = $conn->real_escape_string($data->cat_termo); 
        $nome_aluno = $conn->real_escape_string($data->nome_aluno);
        $id_turma = (int) $data->turmas_id_turma;

        // Ao inserir via formulário de aluno, o status padrão é 'pendente'
        $sql = "INSERT INTO termos (nome_termo, descricao_termo, cat_termo, nome_aluno, turmas_id_turma, status_termo) 
                VALUES ('$nome_termo', '$descricao', '$categoria_post', '$nome_aluno', $id_turma, 'pendente')";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "id_termo" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        break;

    case "PUT":
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(["success" => false, "message" => "Acesso negado."]);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"));
        $id_termo = (int) $data->id_termo;
        $status = $conn->real_escape_string($data->status_termo);
        $id_moderador = (int) $_SESSION['id_usuario'];

        $sql = "UPDATE termos 
                SET termos.status_termo = '$status', 
                    termos.id_moderador = $id_moderador, 
                    termos.data_aprovacao = NOW() 
                WHERE termos.id_termo = $id_termo";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Termo atualizado."]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        break;

    case "DELETE":
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(["success" => false, "message" => "Acesso negado."]);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"));
        $id_termo = (int) $data->id_termo;
        $sql = "DELETE FROM termos WHERE termos.id_termo = $id_termo";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["success" => true, "message" => "Termo excluído."]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método inválido."]);
        break;
}