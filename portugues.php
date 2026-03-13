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

    <div class="fab_container">
        <a class="fab_button" href="adicionar_termo.php">
            <i class="material-icons"></i>
        </a>
    </div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const listContainer = document.querySelector('.list_container');
    const searchInput = document.querySelector('.search_box input');
    const termCountText = document.querySelector('.title_group span');

    // Função para carregar os termos filtrados por Português e Aprovados
    function carregarTermos() {
        fetch('api/termos.php?cat=port')
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
            // Define o caminho da imagem: se existir no banco usa uploads, senão usa padrão
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

    // Filtro de busca em tempo real
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