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

    <header class="navbar_container">
        <div class="header_left_details">
            <a href="index.php" id="btn_voltar" class="btn_back">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="header_title">
                <div class="badge_category">Carregando...</div>
                <h1 class="term_main_title">---</h1>
            </div>
        </div>
    </header>

    <main class="details_content">
        <div class="featured_image_container">
            <img src="assets/images/icon_not_faund.svg" alt="Carregando" class="featured_image">
        </div>

        <section class="info_section">
            <div class="section_title_wrapper">
                <span class="blue_indicator"></span>
                <h2>Descrição</h2>
            </div>
            <div class="info_card">
                <p>Buscando informações...</p>
            </div>
        </section>

        <section class="info_section">
            <div class="section_title_wrapper">
                <span class="blue_indicator"></span>
                <h2>Exemplo Prático</h2>
            </div>
            <div class="info_card bg_italic">
                <p>Buscando exemplo...</p>
            </div>
        </section>

        <footer class="collab_footer">
            <div class="collab_icon">
                <i class="fa-regular fa-user"></i>
            </div>
            <div class="collab_info">
                <p class="collab_name">Colaboração de: <strong>---</strong></p>
                <p class="collab_class">Turma: ---</p>
            </div>
        </footer>
    </main>

<div id="imageModal" class="modal">
    <span class="close_modal">&times;</span>
    <img class="modal_content" id="img01">
    <div id="caption"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const termoId = params.get('id');

    if (!termoId) {
        window.location.href = 'index.php';
        return;
    }

    // Seleção dos elementos do HTML
    const termTitle = document.querySelector('.term_main_title');
    const badge = document.querySelector('.badge_category');
    const btnVoltar = document.getElementById('btn_voltar');
    const imgElement = document.querySelector('.featured_image');
    const infoCards = document.querySelectorAll('.info_card p');
    const collabName = document.querySelector('.collab_name strong');
    const collabClass = document.querySelector('.collab_class');

    // --- BUSCA DOS DADOS DO TERMO ---
    fetch(`api/termos.php?id=${termoId}`)
    .then(response => {
        if (!response.ok) throw new Error('Erro na rede');
        return response.json();
    })
    .then(res => {
        console.log("Resposta da API:", res); // Debug para você ver no F12

        if (res.success && res.data) {
            // O segredo está aqui: acessamos res.data, não res.success
            const termo = res.data;

            // 1. Título
            document.title = `${termo.nome_termo} - Dicionário Técnico`;
            termTitle.innerText = termo.nome_termo;

            // 2. Categoria e Link Voltar
            if (termo.cat_termo === 'port') {
                badge.innerText = 'Português';
                badge.classList.add('port'); // Certifique-se de ter essa cor no CSS
                btnVoltar.href = 'portugues.php';
            } else if (termo.cat_termo === 'mat') {
                badge.innerText = 'Matemática';
                badge.classList.add('math');
                btnVoltar.href = 'matematica.php';
            } else {
                badge.innerText = 'Geral';
                btnVoltar.href = 'index.php';
            }

            // 3. Imagem
            if (termo.foto_termo) {
                imgElement.src = `assets/uploads/${termo.foto_termo}`;
            } else {
                // Imagem padrão caso não tenha foto
                imgElement.src = termo.cat_termo === 'port' ? 'assets/images/port_default.png' : 'assets/images/mat_default.png';
            }
            imgElement.alt = termo.nome_termo;

            // 4. Descrição e Exemplo
            // infoCards[0] é a Descrição, infoCards[1] é o Exemplo
            if (infoCards[0]) infoCards[0].innerText = termo.descricao_termo;
            if (infoCards[1]) infoCards[1].innerText = termo.exemplo_termo || "Nenhum exemplo prático fornecido.";

            // 5. Rodapé
            if (collabName) collabName.innerText = termo.nome_aluno || 'Autor Desconhecido';
            if (collabClass) collabClass.innerText = `Turma: ${termo.nome_turma || 'N/I'}`;

        } else {
            alert('Termo não encontrado.');
            window.location.href = 'index.php';
        }
    })
    .catch(error => {
        console.error('Erro detalhado:', error);
        termTitle.innerText = "Erro ao carregar";
    });

    // --- LÓGICA DO MODAL DE IMAGEM ---
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("img01");
    const captionText = document.getElementById("caption");
    const closeBtn = document.querySelector(".close_modal");

    if (imgElement) {
        imgElement.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        };
    }

    if (closeBtn) {
        closeBtn.onclick = () => modal.style.display = "none";
    }

    window.onclick = (event) => {
        if (event.target == modal) modal.style.display = "none";
    };
});
</script>
</body>
</html>