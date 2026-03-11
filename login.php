<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Área do Administrador</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg_admin">

    <header class="header_login">
        <a href="index.php" class="btn_back_login">
            <i class="fa-solid fa-arrow-left"></i> Voltar ao início
        </a>
    </header>

    <main class="login_container_flex">
        <div class="login_card">
            <div class="icon_circle_login">
                <i class="fa-solid fa-lock"></i>
            </div>
            
            <h1 class="login_title">Administrador</h1>
            <p class="login_subtitle">Acesso restrito para professores</p>

            <form class="login_form">
                <div class="input_group">
                    <label for="email">E-mail</label>
                    <div class="input_wrapper">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" id="email" placeholder="seu.email@senai.br">
                    </div>
                </div>

                <div class="input_group">
                    <label for="password">Senha</label>
                    <div class="input_wrapper">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" id="password" placeholder="********">
                        <i class="fa-regular fa-eye eye_icon"></i>
                    </div>
                </div>

                <button type="submit" class="btn_login">Entrar</button>
                            </form>
        </div>
    </main>

</body>
</html>