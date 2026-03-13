<?php
session_start();
require_once '../config/db.php'; 
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

// Captura de parâmetros via GET
$categoria = isset($_GET['cat']) ? $conn->real_escape_string($_GET['cat']) : '';
$status_filtro = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : 'aprovado';

switch ($method) {
    case "GET":
        // 1. ROTA PARA CONTADORES DO PAINEL ADMIN
        if (isset($_GET['contar_status'])) {
            $sql = "SELECT 
                        SUM(CASE WHEN status_termo = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                        SUM(CASE WHEN status_termo = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
                        SUM(CASE WHEN status_termo = 'reprovado' THEN 1 ELSE 0 END) as rejeitados
                    FROM termos";
            
            $result = $conn->query($sql);
            $contagens = $result->fetch_assoc();
            
            echo json_encode([
                "success" => true, 
                "data" => [
                    "pendente" => (int)$contagens['pendentes'],
                    "aprovado" => (int)$contagens['aprovados'],
                    "rejeitado" => (int)$contagens['rejeitados']
                ]
            ]);
            exit;
        }

        // 2. LISTAGEM DE TERMOS (Com filtros de Categoria e Status)
        $where = "WHERE 1=1";
        
        if ($status_filtro !== '') {
            $where .= " AND status_termo = '$status_filtro'";
        }

        if ($categoria !== 'todos' && $categoria !== '') {
            $where .= " AND cat_termo = '$categoria'";
        }

        $sql = "SELECT termos.*, turmas.nome_turma 
                FROM termos 
                LEFT JOIN turmas ON termos.turmas_id_turma = turmas.id_turma 
                $where 
                ORDER BY termos.data_criacao DESC";

        $result = $conn->query($sql);
        $termos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) { $termos[] = $row; }
        }
        
        echo json_encode(["success" => true, "data" => $termos]);
        break;

    case "POST":
        // CRIAÇÃO DE TERMO
        $nome_termo = $conn->real_escape_string($_POST['nome_termo']);
        $descricao = $conn->real_escape_string($_POST['descricao_termo']);
        $exemplo = isset($_POST['exemplo_termo']) ? $conn->real_escape_string($_POST['exemplo_termo']) : '';
        $categoria_post = $conn->real_escape_string($_POST['cat_termo']); 
        $nome_aluno = $conn->real_escape_string($_POST['nome_aluno']);
        $id_turma = (int) $_POST['turmas_id_turma'];

        $sql = "INSERT INTO termos (nome_termo, descricao_termo, exemplo_termo, cat_termo, nome_aluno, turmas_id_turma, status_termo) 
                VALUES ('$nome_termo', '$descricao', '$exemplo', '$categoria_post', '$nome_aluno', $id_turma, 'pendente')";

        if ($conn->query($sql)) {
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
        $id_moderador = (int) $_SESSION['id_usuario'];

        // AÇÃO: Mudar Status (Aprovar/Rejeitar)
        if (isset($data->acao) && $data->acao === 'mudar_status') {
            $status = $conn->real_escape_string($data->status_termo);
            $sql = "UPDATE termos SET status_termo = '$status', id_moderador = $id_moderador, data_aprovacao = NOW() WHERE id_termo = $id_termo";
        } 
        // AÇÃO: Editar Conteúdo
        else {
            $titulo = $conn->real_escape_string($data->nome_termo);
            $desc = $conn->real_escape_string($data->descricao_termo);
            $ex = $conn->real_escape_string($data->exemplo_termo);
            $sql = "UPDATE termos SET nome_termo = '$titulo', descricao_termo = '$desc', exemplo_termo = '$ex' WHERE id_termo = $id_termo";
        }

        if ($conn->query($sql)) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "message" => $conn->error]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não suportado."]);
        break;
}