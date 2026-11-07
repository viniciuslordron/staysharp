<?php
    $editando = isset($produto);
    $frenteAtual = $produto["imagem"] ?? "";
    $versoAtual = $produto["imagem_verso"] ?? "";
    $tamanhos = $tamanhos ?? [];
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

        <div class="tamanhos-editor">
            <span class="fotos-titulo">Tamanhos e estoque <small>(opcional)</small></span>

            <div class="tamanhos-painel">
                <div id="lista-tamanhos">
                    <?php foreach ($tamanhos as $t): ?>
                        <div class="tamanho-linha" data-linha data-nome="<?= htmlspecialchars($t["tamanho"]) ?>">
                            <span class="tamanho-nome"><?= htmlspecialchars($t["tamanho"]) ?></span>
                            <div class="estoque-form">
                                <input type="number" name="tamanhos[<?= $t["id"] ?>]" aria-label="Estoque do tamanho <?= htmlspecialchars($t["tamanho"]) ?>" value="<?= (int) $t["estoque"] ?>" min="0" max="2147483647" step="1" required>
                                <span class="estoque-un">un.</span>
                            </div>
                            <span class="estoque-status"></span>
                            <button type="button" class="btn-link-perigo" data-remover data-id="<?= $t["id"] ?>">Remover</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <p class="tamanhos-vazio" id="tamanhos-vazio" <?= count($tamanhos) > 0 ? "hidden" : "" ?>>Nenhum tamanho ainda. Escolha abaixo.</p>

                <div class="tamanho-novo">
                    <span class="tamanho-novo-titulo">Adicionar tamanho</span>
                    <div class="tamanho-novo-campos">
                        <?php foreach (["P", "M", "G", "GG", "Único"] as $rapido): ?>
                            <button type="button" class="tamanho-rapido" data-rapido="<?= $rapido ?>"><?= $rapido ?></button>
                        <?php endforeach; ?>
                        <input type="text" id="tamanho-outro" aria-label="Outro tamanho" maxlength="10" placeholder="Outro (ex.: XG)">
                        <button type="button" class="btn btn-secundario" id="tamanho-add">Adicionar</button>
                    </div>
                    <small class="tamanho-aviso" id="tamanho-aviso" hidden></small>
                </div>
            </div>
            <small>Com estoque 0 o tamanho aparece como esgotado na loja. Tudo é salvo junto no botão Salvar produto.</small>
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
                        <div class="tamanhos-lista" data-esp-tamanhos></div>
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

        // ===== tamanhos e estoque (salvos junto com o produto) =====
        var listaTamanhos = $("lista-tamanhos");
        var avisoTamanho = $("tamanho-aviso");
        var proximoNovo = 0;

        function nomesUsados() {
            return Array.prototype.map.call(listaTamanhos.querySelectorAll("[data-linha]"), function (l) {
                return l.dataset.nome.toLowerCase();
            });
        }

        function atualizarTamanhos() {
            var linhas = listaTamanhos.querySelectorAll("[data-linha]");
            var chips = [];

            linhas.forEach(function (linha) {
                var entrada = linha.querySelector("input[type=number]");
                var qtd = parseInt(entrada.value, 10);
                if (isNaN(qtd)) { qtd = 0; }

                var status = linha.querySelector(".estoque-status");
                status.textContent = qtd > 0 ? "Em estoque" : "Esgotado";
                status.classList.toggle("esgotado", qtd <= 0);

                chips.push({ nome: linha.dataset.nome, qtd: qtd });
            });

            $("tamanhos-vazio").hidden = linhas.length > 0;

            // pre-visualização dos tamanhos na página do produto
            todos("[data-esp-tamanhos]").forEach(function (alvo) {
                alvo.textContent = "";
                if (chips.length === 0) {
                    var vazio = document.createElement("small");
                    vazio.textContent = "Nenhum tamanho adicionado.";
                    alvo.appendChild(vazio);
                }
                chips.forEach(function (c) {
                    var chip = document.createElement("span");
                    chip.className = "tamanho" + (c.qtd <= 0 ? " esgotado" : "");
                    chip.appendChild(document.createTextNode(c.nome));
                    var peq = document.createElement("small");
                    peq.textContent = c.qtd <= 0 ? "Esgotado" : c.qtd + " un.";
                    chip.appendChild(peq);
                    alvo.appendChild(chip);
                });
            });

            // marca os botoes rapidos que ja foram usados
            var usados = nomesUsados();
            todos("[data-rapido]").forEach(function (b) {
                b.disabled = usados.indexOf(b.dataset.rapido.toLowerCase()) !== -1;
            });
        }

        function avisarTamanho(msg) {
            avisoTamanho.textContent = msg;
            avisoTamanho.hidden = msg === "";
        }

        function adicionarTamanho(nome) {
            nome = nome.trim();
            if (nome === "") { return; }

            if (nomesUsados().indexOf(nome.toLowerCase()) !== -1) {
                avisarTamanho("O tamanho " + nome + " já está na lista.");
                return;
            }
            avisarTamanho("");

            var indice = proximoNovo++;
            var linha = document.createElement("div");
            linha.className = "tamanho-linha";
            linha.setAttribute("data-linha", "");
            linha.dataset.nome = nome;

            var rotulo = document.createElement("span");
            rotulo.className = "tamanho-nome";
            rotulo.textContent = nome;

            var oculto = document.createElement("input");
            oculto.type = "hidden";
            oculto.name = "novos_tamanhos[" + indice + "][nome]";
            oculto.value = nome;

            var envoltorio = document.createElement("div");
            envoltorio.className = "estoque-form";
            var entrada = document.createElement("input");
            entrada.type = "number";
            entrada.name = "novos_tamanhos[" + indice + "][estoque]";
            entrada.value = "0";
            entrada.min = "0"; entrada.max = "2147483647"; entrada.step = "1"; entrada.required = true;
            entrada.setAttribute("aria-label", "Estoque do tamanho " + nome);
            var un = document.createElement("span");
            un.className = "estoque-un";
            un.textContent = "un.";
            envoltorio.appendChild(entrada);
            envoltorio.appendChild(un);

            var status = document.createElement("span");
            status.className = "estoque-status";

            var remover = document.createElement("button");
            remover.type = "button";
            remover.className = "btn-link-perigo";
            remover.setAttribute("data-remover", "");
            remover.textContent = "Remover";

            linha.appendChild(rotulo);
            linha.appendChild(oculto);
            linha.appendChild(envoltorio);
            linha.appendChild(status);
            linha.appendChild(remover);
            listaTamanhos.appendChild(linha);

            atualizarTamanhos();
            entrada.focus();
            entrada.select();
        }

        todos("[data-rapido]").forEach(function (b) {
            b.addEventListener("click", function () { adicionarTamanho(b.dataset.rapido); });
        });

        var campoOutro = $("tamanho-outro");
        function adicionarOutro() {
            adicionarTamanho(campoOutro.value);
            if (avisoTamanho.hidden) { campoOutro.value = ""; }
        }
        $("tamanho-add").addEventListener("click", adicionarOutro);
        campoOutro.addEventListener("keydown", function (e) {
            if (e.key === "Enter") { e.preventDefault(); adicionarOutro(); }
        });

        listaTamanhos.addEventListener("input", atualizarTamanhos);
        listaTamanhos.addEventListener("click", function (e) {
            var botao = e.target.closest("[data-remover]");
            if (!botao) { return; }

            var linha = botao.closest("[data-linha]");
            // tamanho que ja existe no banco: avisa o servidor para excluir ao salvar
            if (botao.dataset.id) {
                var apagar = document.createElement("input");
                apagar.type = "hidden";
                apagar.name = "remover_tamanhos[]";
                apagar.value = botao.dataset.id;
                $("form-produto").appendChild(apagar);
            }
            linha.remove();
            avisarTamanho("");
            atualizarTamanhos();
        });

        [nome, descricao, preco].forEach(function (c) { c.addEventListener("input", atualizarTexto); });
        categoria.addEventListener("change", atualizarTexto);

        atualizarTexto();
        atualizarTamanhos();
    })();
</script>
