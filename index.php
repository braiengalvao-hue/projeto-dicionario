<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário Técnico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <header class="navbar_container">
        <div class="logo_group">
            <h1 class="logo_title">Dicionário Técnico</h1>
            <p class="logo_subtitle">Sistema de Consulta Técnica</p>
        </div>
        <a href="login.php" class="login_professor">
            <i class="fa-solid fa-lock"></i> Entrar como Professor
        </a>
    </header>

    <main class="main_content">
        <h2 class="welcome_title">Bem-vindo ao Dicionário Técnico</h2>
        <p class="instruction_text">Selecione uma disciplina para começar</p>

        <div class="cards_wrapper">
            <div class="card_item" id="port">
                <div class="icon_circle">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="card_title">Dicionário de Português</h3>
                <p class="card_description">Consulte termos de gramática, sintaxe e linguística</p>
                <a href="portugues.php" class="btn_access">Acessar <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="card_item" id="mat">
                <div class="icon_circle">
                    <i class="fa-solid fa-calculator"></i>
                </div>
                <h3 class="card_title">Dicionário de Matemática</h3>
                <p class="card_description">Explore conceitos de álgebra, geometria e cálculo</p>
                <a href="matematica.php" class="btn_access">Acessar <i class="fa-solid fa-arrow-right"></i></a>
            </div>
        </div>
    </main>

    <?php require_once 'assets/layout/bnt_dark.php' ?>

    <footer>
        <p class="footer_hint">Selecione uma disciplina para começar sua consulta</p>
    </footer>

    <script src="./assets/js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const port = document.getElementById('port');
            const mat = document.getElementById('mat');

            port.addEventListener('click', () => {
                window.location.href = 'portugues.php';
            });

            mat.addEventListener('click', () => {
                window.location.href = 'matematica.php';
            });
        });
    </script>
</body>
</html>