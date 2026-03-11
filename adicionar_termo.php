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
            <a href="portugues.html" class="btn_back"><i class="fa-solid fa-arrow-left"></i></a>
            <div class="title_group">
                <h1>Sugerir Novo Termo</h1>
                <p class="subtitle_header">Contribua com o dicionário técnico</p>
            </div>
        </div>
    </header>

    <main class="form_container_desktop">
        <form action="#" method="POST">
            <section class="form_section_card">
                <h2 class="section_title_form">Suas Informações</h2>
                
                <div class="form_grid_2">
                    <div class="input_group">
                        <label>Seu Nome *</label>
                        <input type="text" placeholder="Digite seu nome completo" required>
                    </div>
                    <div class="input_group">
                        <label>Sua Turma *</label>
                        <input type="text" placeholder="Ex: Técnico em Informática - 2A" required>
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
                <button type="button" class="btn_cancel">Cancelar</button>
                <button type="submit" class="btn_submit_form">
                    <i class="fa-solid fa-paper-plane"></i> Enviar para Revisão
                </button>
            </div>
        </form>
    </main>

</body>
</html>