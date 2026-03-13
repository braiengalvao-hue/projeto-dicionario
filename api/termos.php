<?php
session_start();
require_once '../config/db.php'; 

error_reporting(0); 
ini_set('display_errors', 0);
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

// Captura de parâmetros via GET
// Altere estas linhas no início do termos.php
$categoria = (isset($_GET['cat']) && $_GET['cat'] !== '') ? $conn->real_escape_string($_GET['cat']) : 'todos';
$status_filtro = (isset($_GET['status']) && $_GET['status'] !== '') ? $conn->real_escape_string($_GET['status']) : 'pendente';

switch ($method) {
    case "GET":
        
        // 1. ROTA PARA CONTADORES DO PAINEL ADMIN
// 1. ROTA PARA CONTADORES DO PAINEL ADMIN
    if (isset($_GET['contar_status'])) {
        $sql = "SELECT 
                    SUM(CASE WHEN status_termo = 'pendente' THEN 1 ELSE 0 END) as pendentes,
                    SUM(CASE WHEN status_termo = 'aprovado' THEN 1 ELSE 0 END) as aprovados,
                    SUM(CASE WHEN status_termo = 'reprovado' THEN 1 ELSE 0 END) as reprovados
                FROM termos";
        
        $result = $conn->query($sql);
        $contagens = $result->fetch_assoc();
        
        echo json_encode([
            "success" => true, 
            "data" => [
                "pendente" => (int)$contagens['pendentes'],
                "aprovado" => (int)$contagens['aprovados'],
                "reprovado" => (int)$contagens['reprovados'] // Alterado de rejeitado para reprovado
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

                if(isset($_SESSION['id_usuario'])) {
            $nome_termo = $_POST['nome_termo'] ?? '';
            $descricao = $_POST['descricao_termo'] ?? '';
            $exemplo = $_POST['exemplo_termo'] ?? '';
            $categoria = $_POST['cat_termo'] ?? 'port';
            
            // Se você removeu do HTML, defina valores padrão ou aceite NULL
            $nome_aluno = $_POST['nome_professor'] ?? 'Anônimo'; 
            $_POST['turmas_id_turma'] = 11; 
            $id_turma = isset($_POST['turmas_id_turma']) ? (int)$_POST['turmas_id_turma'] : 0;

            // Verifique se o SQL não vai falhar por falta de colunas obrigatórias
            $sql = "INSERT INTO termos (nome_termo, descricao_termo, exemplo_termo, cat_termo, nome_aluno, turmas_id_turma, status_termo) 
                    VALUES ('$nome_termo', '$descricao', '$exemplo', '$categoria', '$nome_aluno', $id_turma, 'pendente')";

            if ($conn->query($sql)) {
                echo json_encode(["success" => true]);
            } else {
                // Isso garante que o JS receba um JSON mesmo em erro de banco
                echo json_encode(["success" => false, "message" => $conn->error]);
            }
            exit;
        }


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
    
        case 'PATCH':
            // EXCLUSÃO DE TERMO (Mudar status para 'reprovado')
            $data = json_decode(file_get_contents("php://input"));
            $id_termo = (int) $data->id_termo;
            $sql = "UPDATE termos SET status_termo = 'reprovado' WHERE id_termo = $id_termo";
    
            if ($conn->query($sql)) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => $conn->error]);
            }
            break;  
    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "$method Método não suportado."]);
        break;
}