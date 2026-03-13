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
</head>
<body class="bg_light_page">

    <header class="navbar_container">
        <div>
            <h1 class="logo_title">Painel Administrativo</h1>
            <p class="logo_subtitle">Gerencie as sugestões de termos</p>
        </div>
        <a href="api/logout.php" class="login_professor" style="border: 1px solid var(--border_gray);">
            <i class="material-icons">logout</i> Sair
        </a>
    </header>

    <main class="scroll_content">
        <div class="admin_summary_grid">
            <div class="summary_card pending">
                <div class="summary_icon"><i class="material-icons">schedule</i></div>
                <div class="summary_info">
                    <span class="count">0</span>
                    <span class="label">Pendentes</span>
                </div>
            </div>
            <div class="summary_card approved">
                <div class="summary_icon"><i class="material-icons">check_circle</i></div>
                <div class="summary_info">
                    <span class="count">0</span>
                    <span class="label">Aprovadas</span>
                </div>
            </div>
            <div class="summary_card rejected">
                <div class="summary_icon"><i class="material-icons">cancel</i></div>
                <div class="summary_info">
                    <span class="count">0</span>
                    <span class="label">Rejeitadas</span>
                </div>
            </div>
        </div>

        <div class="tabs_container">
            <button class="tab_btn active">Pendentes (0)</button>
            <button class="tab_btn">Aprovadas (0)</button>
            <button class="tab_btn">Rejeitadas (0)</button>
        </div>

        <div class="list_container">
            <p style="text-align:center; padding: 20px;">Carregando sugestões...</p>
        </div>
    </main>

    <div class="fab_container">
        <a class="fab_button" href="adicionar_termos_admin.php" title="Novo Termo"><i class="material-icons">add</i></a>
        <a class="fab_button fab_user_outline" href="cadastrar_usuarios.php" title="Novo Usuário"><i class="material-icons">person_add</i></a>
    </div>

    <div id="editModal" class="modal_overlay">
        <div class="modal_card">
            <div class="modal_header">
                <h2>Editar Sugestão</h2>
                <button class="close_btn" onclick="closeEditModal()"><i class="material-icons">close</i></button>
            </div>
            <div class="modal_body">
                <form id="editForm">
                    <div class="input_group">
                        <label>Título do Termo</label>
                        <input type="text" id="edit_title" required>
                    </div>
                    <div class="input_group">
                        <label>Descrição</label>
                        <textarea id="edit_description" rows="4" required></textarea>
                    </div>
                    <div class="input_group">
                        <label>Exemplo de Uso</label>
                        <textarea id="edit_example" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal_footer">
                <button class="btn_cancel" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" form="editForm" class="btn_submit_form">Salvar Alterações</button>
            </div>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const listContainer = document.querySelector('.list_container');
    const tabs = document.querySelectorAll('.tab_btn');
    const modal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    
    let statusAtual = 'pendente'; 

    function atualizarResumoPainel() {
        fetch('api/termos.php?contar_status=true')
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    // Sincronizado com o nome 'reprovado' que vem da sua API
                    const totalReprovados = d.reprovado || 0;

                    document.querySelector('.summary_card.pending .count').innerText = d.pendente;
                    document.querySelector('.summary_card.approved .count').innerText = d.aprovado;
                    document.querySelector('.summary_card.rejected .count').innerText = totalReprovados;

                    tabs[0].innerText = `Pendentes (${d.pendente})`;
                    tabs[1].innerText = `Aprovadas (${d.aprovado})`;
                    tabs[2].innerText = `Rejeitadas (${totalReprovados})`;
                }
            });
    }

    function carregarTermosAdmin(status) {
        statusAtual = status; 
        listContainer.innerHTML = '<p style="text-align:center; padding:20px;">Buscando dados...</p>';
        
        fetch(`api/termos.php?status=${status}&cat=todos`) 
            .then(res => res.json())
            .then(resultado => {
                renderizarSugestoes(resultado.data);
            })
            .catch(err => {
                console.error(err);
                listContainer.innerHTML = '<p style="padding:20px; text-align:center;">Erro de conexão.</p>';
            });
    }

    function renderizarSugestoes(termos) {
        listContainer.innerHTML = '';
        if (!termos || termos.length === 0) {
            listContainer.innerHTML = `<p style="padding:40px; text-align:center; color: #666;">Nenhum termo em "${statusAtual.toUpperCase()}".</p>`;
            return;
        }

        termos.forEach(termo => {
            const dataF = termo.data_criacao ? new Date(termo.data_criacao).toLocaleDateString('pt-BR') : '---';
            const catClass = termo.cat_termo === 'mat' ? 'math' : 'port';
            const catIcon = termo.cat_termo === 'mat' ? 'calculate' : 'menu_book';
            const catLabel = termo.cat_termo === 'mat' ? 'Matemática' : 'Português';

            let botoesHTML = '';

            // Botão Aprovar: Aparece em Pendentes e Reprovados
            if (statusAtual !== 'aprovado') {
                botoesHTML += `
                    <button class="btn_approve" onclick="alterarStatus(${termo.id_termo}, 'aprovado')">
                        <i class="material-icons">check_circle</i> Aprovar
                    </button>`;
            }

            // Botão Editar: Sempre aparece
            botoesHTML += `
                <button class="btn_edit_outline" onclick='abrirEdicao(${JSON.stringify(termo)})'>
                    <i class="material-icons">edit</i> Editar
                </button>`;

            // Lógica Rejeitar vs Excluir
            if (statusAtual != 'reprovado') {
                // Se já está reprovado, mostra botão de EXCLUIR definitivo
                botoesHTML += `
                    <button class="btn_reject_outline" style="color: #d32f2f; border-color: #d32f2f;" onclick="excluirTermo(${termo.id_termo})">
                        <i class="material-icons">delete_forever</i> Excluir
                    </button>`;
            } 

            const card = `
                <div class="suggestion_card">
                    <div class="suggestion_header">
                        <div class="term_title_row">
                            <h2>${termo.nome_termo}</h2>
                            <span class="badge_category_alt ${catClass}">
                                <i class="material-icons">${catIcon}</i> ${catLabel}
                            </span>
                        </div>
                        <p class="collab_meta">Por: <strong>${termo.nome_aluno}</strong> | ${termo.nome_turma || 'Turma N/I'}</p>
                        <p class="date_meta">Enviado em: ${dataF}</p>
                    </div>
                    <div class="suggestion_body">
                        <div class="content_block">
                            <h4>Descrição:</h4>
                            <p>${termo.descricao_termo}</p>
                        </div>
                        <div class="content_block">
                            <h4>Exemplo:</h4>
                            <p class="example_text">${termo.exemplo_termo || '---'}</p>
                        </div>
                    </div>
                    <div class="suggestion_actions">
                        ${botoesHTML}
                    </div>
                </div>`;
            listContainer.insertAdjacentHTML('beforeend', card);
        });
    }

    window.alterarStatus = function(id, novoStatus) {
        const acaoTxt = novoStatus === 'aprovado' ? 'aprovar' : 'rejeitar';
        if(!confirm(`Deseja realmente ${acaoTxt} este termo?`)) return;

        fetch('api/termos.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_termo: id, status_termo: novoStatus, acao: 'mudar_status' })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                carregarTermosAdmin(statusAtual);
                atualizarResumoPainel();
            }
        });
    };

    window.excluirTermo = function(id) {
        if(!confirm("Tem certeza que deseja EXCLUIR permanentemente este termo?")) return;
        
        fetch('api/termos.php', {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_termo: id })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                carregarTermosAdmin(statusAtual);
                atualizarResumoPainel();
            } else {
                alert("Erro ao excluir: " + res.message);
            }
        });
    };

    window.abrirEdicao = function(termo) {
        document.getElementById('edit_title').value = termo.nome_termo;
        document.getElementById('edit_description').value = termo.descricao_termo;
        document.getElementById('edit_example').value = termo.exemplo_termo;
        editForm.dataset.id = termo.id_termo;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeEditModal = function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const dados = {
            id_termo: this.dataset.id,
            nome_termo: document.getElementById('edit_title').value,
            descricao_termo: document.getElementById('edit_description').value,
            exemplo_termo: document.getElementById('edit_example').value,
            acao: 'editar_conteudo'
        };

        fetch('api/termos.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dados)
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                closeEditModal();
                carregarTermosAdmin(statusAtual);
            }
        });
    });

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const estados = ['pendente', 'aprovado', 'reprovado'];
            carregarTermosAdmin(estados[index]);
        });
    });

    window.onclick = function(e) { if (e.target == modal) closeEditModal(); }

    atualizarResumoPainel();
    carregarTermosAdmin('pendente');
}); 
</script>
</body>
</html>