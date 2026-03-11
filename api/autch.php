<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

$method = $_SERVER["REQUEST_METHOD"];
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($method) {
    case "POST":
        $data = json_decode(file_get_contents("php://input"));

        // --- ROTA DE CADASTRO ---
        if ($action === 'register') {
            if (!isset($data->login_usuario) || !isset($data->senha_usuario) || !isset($data->nome_professor) || !isset($data->especializacao_professor)) {
                echo json_encode(["success" => false, "message" => "Dados incompletos para cadastro."]);
                exit;
            }

            $login = $conn->real_escape_string($data->login_usuario);
            $nome = $conn->real_escape_string($data->nome_professor);
            $especializacao = $conn->real_escape_string($data->especializacao_professor); 
            $senha_hash = password_hash($data->senha_usuario, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (usuarios.login_usuario, usuarios.senha_usuario, usuarios.nome_professor, usuarios.especializacao_professor) 
                    VALUES ('$login', '$senha_hash', '$nome', '$especializacao')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "message" => "Usuário cadastrado com sucesso!"]);
            } else {
                echo json_encode(["success" => false, "message" => "Erro ao cadastrar: " . $conn->error]);
            }
        } 
        
        // --- ROTA DE LOGIN ---
        else if ($action === 'login') {
            if (!isset($data->login_usuario) || !isset($data->senha_usuario)) {
                echo json_encode(["success" => false, "message" => "Informe login e senha."]);
                exit;
            }

            $login = $conn->real_escape_string($data->login_usuario);

            $sql = "SELECT usuarios.id_usuario, usuarios.login_usuario, usuarios.senha_usuario, usuarios.nome_professor, usuarios.especializacao_professor 
                    FROM usuarios 
                    WHERE usuarios.login_usuario = '$login'";
            
            $result = $conn->query($sql);

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                
                if (password_verify($data->senha_usuario, $user['senha_usuario'])) {
                    $_SESSION['id_usuario'] = $user['id_usuario'];
                    $_SESSION['nome_professor'] = $user['nome_professor'];
                    $_SESSION['especializacao'] = $user['especializacao_professor'];

                    echo json_encode([
                        "success" => true, 
                        "message" => "Login realizado!",
                        "user" => [
                            "nome" => $user['nome_professor'],
                            "especializacao" => $user['especializacao_professor']
                        ]
                    ]);
                } else {
                    echo json_encode(["success" => false, "message" => "Senha incorreta."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "Usuário não encontrado."]);
            }
        }
        break;

    default:
        echo json_encode(["success" => false, "message" => "Método ou ação inválida."]);
        break;
}