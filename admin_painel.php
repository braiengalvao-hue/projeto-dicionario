<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    // Se não houver sessão, manda de volta para o login
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
                    <span class="count">2</span>
                    <span class="label">Pendentes</span>
                </div>
            </div>
            <div class="summary_card approved">
                <div class="summary_icon"><i class="material-icons">check_circle</i></div>
                <div class="summary_info">
                    <span class="count">124</span>
                    <span class="label">Aprovadas</span>
                </div>
            </div>
            <div class="summary_card rejected">
                <div class="summary_icon"><i class="material-icons">cancel</i></div>
                <div class="summary_info">
                    <span class="count">12</span>
                    <span class="label">Rejeitadas</span>
                </div>
            </div>
        </div>

        <div class="tabs_container">
            <button class="tab_btn active">Pendentes (2)</button>
            <button class="tab_btn">Aprovadas</button>
            <button class="tab_btn">Rejeitadas</button>
        </div>

        <div class="list_container">
            
            <div class="suggestion_card">
                <div class="suggestion_header">
                    <div class="term_title_row">
                        <h2>Logaritmo</h2>
                        <span class="badge_category_alt math">
                            <i class="material-icons">calculate</i> Matemática
                        </span>
                    </div>
                    <p class="collab_meta">
                        Por: <strong>Maria Eduarda Lima</strong> | Eletrônica - 2B
                    </p>
                    <p class="date_meta">Enviado em: 09/03/2026</p>
                </div>

                <div class="suggestion_body">
                    <div class="content_block">
                        <h4>Descrição:</h4>
                        <p>Expoente que se deve elevar uma base para obter determinado número.</p>
                    </div>
                    <div class="content_block">
                        <h4>Exemplo:</h4>
                        <p class="example_text">log₂(8) = 3, pois 2³ = 8</p>
                    </div>
                </div>

                <div class="suggestion_actions">
                    <button class="btn_approve">
                        <i class="material-icons">check_circle</i> Aprovar
                    </button>
                    <button class="btn_edit_outline" onclick="openEditModal()">
                        <i class="material-icons">edit</i> Editar
                    </button>
                    <button class="btn_reject_outline">
                        <i class="material-icons">cancel</i> Rejeitar
                    </button>
                </div>
            </div>

        </div>
    </main>

    <div class="fab_container">
        <a class="fab_button" href="adicionar_termo.php">
            <i class="material-icons">add</i>
        </a>
        <a class="fab_button fab_user_outline" href="cadastrar_usuarios.php">
            <i class="material-icons">person_add</i>
        </a>
    </div>

    <div id="editModal" class="modal_overlay">
        <div class="modal_card">
            <div class="modal_header">
                <h2>Editar Sugestão</h2>
                <button class="close_btn" onclick="closeEditModal()">
                    <i class="material-icons">close</i>
                </button>
            </div>
            
            <div class="modal_body">
                <form id="editForm">
                    <div class="input_group">
                        <label>Título do Termo</label>
                        <input type="text" id="edit_title" value="Logaritmo">
                    </div>

                    <div class="input_group">
                        <label>Descrição</label>
                        <textarea id="edit_description" rows="4">Expoente que se deve elevar uma base para obter determinado número.</textarea>
                    </div>

                    <div class="input_group">
                        <label>Exemplo de Uso</label>
                        <textarea id="edit_example" rows="2">log₂(8) = 3, pois 2³ = 8</textarea>
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
    let statusAtual = 'pendente'; // Começa sempre pelos pendentes

    // --- 1. CARREGAR TERMOS ---
    function carregarTermosAdmin(status = 'pendente') {
        statusAtual = status;
        
        // Como o professor vê todas as matérias no painel, usamos status=... e cat=todos (ou altere sua API para aceitar cat vazio como 'todos')
        // Aqui vamos buscar de ambas as categorias enviando 'port' e depois 'mat' ou ajustando a API
        fetch(`api/termos.php?status=${status}&cat=todos`) 
            .then(res => res.json())
            .then(resultado => {
                renderizarSugestoes(resultado.data);
                atualizarContadores(resultado.data.length, status);
            })
            .catch(err => {
                console.error("Erro ao carregar:", err);
                listContainer.innerHTML = '<p style="padding:20px; text-align:center;">Erro ao conectar com o servidor.</p>';
            });
    }

    // --- 2. RENDERIZAR OS CARDS ---
    function renderizarSugestoes(termos) {
        listContainer.innerHTML = '';
        if (termos.length === 0) {
            listContainer.innerHTML = `<p style="padding:40px; text-align:center; color: #666;">Nenhuma sugestão encontrada em "${statusAtual}".</p>`;
            return;
        }

        termos.forEach(termo => {
            const dataFormatada = new Date(termo.data_criacao).toLocaleDateString('pt-BR');
            const classeCat = termo.cat_termo === 'mat' ? 'math' : 'port';
            const iconCat = termo.cat_termo === 'mat' ? 'calculate' : 'menu_book';
            const labelCat = termo.cat_termo === 'mat' ? 'Matemática' : 'Português';

            const card = `
                <div class="suggestion_card" id="termo-${termo.id_termo}">
                    <div class="suggestion_header">
                        <div class="term_title_row">
                            <h2>${termo.nome_termo}</h2>
                            <span class="badge_category_alt ${classeCat}">
                                <i class="material-icons">${iconCat}</i> ${labelCat}
                            </span>
                        </div>
                        <p class="collab_meta">Por: <strong>${termo.nome_aluno}</strong> | ${termo.nome_turma}</p>
                        <p class="date_meta">Enviado em: ${dataFormatada}</p>
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
                        ${statusAtual !== 'aprovado' ? `
                            <button class="btn_approve" onclick="alterarStatus(${termo.id_termo}, 'aprovado')">
                                <i class="material-icons">check_circle</i> Aprovar
                            </button>` : ''}
                        
                        <button class="btn_edit_outline" onclick="abrirEdicao(${JSON.stringify(termo).replace(/"/g, '&quot;')})">
                            <i class="material-icons">edit</i> Editar
                        </button>

                        ${statusAtual !== 'rejeitado' ? `
                            <button class="btn_reject_outline" onclick="alterarStatus(${termo.id_termo}, 'rejeitado')">
                                <i class="material-icons">cancel</i> Rejeitar
                            </button>` : ''}
                    </div>
                </div>
            `;
            listContainer.insertAdjacentHTML('beforeend', card);
        });
    }

    // --- 3. ALTERAR STATUS (PUT) ---
    window.alterarStatus = function(id, novoStatus) {
        if(!confirm(`Deseja realmente definir como ${novoStatus}?`)) return;

        fetch('api/termos.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_termo: id, status_termo: novoStatus })
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert('Status atualizado!');
                carregarTermosAdmin(statusAtual); // Recarrega a lista atual
            } else {
                alert('Erro: ' + res.message);
            }
        });
    };

    // --- 4. LÓGICA DAS ABAS ---
    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            const estados = ['pendente', 'aprovado', 'rejeitado'];
            carregarTermosAdmin(estados[index]);
        });
    });

    // --- 5. FUNÇÕES DO MODAL ---
    window.abrirEdicao = function(termo) {
        document.getElementById('edit_title').value = termo.nome_termo;
        document.getElementById('edit_description').value = termo.descricao_termo;
        document.getElementById('edit_example').value = termo.exemplo_termo;
        // Salva o ID no formulário para saber o que editar
        document.getElementById('editForm').dataset.id = termo.id_termo;
        
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    };

    window.closeEditModal = function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    };

    function atualizarContadores(total, status) {
        // Opcional: Você pode fazer um fetch separado para contar todos de uma vez
        // Por enquanto, atualiza o número da aba selecionada
        const activeTab = document.querySelector('.tab_btn.active');
        if (status === 'pendente') activeTab.innerText = `Pendentes (${total})`;
    }
    // Função para buscar os números reais do banco de dados
function atualizarResumoCards() {
    fetch('api/termos.php?contar_status=true')
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const dados = res.data;
                
                // Atualiza os números nos cards coloridos de cima
                document.querySelector('.summary_card.pending .count').innerText = dados.pendente;
                document.querySelector('.summary_card.approved .count').innerText = dados.aprovado;
                document.querySelector('.summary_card.rejected .count').innerText = dados.rejeitado;

                // Atualiza o texto das abas (opcional, para ficar bonito)
                const abas = document.querySelectorAll('.tab_btn');
                abas[0].innerText = `Pendentes (${dados.pendente})`;
                abas[1].innerText = `Aprovadas (${dados.aprovado})`;
                abas[2].innerText = `Rejeitadas (${dados.rejeitado})`;
            }
        });
}

// Chame esta função em dois momentos:
// 1. Assim que a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            atualizarResumoCards();
            carregarTermosAdmin('pendente'); // sua função já existente
        });

        // 2. Dentro da sua função alterarStatus, logo após o sucesso do UPDATE
        // Exemplo:
        window.alterarStatus = function(id, novoStatus) {
            // ... seu fetch de PUT atual ...
            .then(res => {
                if(res.success) {
                    alert('Status atualizado!');
                    carregarTermosAdmin(statusAtual); // Recarrega a lista
                    atualizarResumoCards();           // RECALCULA OS NÚMEROS DO TOPO
                }
            });
        };

    // Inicialização
    carregarTermosAdmin();
});
</script>

</body>
</html>