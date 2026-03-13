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
        <a href="#" class="login_professor" style="border: 1px solid var(--border_gray);">
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
        <a class="fab_button fab_user_outline" href="cadastrar.php">
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
        const modal = document.getElementById('editModal');

        function openEditModal() {
            modal.style.display = 'flex';
            // Impede o scroll da página ao fundo quando o modal abre
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            modal.style.display = 'none';
            // Devolve o scroll da página
            document.body.style.overflow = 'auto';
        }

        // Fecha o modal se clicar fora da caixa branca
        window.onclick = function(event) {
            if (event.target == modal) {
                closeEditModal();
            }
        }
    </script>

</body>
</html>