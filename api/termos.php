<?php
session_start();
require_once '../config/db.php'; 

// Habilite para debug se necessário, mas mantenha 0 em produção
error_reporting(0); 
ini_set('display_errors', 0);
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];

// Captura de parâmetros via GET
$categoria = (isset($_GET['cat']) && $_GET['cat'] !== '') ? $conn->real_escape_string($_GET['cat']) : 'todos';
$status_filtro = (isset($_GET['status']) && $_GET['status'] !== '') ? $conn->real_escape_string($_GET['status']) : 'pendente';

switch ($method) {
    case "GET":
        // 1. BUSCA POR ID (Página de Detalhes)
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $sql = "SELECT termos.*, turmas.nome_turma 
                    FROM termos 
                    LEFT JOIN turmas ON termos.turmas_id_turma = turmas.id_turma 
                    WHERE id_termo = $id";
            
            $result = $conn->query($sql);
            $termo = $result->fetch_assoc();

            if ($termo) {
                echo json_encode(["success" => true, "data" => $termo]);
            } else {
                echo json_encode(["success" => false, "message" => "Termo não encontrado"]);
            }
            exit;
        }

        // 2. ROTA PARA CONTADORES
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
                    "reprovado" => (int)$contagens['reprovados']
                ]
            ]);
            exit;
        }

        // 3. LISTAGEM GERAL
        $where = "WHERE 1=1";
        if ($status_filtro !== '') $where .= " AND status_termo = '$status_filtro'";
        if ($categoria !== 'todos' && $categoria !== '') $where .= " AND cat_termo = '$categoria'";

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
        // 1. Captura de dados de texto
        $nome_termo = $conn->real_escape_string($_POST['nome_termo'] ?? '');
        $descricao = $conn->real_escape_string($_POST['descricao_termo'] ?? '');
        $exemplo = $conn->real_escape_string($_POST['exemplo_termo'] ?? '');
        $categoria_post = $conn->real_escape_string($_POST['cat_termo'] ?? 'port'); 
        
        // Lógica para Autor e Turma
        if(isset($_SESSION['id_usuario'])) {
            $nome_autor = $_POST['nome_professor'] ?? 'Professor'; 
            $id_turma = 11; 
        } else {
            $nome_autor = $conn->real_escape_string($_POST['nome_aluno'] ?? 'Anônimo');
            $id_turma = (int)($_POST['turmas_id_turma'] ?? 0);
        }

        // 2. LÓGICA DE UPLOAD DE IMAGEM
        $nome_imagem_final = null; // Valor padrão se não houver imagem

        if (isset($_FILES['foto_termo']) && $_FILES['foto_termo']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto_termo'];
            $extensao = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $extensoes_permitidas = ['jpg', 'jpeg', 'png', 'webp'];

            // Valida extensão
            if (in_array($extensao, $extensoes_permitidas)) {
                // Gera um nome único: ex: 65f2a1b3c4d5e.png
                $novo_nome = uniqid() . "." . $extensao;
                $destino = "../assets/uploads/" . $novo_nome;

                // Move o arquivo da pasta temporária para o destino final
                if (move_uploaded_file($file['tmp_name'], $destino)) {
                    $nome_imagem_final = $novo_nome;
                }
            }
        }

        // 3. SQL atualizado com a coluna foto_termo
        $sql = "INSERT INTO termos (nome_termo, descricao_termo, exemplo_termo, cat_termo, nome_aluno, turmas_id_turma, status_termo, foto_termo) 
                VALUES ('$nome_termo', '$descricao', '$exemplo', '$categoria_post', '$nome_autor', $id_turma, 'pendente', " . 
                ($nome_imagem_final ? "'$nome_imagem_final'" : "NULL") . ")";

        if ($conn->query($sql)) {
            echo json_encode(["success" => true, "id_termo" => $conn->insert_id]);
        } else {
            echo json_encode(["success" => false, "message" => "Erro no banco: " . $conn->error]);
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

        if (isset($data->acao) && $data->acao === 'mudar_status') {
            $status = $conn->real_escape_string($data->status_termo);
            $sql = "UPDATE termos SET status_termo = '$status', id_moderador = $id_moderador, data_aprovacao = NOW() WHERE id_termo = $id_termo";
        } else {
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
        // 1. Verifica se o usuário está logado (apenas moderadores podem excluir/reprovar)
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(["success" => false, "message" => "Acesso negado."]);
            exit;
        }

        // 2. Captura o corpo da requisição JSON
        $data = json_decode(file_get_contents("php://input"));

        // 3. Valida se o ID do termo foi enviado
        if (isset($data->id_termo)) {
            $id_termo = (int) $data->id_termo;
            
            // 4. Executa a exclusão lógica mudando o status
            $sql = "UPDATE termos SET status_termo = 'reprovado' WHERE id_termo = $id_termo";
    
            if ($conn->query($sql)) {
                echo json_encode(["success" => true, "message" => "Termo reprovado com sucesso."]);
            } else {
                echo json_encode(["success" => false, "message" => "Erro ao atualizar: " . $conn->error]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "ID do termo não fornecido."]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Método não suportado."]);
        break;
}