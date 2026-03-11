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
        <form id="cadastroTermo">
            <section class="form_section_card">
                <h2 class="section_title_form">Suas Informações</h2>
                
                <div class="form_grid_2">
                    <div class="input_group">
                        <label>Seu Nome *</label>
                        <input type="text" placeholder="Digite seu nome completo" required>
                    </div>
                <div class="input_group">
                    <label>Sua Turma</label>
                    <select name="turmas_id_turma" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: white;">
                        <option value="" disabled selected>Selecione sua turma</option>
                        <option value="1">6º ano</option>
                    </select>
                </div>
                </div>
            </section>

            <section class="form_section_card">
                <h2 class="section_title_form">Informações do Termo</h2>
                
                <div class="input_group">
                    <label>Nome do Termo *</label>
                    <input type="text" placeholder="Digite o termo técnico" required>
                </div>

                <div class="input_group">
                    <label>Disciplina *</label>
                    <div class="radio_group_horizontal">
                        <label class="radio_item">
                            <input type="radio" name="disciplina" value="portugues" checked>
                            <span>Português</span>
                        </label>
                        <label class="radio_item">
                            <input type="radio" name="disciplina" value="matematica">
                            <span>Matemática</span>
                        </label>
                    </div>
                </div>

                <div class="input_group">
                    <label>Descrição do Termo *</label>
                    <textarea placeholder="Explique o significado do termo de forma clara e objetiva" rows="4"></textarea>
                </div>

                <div class="input_group">
                    <label>Exemplo de Uso *</label>
                    <textarea placeholder="Forneça um exemplo prático de aplicação do termo" rows="3"></textarea>
                </div>

                <div class="input_group">
                    <label>Adicionar Foto (opcional)</label>
                    <div class="upload_area_desktop">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <p>Clique para escolher uma imagem ou arraste aqui</p>
                        <span>PNG, JPG até 5MB</span>
                        <input type="file" id="file_upload" hidden>
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

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const selectTurma = document.querySelector('select[name="turmas_id_turma"]');

    // --- PARTE NOVA: BUSCAR TURMAS NO BANCO ---
    fetch('api/termos.php?listar_turmas=true')
        .then(response => response.json())
        .then(resultado => {
            if (resultado.success) {
                // Limpa o "Carregando..." e mantém apenas a primeira opção desabilitada
                selectTurma.innerHTML = '<option value="" disabled selected>Selecione sua turma</option>';
                
                resultado.data.forEach(turma => {
                    const option = `<option value="${turma.id_turma}">${turma.nome_turma}</option>`;
                    selectTurma.insertAdjacentHTML('beforeend', option);
                });
            }
        })
        .catch(err => console.error("Erro ao carregar turmas:", err));


    // --- PARTE DE ENVIO DO FORMULÁRIO ---
    document.getElementById('cadastroTermo').addEventListener('submit', function(e) {
        e.preventDefault();

        const btnSubmit = document.querySelector('.btn_submit_form');
        
        // Captura os dados
        const dados = {
            nome_aluno: document.querySelector('input[placeholder="Digite seu nome completo"]').value,
            turmas_id_turma: selectTurma.value,
            nome_termo: document.querySelector('input[placeholder="Digite o termo técnico"]').value,
            cat_termo: document.querySelector('input[name="disciplina"]:checked').value === 'portugues' ? 'port' : 'mat',
            descricao_termo: document.querySelector('textarea[placeholder^="Explique"]').value,
            exemplo_termo: document.querySelector('textarea[placeholder^="Forneça"]').value
        };

        if(!dados.turmas_id_turma) {
            alert("Por favor, selecione uma turma.");
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enviando...';

        fetch('api/termos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sugestão enviada! Aguarde a revisão do professor.');
                window.location.href = 'index.php';
            } else {
                alert('Erro: ' + data.message);
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Enviar para Revisão';
            }
        });
    });
});
</script>
</body>
</html>