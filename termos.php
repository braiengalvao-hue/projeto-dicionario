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
            <img src="assets/images/icon_not_found.svg" alt="Carregando" class="featured_image">
        </div>

        <section class="info_section">
            <div class="section_title_wrapper">
                <span class="blue_indicator"></span>
                <h2>Descrição</h2>
            </div>
            <div class="info_card">
                <p id="desc-text">Buscando informações...</p>
            </div>
        </section>

        <section class="info_section">
            <div class="section_title_wrapper">
                <span class="blue_indicator"></span>
                <h2>Exemplo Prático</h2>
            </div>
            <div class="info_card bg_italic">
                <p id="ex-text">Buscando exemplo...</p>
            </div>
        </section>

        <footer class="collab_footer">
            <div class="collab_icon">
                <i class="fa-regular fa-user"></i>
            </div>
            <div class="collab_info">
                <p class="collab_name">Colaboração de: <strong id="student-name">---</strong></p>
                <p class="collab_class" id="student-class">Turma: ---</p>
            </div>
        </footer>
    </main>

    <?php require_once 'assets/layout/bnt_add_dark.php' ?>

    <div id="imageModal" class="modal">
        <span class="close_modal">&times;</span>
        <img class="modal_content" id="img01">
        <div id="caption"></div>
    </div>



    <script src="./assets/js/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const termoId = params.get('id');

        if (!termoId) {
            window.location.href = 'index.php';
            return;
        }

        // Seleção dos elementos
        const termTitle = document.querySelector('.term_main_title');
        const badge = document.querySelector('.badge_category');
        const btnVoltar = document.getElementById('btn_voltar');
        const imgElement = document.querySelector('.featured_image');
        const descText = document.getElementById('desc-text');
        const exText = document.getElementById('ex-text');
        const collabName = document.getElementById('student-name');
        const collabClass = document.getElementById('student-class');

        // Busca dos dados na API
        fetch(`api/termos.php?id=${termoId}`)
            .then(response => response.json())
            .then(res => {
                if (res.success && res.data) {
                    const termo = res.data;

                    // Título e Meta Tag
                    document.title = `${termo.nome_termo} - Dicionário Técnico`;
                    termTitle.innerText = termo.nome_termo;

                    // Categoria e Navegação
                    if (termo.cat_termo === 'port') {
                        badge.innerText = 'Português';
                        badge.className = 'badge_category port';
                        btnVoltar.href = 'portugues.php';
                    } else if (termo.cat_termo === 'mat') {
                        badge.innerText = 'Matemática';
                        badge.className = 'badge_category math';
                        btnVoltar.href = 'matematica.php';
                    }

                    // Imagem com fallback
                    if (termo.foto_termo) {
                        imgElement.src = `assets/uploads/${termo.foto_termo}`;
                    } else {
                        imgElement.src = termo.cat_termo === 'port' ? 'assets/images/port_default.png' : 'assets/images/mat_default.png';
                    }
                    imgElement.alt = termo.nome_termo;

                    // Textos
                    descText.innerText = termo.descricao_termo;
                    exText.innerText = termo.exemplo_termo || "Nenhum exemplo prático fornecido.";

                    // Créditos
                    collabName.innerText = termo.nome_aluno || 'Autor Desconhecido';
                    collabClass.innerText = `Turma: ${termo.nome_turma || 'N/I'}`;

                } else {
                    alert('Termo não encontrado.');
                    window.location.href = 'index.php';
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                termTitle.innerText = "Erro ao carregar";
            });

        // Lógica do Modal
        const modal = document.getElementById("imageModal");
        const modalImg = document.getElementById("img01");
        const captionText = document.getElementById("caption");
        const closeBtn = document.querySelector(".close_modal");

        imgElement.onclick = function() {
            modal.style.display = "block";
            modalImg.src = this.src;
            captionText.innerHTML = this.alt;
        };

        closeBtn.onclick = () => modal.style.display = "none";
        
        window.onclick = (event) => {
            if (event.target == modal) modal.style.display = "none";
        };
    });
    </script>
</body>
</html>