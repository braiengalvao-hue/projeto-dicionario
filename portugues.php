<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicionário de Português - SENAI</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <header class="sticky_header">
        <div class="header_top">
            <div class="header_left">
                <a href="index.php" class="btn_back"><i class="fa-solid fa-arrow-left"></i></a>
                <div class="icon_circle_small">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <div class="title_group">
                    <h1>Dicionário de Português</h1>
                    <span id="term-count">Carregando termos...</span>
                </div>
            </div>
        </div>
        
        <div class="search_container">
            <div class="search_box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" id="search-input" placeholder="Buscar termo...">
            </div>
        </div>
    </header>

    <main class="scroll_content">
        <div class="list_container">
            <p style="padding:20px; text-align:center;">Buscando termos...</p>
        </div>
    </main>

    <?php require_once 'assets/layout/bnt_add_dark.php' ?>
    
    <script src="./assets/js/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const listContainer = document.querySelector('.list_container');
        const searchInput = document.getElementById('search-input');
        const termCountText = document.getElementById('term-count');

        function carregarTermos() {
            fetch('api/termos.php?cat=port&status=aprovado') 
                .then(response => response.json())
                .then(resultado => {
                    if (resultado.success && resultado.data.length > 0) {
                        renderizarLista(resultado.data);
                        termCountText.innerText = `${resultado.data.length} termos disponíveis`;
                    } else {
                        listContainer.innerHTML = '<p style="padding:20px; text-align:center;">Nenhum termo aprovado encontrado.</p>';
                        termCountText.innerText = `0 termos disponíveis`;
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    listContainer.innerHTML = '<p style="padding:20px; text-align:center;">Erro na ligação ao servidor.</p>';
                });
        }

        function renderizarLista(termos) {
            listContainer.innerHTML = ''; 

            termos.forEach(termo => {
                const imagem = termo.foto_termo 
                    ? `assets/uploads/${termo.foto_termo}` 
                    : 'assets/images/port.png';

                const cardHTML = `
                    <div class="term_card" 
                         data-nome="${termo.nome_termo.toLowerCase()}" 
                         onclick="window.location.href='termos.php?id=${termo.id_termo}'" 
                         style="cursor: pointer;">
                        
                        <img src="${imagem}" alt="${termo.nome_termo}" class="term_image">
                        
                        <div class="term_info">
                            <h3>${termo.nome_termo}</h3>
                            <p>${termo.descricao_termo}</p>
                        </div>
                        
                        <i class="fa-solid fa-arrow-right arrow_icon"></i>
                    </div>
                `;
                listContainer.insertAdjacentHTML('beforeend', cardHTML);
            });
        }

        searchInput.addEventListener('input', function() {
            const busca = this.value.toLowerCase();
            const cards = document.querySelectorAll('.term_card');
            
            cards.forEach(card => {
                const nome = card.getAttribute('data-nome');
                card.style.display = nome.includes(busca) ? 'flex' : 'none';
            });
        });

        carregarTermos();
    });
    </script>
</body>
</html>