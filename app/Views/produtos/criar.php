<?php
    $editando = isset($produto);
    $frenteAtual = $produto["imagem"] ?? "";
    $versoAtual = $produto["imagem_verso"] ?? "";
?>
<h1><?= $editando ? "Editar Produto" : "Novo Produto" ?></h1>

<div class="editor-produto">
    <form class="formulario" id="form-produto" method="POST" enctype="multipart/form-data" action="<?= $editando ? "/staysharp/produtos/atualizar" : "/staysharp/produtos/salvar" ?>">
        <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= $produto["id"] ?>">
        <?php endif; ?>

        <p class="aviso-obrigatorio"><span class="obrigatorio">*</span> campos obrigatórios</p>

        <label>
            <span>Nome <span class="obrigatorio">*</span></span>
            <input type="text" name="nome" id="campo-nome" maxlength="120" placeholder="Ex.: Camisa Staysharp Preta" value="<?= htmlspecialchars($produto["nome"] ?? "") ?>" required>
        </label>

        <label>
            <span>Descrição <small>(opcional)</small></span>
            <textarea name="descricao" id="campo-descricao" placeholder="Fale sobre o tecido, a modelagem, o caimento..."><?= htmlspecialchars($produto["descricao"] ?? "") ?></textarea>
        </label>

        <div class="linha-campos">
            <label>
                <span>Preço (R$) <span class="obrigatorio">*</span></span>
                <input type="number" name="preco" id="campo-preco" step="0.01" min="0.01" placeholder="0,00" value="<?= htmlspecialchars((string) ($produto["preco"] ?? "")) ?>" required>
            </label>

            <label>
                <span>Categoria <span class="obrigatorio">*</span></span>
                <select name="categoria_id" id="campo-categoria" required>
                    <option value="">Selecione uma categoria</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?= $categoria["id"] ?>" <?= $editando && $produto["categoria_id"] == $categoria["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($categoria["nome"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="fotos">
            <span class="fotos-titulo">Fotos</span>

            <div class="fotos-grade">
                <div class="foto-campo">
                    <label class="upload" id="upload-frente">
                        <input type="file" name="imagem" id="campo-frente" accept="image/jpeg,image/png,image/webp,image/gif" <?= $editando && $frenteAtual !== "" ? "" : "required" ?>>
                        <img class="upload-previa" id="previa-frente" alt="Foto de frente" <?= $frenteAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($frenteAtual) . '"' : "hidden" ?>>
                        <span class="upload-vazio" <?= $frenteAtual !== "" ? "hidden" : "" ?>>
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                            Clique para escolher
                        </span>
                        <span class="upload-trocar" <?= $frenteAtual !== "" ? "" : "hidden" ?>>Trocar foto</span>
                    </label>
                    <strong>Frente <span class="obrigatorio">*</span></strong>
                </div>

                <div class="foto-campo">
                    <label class="upload" id="upload-verso">
                        <input type="file" name="imagem_verso" id="campo-verso" accept="image/jpeg,image/png,image/webp,image/gif">
                        <img class="upload-previa" id="previa-verso" alt="Foto de costas" <?= $versoAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($versoAtual) . '"' : "hidden" ?>>
                        <span class="upload-vazio" <?= $versoAtual !== "" ? "hidden" : "" ?>>
                            <svg viewBox="0 0 24 24" width="28" height="28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 16V4M7 9l5-5 5 5M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3"/></svg>
                            Clique para escolher
                        </span>
                        <span class="upload-trocar" <?= $versoAtual !== "" ? "" : "hidden" ?>>Trocar foto</span>
                    </label>
                    <strong>Costas <small>(opcional)</small></strong>
                </div>
            </div>

            <small>JPG, PNG, WEBP ou GIF, até 5 MB. A foto de costas aparece quando o cliente passa o mouse sobre o produto.</small>

            <?php if ($versoAtual !== ""): ?>
                <label class="opcao">
                    <input type="checkbox" name="remover_verso" id="campo-remover-verso" value="1">
                    Remover a foto de costas
                </label>
            <?php endif; ?>
        </div>

        <div class="form-botoes">
            <button class="btn" type="submit">Salvar produto</button>
            <a class="btn btn-secundario" href="/staysharp/produtos">Voltar</a>
        </div>
    </form>

    <aside class="espelho" aria-label="Prévia do anúncio">
        <span class="espelho-titulo">Prévia do anúncio</span>

        <div class="espelho-abas" role="tablist">
            <button type="button" class="aba ativa" data-aba="lista" role="tab">Na listagem</button>
            <button type="button" class="aba" data-aba="detalhe" role="tab">Ao abrir o produto</button>
        </div>

        <!-- como aparece na lista de produtos -->
        <div class="espelho-painel" data-painel="lista">
            <div class="card-produto">
                <div class="troca-foto">
                    <img data-esp-frente alt="" <?= $frenteAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($frenteAtual) . '"' : "hidden" ?>>
                    <img class="verso" data-esp-verso alt="" <?= $versoAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($versoAtual) . '"' : "hidden" ?>>
                    <span class="espelho-sem-foto" data-esp-sem-foto <?= $frenteAtual !== "" ? "hidden" : "" ?>>Sem foto</span>
                </div>

                <div class="card-corpo">
                    <span class="categoria" data-esp="categoria">Categoria</span>
                    <h3 data-esp="nome">Nome do produto</h3>
                    <span class="preco" data-esp="preco">R$ 0,00</span>
                    <div class="acoes">
                        <span class="btn">Ver detalhes</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- como aparece na página do produto -->
        <div class="espelho-painel" data-painel="detalhe" hidden>
            <div class="card-produto espelho-detalhe">
                <div class="troca-foto">
                    <img data-esp-frente alt="" <?= $frenteAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($frenteAtual) . '"' : "hidden" ?>>
                    <img class="verso" data-esp-verso alt="" <?= $versoAtual !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($versoAtual) . '"' : "hidden" ?>>
                    <span class="espelho-sem-foto" data-esp-sem-foto <?= $frenteAtual !== "" ? "hidden" : "" ?>>Sem foto</span>
                </div>

                <div class="card-corpo">
                    <span class="categoria" data-esp="categoria">Categoria</span>
                    <h2 class="espelho-detalhe-nome" data-esp="nome">Nome do produto</h2>
                    <span class="preco" data-esp="preco">R$ 0,00</span>
                    <p class="espelho-descricao" data-esp="descricao"></p>

                    <div class="tamanhos">
                        <span class="tamanhos-titulo">Tamanho</span>
                        <small>Os tamanhos e o estoque são cadastrados depois de salvar.</small>
                    </div>

                    <div class="acoes">
                        <span class="btn btn-desativado">Escolha um tamanho</span>
                    </div>
                </div>
            </div>
        </div>

        <small>Passe o mouse sobre a foto para ver o verso, como na loja.</small>
    </aside>
</div>

<script>
    (function () {
        var $ = function (id) { return document.getElementById(id); };
        var todos = function (seletor) { return document.querySelectorAll(seletor); };

        var nome = $("campo-nome"), descricao = $("campo-descricao"), preco = $("campo-preco"), categoria = $("campo-categoria");
        var removerVerso = $("campo-remover-verso");

        function texto(chave, valor) {
            todos('[data-esp="' + chave + '"]').forEach(function (el) { el.textContent = valor; });
        }

        function atualizarTexto() {
            texto("nome", nome.value.trim() || "Nome do produto");
            texto("descricao", descricao.value.trim());

            var valor = parseFloat(preco.value);
            texto("preco", "R$ " + (isNaN(valor) ? 0 : valor).toLocaleString("pt-BR", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

            var opcao = categoria.options[categoria.selectedIndex];
            texto("categoria", categoria.value ? opcao.text.trim() : "Categoria");
        }

        function mostrarFoto(atributo, url, visivel) {
            todos("[" + atributo + "]").forEach(function (img) {
                if (url) { img.src = url; }
                img.hidden = !visivel;
            });
        }

        function atualizarSemFoto() {
            var temFrente = !document.querySelector("[data-esp-frente]").hidden;
            todos("[data-esp-sem-foto]").forEach(function (el) { el.hidden = temFrente; });
        }

        // liga um campo de arquivo à miniatura do formulário e às fotos das duas prévias
        function ligarUpload(campo, previa, atributo) {
            var input = $(campo), mini = $(previa);
            var caixa = input.closest(".upload");
            var vazio = caixa.querySelector(".upload-vazio");
            var trocar = caixa.querySelector(".upload-trocar");

            input.addEventListener("change", function () {
                var arquivo = input.files[0];
                if (!arquivo) { return; }

                if (arquivo.size > 5 * 1024 * 1024) {
                    alert("A imagem deve ter no máximo 5 MB");
                    input.value = "";
                    return;
                }

                var url = URL.createObjectURL(arquivo);
                mini.src = url; mini.hidden = false;
                mini.style.opacity = "";
                mostrarFoto(atributo, url, true);
                vazio.hidden = true; trocar.hidden = false;

                if (removerVerso && campo === "campo-verso") { removerVerso.checked = false; }
                atualizarSemFoto();
            });
        }

        ligarUpload("campo-frente", "previa-frente", "data-esp-frente");
        ligarUpload("campo-verso", "previa-verso", "data-esp-verso");

        // marcar "remover costas" esconde a foto de costas nas prévias
        if (removerVerso) {
            removerVerso.addEventListener("change", function () {
                var mostrar = !removerVerso.checked || $("campo-verso").files.length > 0;
                mostrarFoto("data-esp-verso", null, mostrar);
                $("previa-verso").style.opacity = mostrar ? "" : "0.3";
            });
        }

        // abas: lista x página do produto
        todos(".espelho-abas .aba").forEach(function (aba) {
            aba.addEventListener("click", function () {
                todos(".espelho-abas .aba").forEach(function (a) { a.classList.toggle("ativa", a === aba); });
                todos(".espelho-painel").forEach(function (p) { p.hidden = p.dataset.painel !== aba.dataset.aba; });
            });
        });

        [nome, descricao, preco].forEach(function (c) { c.addEventListener("input", atualizarTexto); });
        categoria.addEventListener("change", atualizarTexto);

        atualizarTexto();
    })();
</script>
