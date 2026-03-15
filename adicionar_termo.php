<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sugerir Novo Termo - Dicionário Técnico</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg_light_page">

    <header class="sticky_header">
        <div class="header_left">
            <button onclick="history.back()" class="btn_back"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="title_group">
                <h1>Sugerir Novo Termo</h1>
                <p class="subtitle_header">Contribua com o dicionário técnico</p>
            </div>
        </div>
    </header>

    <main class="form_container_desktop">
        <form id="cadastroTermo" enctype="multipart/form-data">
            <section class="form_section_card">
                <h2 class="section_title_form">Suas Informações</h2>
                <div class="form_grid_2">
                    <div class="input_group">
                        <label>Seu Nome *</label>
                        <input type="text" name="nome_aluno" placeholder="Digite seu nome completo" required>
                    </div>
                    <div class="input_group">
                        <label>Sua Turma</label>
                        <select name="turmas_id_turma" id="selectTurma" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                            <option value="" disabled selected>Carregando turmas...</option>
                        </select>
                    </div>
                </div>
            </section>

            <section class="form_section_card">
                <h2 class="section_title_form">Informações do Termo</h2>
                <div class="input_group">
                    <label>Nome do Termo *</label>
                    <input type="text" name="nome_termo" placeholder="Digite o termo técnico" required>
                </div>

                <div class="input_group">
                    <label>Disciplina *</label>
                    <div class="radio_group_horizontal">
                        <label class="radio_item">
                            <input type="radio" name="cat_termo" value="port" checked>
                            <span>Português</span>
                        </label>
                        <label class="radio_item">
                            <input type="radio" name="cat_termo" value="mat">
                            <span>Matemática</span>
                        </label>
                    </div>
                </div>

                <div class="input_group">
                    <label>Descrição do Termo *</label>
                    <textarea name="descricao_termo" placeholder="Explique o significado do termo de forma clara e objetiva" rows="4" required></textarea>
                </div>

                <div class="input_group">
                    <label>Exemplo de Uso (Opcional)</label>
                    <textarea name="exemplo_termo" placeholder="Forneça um exemplo prático de aplicação do termo" rows="3"></textarea>
                </div>

                <div class="input_group">
                    <label>Adicionar Foto (opcional)</label>
                    <div class="upload_area_desktop" onclick="document.getElementById('file_upload').click()">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p id="file_name_display">Clique para escolher uma imagem ou arraste aqui</p>
                        <span>PNG, JPG até 5MB</span>
                        <input type="file" name="foto_termo" id="file_upload" hidden accept="image/*">
                    </div>
                </div>
            </section>

            <div class="form_actions_footer">
                <button type="button" onclick="history.back()" class="btn_cancel">Cancelar</button>
                <button type="submit" class="btn_submit_form">
                    <i class="fa-solid fa-paper-plane"></i> Enviar para Revisão
                </button>
            </div>
        </form>
    </main>

    <?php require_once 'assets/layout/bnt_add_dark.php' ?>

    <script src="./assets/js/script.js"></script>

    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectTurma = document.getElementById('selectTurma');
            const fileInput = document.getElementById('file_upload');
            const fileNameDisplay = document.getElementById('file_name_display');

            fetch('api/turma.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Arquivo não encontrado ou erro no servidor');
                    }
                    return response.json();
                })
                .then(resultado => {
                    if (resultado.success && Array.isArray(resultado.data)) {
                        const selectTurma = document.getElementById('selectTurma');
                        if (resultado.data.length === 0) {
                            selectTurma.innerHTML = '<option value="" disabled selected>Nenhuma turma cadastrada</option>';
                            return;
                        }
                        selectTurma.innerHTML = '<option value="" disabled selected>Selecione sua turma</option>';
                        resultado.data.forEach(turma => {
                            const option = document.createElement('option');
                            option.value = turma.id_turma;
                            option.textContent = turma.nome_turma;
                            selectTurma.appendChild(option);
                        });
                    } else {
                        throw new Error(resultado.message || 'Erro desconhecido na API');
                    }
                })
                .catch(err => {
                    console.error("Erro detalhado:", err);
                    document.getElementById('selectTurma').innerHTML = '<option value="" disabled>Erro ao carregar turmas</option>';
                });

            fileInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    fileNameDisplay.innerText = "Selecionado: " + this.files[0].name;
                    fileNameDisplay.style.color = "#007bff";
                }
            });

            document.getElementById('cadastroTermo').addEventListener('submit', function(e) {
                e.preventDefault();
                const btnSubmit = document.querySelector('.btn_submit_form');
                const formData = new FormData(this);

                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';

                fetch('api/termos.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Sugestão enviada com sucesso! Aguarde a revisão do professor.');
                            window.location.href = 'index.php';
                        } else {
                            alert('Erro ao enviar: ' + data.message);
                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar para Revisão';
                        }
                    })
                    .catch(err => {
                        console.error("Erro na conexão:", err);
                        alert('Erro na conexão com o servidor.');
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar para Revisão';
                    });
            });
        });
    </script>
</body>
</html>