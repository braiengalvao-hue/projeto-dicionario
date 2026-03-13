<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor - Sistema de Dicionário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .custom_select {
    width: 100%;
    padding: 12px 16px 12px 48px;
    border: 1px solid var(--border_gray);
    border-radius: 12px;
    background-color: #F9FAFB;
    font-size: 14px;
    outline: none;
    appearance: none; /* Remove a seta padrão */
    cursor: pointer;
}

.custom_select:focus {
    border-color: var(--primary_blue);
    background-color: var(--white);
}

/* Adicionando uma setinha customizada para o select */
.input_group .input_wrapper::after {
    content: '\f107';
    font-family: 'Font Awesome 6 Free';
    font-weight: 900;
    position: absolute;
    right: 16px;
    color: var(--text_muted);
    pointer-events: none;
}
    </style>
</head>
<body class="bg_admin">

    <header class="header_login">
        <a href="login.php" class="btn_back_login">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao login
        </a>
    </header>

    <main class="login_container_flex">
        <div class="login_card">
            <div class="icon_circle_login">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            
            <h1 class="login_title">Novo Professor</h1>
            <p class="login_subtitle">Crie uma conta para moderar termos</p>

            <form class="login_form" id="registerForm">
                <div class="input_group">
                    <label for="nome_professor">Nome Completo</label>
                    <div class="input_wrapper">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" id="nome_professor" placeholder="Ex: Ricardo Souza" required>
                    </div>
                </div>

                <div class="input_group">
                    <label for="email">E-mail de Acesso</label>
                    <div class="input_wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" placeholder="seu.email@senai.br" required>
                    </div>
                </div>

                <div class="input_group">
                    <label for="especializacao">Disciplina de Especialização</label>
                    <div class="input_wrapper">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <select id="especializacao" class="custom_select" required>
                            <option value="" disabled selected>Selecione a matéria...</option>
                            <option value="port">Português</option>
                            <option value="mat">Matemática</option>
                        </select>
                    </div>
                </div>

                <div class="input_group">
                    <label for="password">Defina uma Senha</label>
                    <div class="input_wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" placeholder="********" required>
                    </div>
                </div>

                <button type="submit" class="btn_login" id="btnRegister">Criar Conta</button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('btnRegister');
            const nome = document.getElementById('nome_professor').value;
            const login = document.getElementById('email').value;
            const espec = document.getElementById('especializacao').value;
            const senha = document.getElementById('password').value;

            btn.innerText = "Cadastrando...";
            btn.disabled = true;

            fetch('api/autch.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    nome_professor: nome,
                    login_usuario: login,
                    especializacao_professor: espec,
                    senha_usuario: senha
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Cadastro realizado com sucesso! Agora você pode fazer login.");
                    window.location.href = 'login.php';
                } else {
                    alert("Erro: " + data.message);
                    btn.innerText = "Criar Conta";
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert("Erro ao conectar com o servidor.");
                btn.innerText = "Criar Conta";
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>