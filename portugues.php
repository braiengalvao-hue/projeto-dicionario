<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário de Português - SENAI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="sticky_header">
        <div class="header_top">
            <div class="header_left">
                <a href="index.php" class="btn_back"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="icon_circle_small">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="title_group">
                    <h1>Dicionário de Português</h1>
                    <span>5 termos disponíveis</span>
                </div>
            </div>
        </div>
        
        <div class="search_container">
            <div class="search_box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Buscar termo...">
            </div>
        </div>
    </header>

    <main class="scroll_content">
        <div class="list_container">
            
            <div class="term_card">
                <img src="assets/images/icon_not_faund.svg" alt="Icone de imagem" class="term_image">
                <div class="term_info">
                    <h3>Coesão</h3>
                    <p>Propriedade que garante a conexão entre as partes de um texto, tornando-o um todo unificado e compreensível.</p>
                </div>
                <a href="termos.php"><i class="fa-solid fa-arrow-right arrow_icon"></i>
</a>
            </div>

            <div class="term_card">
                <img src="assets/images/icon_not_faund.svg" alt="Icone de imagem" class="term_image">
                <div class="term_info">
                    <h3>Predicado</h3>
                    <p>Tudo aquilo que se declara sobre o sujeito da oração, contendo obrigatoriamente um verbo ou locução verbal.</p>
                </div>
                <i class="fa-solid fa-arrow-right arrow_icon"></i>
            </div>

            </div>
    </main>

    <button class="fab_button">
        <i class="fa-solid fa-plus"></i>
    </button>

</body>
</html>