<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - SGD</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="bg_light_page">

    <script>
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <header class="navbar_container">
        <div>
            <h1 class="logo_title">Painel Administrativo</h1>
            <p class="logo_subtitle">Gerencie as sugestões de termos</p>
        </div>
        <a href="api/logout.php" class="login_professor">
            <i class="material-icons">logout</i> <span>Sair</span>
        </a>
    </header>

    <main class="scroll_content">
        <div class="admin_summary_grid">
            <div class="summary_card pending">
                <div class="summary_icon"><i class="material-icons">schedule</i></div>
                <div class="summary_info"><span class="count">0</span><span class="label">Pendentes</span></div>
            </div>
            <div class="summary_card approved">
                <div class="summary_icon"><i class="material-icons">check_circle</i></div>
                <div class="summary_info"><span class="count">0</span><span class="label">Aprovadas</span></div>
            </div>
            <div class="summary_card rejected">
                <div class="summary_icon"><i class="material-icons">cancel</i></div>
                <div class="summary_info"><span class="count">0</span><span class="label">Rejeitadas</span></div>
            </div>
        </div>

        <div class="admin_toolbar">
            <div class="tabs_container">
                <button class="tab_btn active"><i class="material-icons">history</i><span class="tab_text">Pendentes (0)</span></button>
                <button class="tab_btn"><i class="material-icons">done_all</i><span class="tab_text">Aprovadas (0)</span></button>
                <button class="tab_btn"><i class="material-icons">block</i><span class="tab_text">Rejeitadas (0)</span></button>
            </div>

            <div class="filter_wrapper">
                <i class="material-icons" style="color: #667085; font-size: 20px;">filter_list</i>
                <select id="selectTurma">
                    <option value="todos">Todas as Turmas</option>
                </select>
            </div>
        </div>

        <div class="list_container">
            <p style="text-align:center; padding: 20px;">Carregando sugestões...</p>
        </div>
    </main>

    <div class="fab_container">
        <a class="fab_button" href="adicionar_termos_admin.php" title="Novo Termo"><img src="assets/images/plus-lg_icon.svg" id="add" alt="Adicionar"></a>
        <a class="fab_button fab_user_outline" href="cadastrar.php" title="Novo Usuário"><i class="material-icons">person_add</i></a>
        <button id="theme-toggle" class="fab_button" aria-label="Alternar tema"><img src="assets/images/moon_icon.svg" id="theme-icon" alt="Tema" width="24"></button>
    </div>

    <div id="editModal" class="modal_overlay">
        <div class="modal_card">
            <div class="modal_header">
                <h2>Editar Sugestão</h2>
                <button class="close_btn" onclick="closeEditModal()"><i class="material-icons">close</i></button>
            </div>
            <div class="modal_body">
                <form id="editForm">
                    <div class="input_group"><label>Título do Termo</label><input type="text" id="edit_title" required></div>
                    <div class="input_group"><label>Descrição</label><textarea id="edit_description" rows="4" required></textarea></div>
                    <div class="input_group"><label>Exemplo de Uso</label><textarea id="edit_example" rows="2"></textarea></div>
                </form>
            </div>
            <div class="modal_footer">
                <button class="btn_cancel" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" form="editForm" class="btn_submit_form">Salvar Alterações</button>
            </div>
        </div>
    </div>

    <div id="imageModal" class="modal_overlay" style="display:none; align-items:center; justify-content:center; z-index: 9999;">
        <span class="close_btn" onclick="closeImageModal()" style="position:absolute; top:20px; right:30px; color:white; font-size:40px; cursor:pointer;">&times;</span>
        <img class="modal_content" id="img01" style="max-width:90%; max-height:80%; border-radius:8px;">
        <div id="caption" style="color:white; margin-top:15px; text-align:center; font-weight:bold;"></div>
    </div>

    <script src="./assets/js/script.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const listContainer = document.querySelector('.list_container');
        const tabs = document.querySelectorAll('.tab_btn');
        const modal = document.getElementById('editModal');
        const imgModal = document.getElementById('imageModal');
        const editForm = document.getElementById('editForm');
        const selectTurma = document.getElementById('selectTurma');
        
        let statusAtual = 'pendente'; 

        // 1. CARREGAR TURMAS NO SELECT (USANDO api/turma.php)
        function carregarFiltroTurmas() {
            fetch('api/turma.php')
                .then(res => res.json())
                .then(resultado => {
                    if (resultado.success && Array.isArray(resultado.data)) {
                        selectTurma.innerHTML = '<option value="todos">Todas as Turmas</option>';
                        resultado.data.forEach(turma => {
                            const option = document.createElement('option');
                            option.value = turma.nome_turma; // Valor usado no filtro SQL
                            option.textContent = turma.nome_turma; // Nome exibido
                            selectTurma.appendChild(option);
                        });
                    }
                })
                .catch(err => console.error("Erro ao carregar turmas:", err));
        }

        // 2. CARREGAR LISTA DE TERMOS
        function carregarTermosAdmin(status) {
            statusAtual = status; 
            const turmaValue = selectTurma.value;
            listContainer.innerHTML = '<p style="text-align:center; padding:20px;">Buscando dados...</p>';
            
            fetch(`api/termos.php?status=${status}&cat=todos&turma=${turmaValue}`) 
                .then(res => res.json())
                .then(resultado => renderizarSugestoes(resultado.data))
                .catch(() => listContainer.innerHTML = '<p style="text-align:center; padding:20px;">Erro de conexão com o servidor.</p>');
        }

        // 3. RENDERIZAR OS CARDS NA TELA
        function renderizarSugestoes(termos) {
            listContainer.innerHTML = '';
            if (!termos || termos.length === 0) {
                listContainer.innerHTML = `<p style="padding:40px; text-align:center; color: #666;">Nenhumm termo encontrado para essa turma.</p>`;
                return;
            }

            termos.forEach(termo => {
                const dataF = termo.data_criacao ? new Date(termo.data_criacao).toLocaleDateString('pt-BR') : '---';
                const catClass = termo.cat_termo === 'mat' ? 'math' : 'port';
                const catIcon = termo.cat_termo === 'mat' ? 'calculate' : 'menu_book';
                const catLabel = termo.cat_termo === 'mat' ? 'Matemática' : 'Português';

                let fotoHTML = termo.foto_termo ? `<div class="suggestion_image_admin"><img src="assets/uploads/${termo.foto_termo}" onclick="abrirImagemModal(this.src, '${termo.nome_termo}')"></div>` : '';

                let botoesHTML = statusAtual !== 'aprovado' ? `<button class="btn_approve" onclick="alterarStatus(${termo.id_termo}, 'aprovado')"><i class="material-icons">check_circle</i> <span>Aprovar</span></button>` : '';
                botoesHTML += `<button class="btn_edit_outline" onclick='abrirEdicao(${JSON.stringify(termo)})'><i class="material-icons">edit</i> <span>Editar</span></button>`;
                botoesHTML += `<button class="btn_reject_outline" onclick="excluirTermo(${termo.id_termo})"><i class="material-icons">delete_forever</i> <span>Excluir</span></button>`;

                const card = `
                    <div class="suggestion_card">
                        <div class="suggestion_header">
                            <div class="term_title_row">
                                <span class="badge_category_alt ${catClass}"><i class="material-icons">${catIcon}</i> ${catLabel}</span>
                                <h2>${termo.nome_termo}</h2>
                            </div>
                            <p class="collab_meta">Por: <strong>${termo.nome_aluno}</strong> | ${termo.nome_turma || 'Turma N/I'}</p>
                            <p class="date_meta">Enviado em: ${dataF}</p>
                        </div>
                        <div class="suggestion_content_wrapper">
                            ${fotoHTML}
                            <div class="suggestion_body_text">
                                <div class="content_block"><h4>Descrição:</h4><p>${termo.descricao_termo}</p></div>
                                <div class="content_block"><h4>Exemplo:</h4><p class="example_text">${termo.exemplo_termo || '---'}</p></div>
                            </div>
                        </div>
                        <div class="suggestion_actions">${botoesHTML}</div>
                    </div>`;
                listContainer.insertAdjacentHTML('beforeend', card);
            });
        }

        // 4. ATUALIZAR NÚMEROS DO PAINEL (PENDENTES, APROVADAS...)
        function atualizarResumoPainel() {
            fetch('api/termos.php?contar_status=true')
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        const d = res.data;
                        const repro = d.reprovado || 0;
                        document.querySelector('.summary_card.pending .count').innerText = d.pendente;
                        document.querySelector('.summary_card.approved .count').innerText = d.aprovado;
                        document.querySelector('.summary_card.rejected .count').innerText = repro;
                        
                        tabs[0].querySelector('.tab_text').innerText = `Pendentes (${d.pendente})`;
                        tabs[1].querySelector('.tab_text').innerText = `Aprovadas (${d.aprovado})`;
                        tabs[2].querySelector('.tab_text').innerText = `Rejeitadas (${repro})`;
                    }
                });
        }

        // --- LISTENERS DE EVENTOS ---

        // Filtrar ao mudar a turma
        selectTurma.addEventListener('change', () => carregarTermosAdmin(statusAtual));

        // Mudar de aba (Pendentes, Aprovadas, Rejeitadas)
        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const estados = ['pendente', 'aprovado', 'reprovado'];
                carregarTermosAdmin(estados[index]);
            });
        });

        // --- FUNÇÕES DE AÇÃO (GLOBAIS) ---

        window.alterarStatus = function(id, novoStatus) {
            if(!confirm(`Deseja aprovar este termo?`)) return;
            fetch('api/termos.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_termo: id, status_termo: novoStatus, acao: 'mudar_status' })
            }).then(() => { carregarTermosAdmin(statusAtual); atualizarResumoPainel(); });
        };

        window.excluirTermo = function(id) {
            if(!confirm("Deseja excluir permanentemente esta sugestão?")) return;
            fetch('api/termos.php', {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_termo: id })
            }).then(() => { carregarTermosAdmin(statusAtual); atualizarResumoPainel(); });
        };

        window.abrirEdicao = function(termo) {
            document.getElementById('edit_title').value = termo.nome_termo;
            document.getElementById('edit_description').value = termo.descricao_termo;
            document.getElementById('edit_example').value = termo.exemplo_termo;
            editForm.dataset.id = termo.id_termo;
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeEditModal = () => { modal.style.display = 'none'; document.body.style.overflow = 'auto'; };

        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch('api/termos.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id_termo: this.dataset.id,
                    nome_termo: document.getElementById('edit_title').value,
                    descricao_termo: document.getElementById('edit_description').value,
                    exemplo_termo: document.getElementById('edit_example').value,
                    acao: 'editar_conteudo'
                })
            }).then(() => { closeEditModal(); carregarTermosAdmin(statusAtual); });
        });

        window.abrirImagemModal = function(src, titulo) {
            imgModal.style.display = "flex";
            document.getElementById("img01").src = src;
            // document.getElementById("caption").innerText = titulo;
            document.body.style.overflow = 'hidden';
        };

        window.closeImageModal = () => { imgModal.style.display = "none"; document.body.style.overflow = 'auto'; };

        // --- INICIALIZAÇÃO ---
        carregarFiltroTurmas();
        atualizarResumoPainel();
        carregarTermosAdmin('pendente');
    }); 
    </script>
</body>
</html>