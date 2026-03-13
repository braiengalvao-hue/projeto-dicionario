<?php
session_start();
require_once '../config/db.php'; 
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

$categoria = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';

$status_filtro = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'aprovado';

switch ($method) {
    case "GET":
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sql = "SELECT termos.*, turmas.nome_turma 
                    FROM termos 
                    INNER JOIN turmas ON termos.turmas_id_turma = turmas.id_turma 
                    WHERE termos.id_termo = $id";
            
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                echo json_encode(["success" => true, "data" => $result->fetch_assoc()]);
            } else {
                echo json_encode(["success" => false, "message" => "Termo não encontrado."]);
            }
            exit;
        }

        if (isset($_GET['listar_turmas'])) {
            $sql = "SELECT id_turma, nome_turma FROM turmas ORDER BY nome_turma ASC";
            $result = $conn->query($sql);
            $turmas = [];
            while ($row = $result->fetch_assoc()) { $turmas[] = $row; }
            echo json_encode(["success" => true, "data" => $turmas]);
            exit;
        }

        // --- VALIDAÇÃO DA MATÉRIA ---
        if ($categoria === '') {
            echo json_encode(["success" => false, "message" => "A categoria (cat) é obrigatória para listar os termos."]);
            exit;
        }

        // --- SQL DE LISTAGEM FILTRADA ---
        $sql = "SELECT 
                    termos.id_termo, termos.nome_termo, termos.descricao_termo, 
                    termos.cat_termo, termos.foto_termo, termos.status_termo, 
                    termos.nome_aluno, turmas.nome_turma 
                FROM termos 
                INNER JOIN turmas ON termos.turmas_id_turma = turmas.id_turma 
                WHERE termos.cat_termo = '$categoria' 
                AND termos.status_termo = '$status_filtro' 
                ORDER BY termos.nome_termo ASC";

        $result = $conn->query($sql);
        $termos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) { $termos[] = $row; }
        }

        echo json_encode(["success" => true, "data" => $termos]);
        break;

        

        case "POST":
        if (!isset($_POST['nome_termo']) || !isset($_POST['descricao_termo']) || !isset($_POST['turmas_id_turma'])) {
            echo json_encode(["success" => false, "message" => "Dados incompletos no servidor."]);
            exit;
        }

        $nome_termo = $conn->real_escape_string($_POST['nome_termo']);
        $descricao = $conn->real_escape_string($_POST['descricao_termo']);
        $exemplo = isset($_POST['exemplo_termo']) ? $conn->real_escape_string($_POST['exemplo_termo']) : '';
        $categoria_post = $conn->real_escape_string($_POST['cat_termo']); 
        $nome_aluno = $conn->real_escape_string($_POST['nome_aluno']);
        $id_turma = (int) $_POST['turmas_id_turma'];

        $foto_nome = null;
        if (isset($_FILES['foto_termo']) && $_FILES['foto_termo']['error'] === 0) {
            $diretorio = "../assets/uploads/";
            
            if (!file_exists($diretorio)) {
                mkdir($diretorio, 0777, true);
            }

            $extensao = pathinfo($_FILES['foto_termo']['name'], PATHINFO_EXTENSION);
            $foto_nome = uniqid() . "." . $extensao;
            
            move_uploaded_file($_FILES['foto_termo']['tmp_name'], $diretorio . $foto_nome);
        }

        $sql = "INSERT INTO termos (nome_termo, descricao_termo, exemplo_termo, cat_termo, nome_aluno, turmas_id_turma, foto_termo, status_termo) 
                VALUES ('$nome_termo', '$descricao', '$exemplo', '$categoria_post', '$nome_aluno', $id_turma, " . ($foto_nome ? "'$foto_nome'" : "NULL") . ", 'pendente')";

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