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

    // --- BUSCA DOS DADOS DO TERMO ---
    fetch(`api/termos.php?id=${termoId}`)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                const termo = res.data;

                // 1. Título da Aba e do Cabeçalho
                document.title = `${termo.nome_termo} - Dicionário Técnico`;
                document.querySelector('.term_main_title').innerText = termo.nome_termo;

                // 2. Categoria e Lógica do Botão Voltar
                const badge = document.querySelector('.badge_category');
                const btnVoltar = document.getElementById('btn_voltar');

                if (termo.cat_termo === 'port') {
                    badge.innerText = 'Português';
                    btnVoltar.href = 'portugues.php';
                } else {
                    badge.innerText = 'Matemática';
                    btnVoltar.href = 'matematica.php';
                }
                
                // 3. Imagem (AJUSTADO COM O CAMINHO DA PASTA)
                const imgElement = document.querySelector('.featured_image');
                
                if (termo.foto_termo) {
                    // Se houver foto no banco, concatena com a pasta de uploads
                    imgElement.src = `assets/uploads/${termo.foto_termo}`;
                } else {
                    // Se não houver, usa a imagem padrão da categoria
                    imgElement.src = termo.cat_termo === 'port' ? 'assets/images/port.png' : 'assets/images/mat.png';
                }
                imgElement.alt = termo.nome_termo;

                // 4. Conteúdo (Descrição e Exemplo)
                const cards = document.querySelectorAll('.info_card p');
                cards[0].innerText = termo.descricao_termo;
                cards[1].innerText = termo.exemplo_termo || "Nenhum exemplo prático fornecido para este termo.";

                // 5. Rodapé (Aluno e Turma)
                document.querySelector('.collab_name strong').innerText = termo.nome_aluno;
                document.querySelector('.collab_class').innerText = `Turma: ${termo.nome_turma}`;
                
            } else {
                alert('Termo não encontrado ou ainda pendente de aprovação.');
                window.location.href = 'index.php';
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao conectar com o servidor.');
        });

    // --- LÓGICA DO MODAL (DENTRO DO MESMO DOMContentLoaded) ---
    const modal = document.getElementById("imageModal");
    const img = document.querySelector(".featured_image");
    const modalImg = document.getElementById("img01");
    const captionText = document.getElementById("caption");
    const span = document.querySelector(".close_modal");

    img.onclick = function() {
        modal.style.display = "block";
        modalImg.src = this.src;
        captionText.innerHTML = this.alt;
    }

    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
</script>
</body>
</html>