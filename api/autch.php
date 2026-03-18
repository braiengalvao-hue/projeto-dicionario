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
            // 1. Verifique se os nomes batem exatamente com o JSON enviado pelo JS
            if (!isset($data->login_usuario) || !isset($data->senha_usuario) || !isset($data->nome_professor)) {
                echo json_encode(["success" => false, "message" => "Dados incompletos."]);
                exit;
            }

            $login = $conn->real_escape_string($data->login_usuario);
            $nome = $conn->real_escape_string($data->nome_professor);

            // Use o operador de coalescência (??) para evitar o erro de 'property not found'
            $especializacao = isset($data->especializacao_professor) ? $conn->real_escape_string($data->especializacao_professor) : '';

            $senha_hash = password_hash($data->senha_usuario, PASSWORD_DEFAULT);

            // 2. Verificação de login existente
            $sql_check = "SELECT id_usuario FROM usuarios WHERE login_usuario = '$login'";
            $result_check = $conn->query($sql_check);

            if ($result_check && $result_check->num_rows > 0) {
                echo json_encode(["success" => false, "message" => "Este e-mail/login já está cadastrado."]);
                exit;
            }

            // 3. INSERT (Removido o prefixo 'usuarios.' de dentro dos parênteses)
            $sql = "INSERT INTO usuarios (login_usuario, senha_usuario, nome_professor, especializacao_professor) 
            VALUES ('$login', '$senha_hash', '$nome', '$especializacao')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(["success" => true, "message" => "Usuário cadastrado com sucesso!"]);
            } else {
                // Se der erro aqui, o JSON ainda será válido
                echo json_encode(["success" => false, "message" => "Erro no banco: " . $conn->error]);
            }
            exit; // Importante para não executar nada abaixo
        }
        // --- ROTA DE LOGIN ---
        else if ($action === 'login') {
            if (!isset($data->login_usuario) || !isset($data->senha_usuario)) {
                echo json_encode(["success" => false, "message" => "Informe login e senha."]);
                exit;
            }

            $login = $conn->real_escape_string($data->login_usuario);

            // Buscamos o usuário pelo login
            $sql = "SELECT id_usuario, login_usuario, senha_usuario, nome_professor, especializacao_professor 
            FROM usuarios 
            WHERE login_usuario = '$login'";

            $result = $conn->query($sql);

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();

                // CORREÇÃO AQUI: Usar password_verify para comparar a senha digitada com o hash do banco
                if (password_verify($data->senha_usuario, $user['senha_usuario'])) {

                    // Inicia a sessão se ainda não foi iniciada (verifique se já deu session_start() no topo do arquivo)
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

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
                    // Senha não confere com o hash
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
