<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dicionário de Português</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
</head>
<body>

    <header class="sticky_header">
        <div class="header_left">
            <a href="index.php" class="btn_back"><i class="fa-solid fa-arrow-left"></i></a>
            <div class="icon_circle_small">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <div class="title_group">
                <h1 style="font-size: 20px;">Dicionário de Português</h1>
                <span style="font-size: 13px; color: var(--text_muted);">5 termos disponíveis</span>
            </div>
        </div>
        
        <div class="search_box">
            <i class="fa-solid fa-magnifying-glass" style="color: var(--text_muted);"></i>
            <input type="text" placeholder="Buscar termo...">
        </div>
    </header>

    <main style="padding: 40px 10%;">
        <div class="term_card">
            <img src="https://via.placeholder.com/100x70" class="term_image">
            <div class="term_info">
                <h3>Coesão</h3>
                <p>Propriedade que garante a conexão entre as partes de um texto, tornando-o um todo unificado e compreensível.</p>
            </div>
            <i class="fa-solid fa-arrow-right" style="color: var(--border_gray); font-size: 12px;"></i>
        </div>

        <div class="term_card">
            <img src="https://via.placeholder.com/100x70" class="term_image">
            <div class="term_info">
                <h3>Predicado</h3>
                <p>Tudo aquilo que se declara sobre o sujeito da oração, contendo obrigatoriamente um verbo ou locução verbal.</p>
            </div>
            <a href="termos.php"><i class="fa-solid fa-arrow-right" style="color: var(--border_gray); font-size: 12px;"></i>
</a>
        </div>
    </main>

    <button class="fab_button">
        <i class="fa-solid fa-plus"></i>
    </button>

</body>
</html>