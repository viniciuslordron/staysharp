<?php if ($produto === null): ?>

    <h1>Produto não encontrado</h1>
    <p>O produto que você procurou não existe ou foi excluído.</p>
    <a class="btn" href="/staysharp/produtos">Voltar para produtos</a>

<?php else: ?>

    <?php
        $frente = $produto["imagem"] ?? "";
        $verso = $produto["imagem_verso"] ?? "";
        $preco = "R$ " . number_format($produto["preco"], 2, ",", ".");

        // estoque do tamanho escolhido ou, sem escolha, o total do produto
        $estoqueTotal = 0;
        $estoqueEscolhido = 0;
        foreach ($tamanhos as $t) {
            $estoqueTotal += (int) $t["estoque"];
            if ($t["tamanho"] === $tamanhoEscolhido) {
                $estoqueEscolhido = (int) $t["estoque"];
            }
        }
    ?>

    <div class="produto-pagina">
        <h1 class="produto-titulo"><?= htmlspecialchars($produto["nome"]) ?></h1>

        <div class="produto-colunas">
            <div class="produto-galeria">
                <?php if ($frente !== ""): ?>
                    <div class="troca-foto" id="galeria-fotos">
                        <img src="/staysharp/public/img/<?= htmlspecialchars($frente) ?>" alt="<?= htmlspecialchars($produto["nome"]) ?>">
                        <?php if ($verso !== ""): ?>
                            <img class="verso" src="/staysharp/public/img/<?= htmlspecialchars($verso) ?>" alt="<?= htmlspecialchars($produto["nome"]) ?> (costas)">
                            <span class="produto-contador" id="galeria-contador">1/2</span>
                        <?php endif; ?>
                    </div>

                    <?php if ($verso !== ""): ?>
                        <div class="produto-miniaturas" role="group" aria-label="Fotos do produto">
                            <button type="button" class="ativo" data-lado="frente" aria-label="Ver a foto de frente">
                                <img src="/staysharp/public/img/<?= htmlspecialchars($frente) ?>" alt="">
                            </button>
                            <button type="button" data-lado="verso" aria-label="Ver a foto de costas">
                                <img src="/staysharp/public/img/<?= htmlspecialchars($verso) ?>" alt="">
                            </button>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="produto-sem-foto">Sem foto</div>
                <?php endif; ?>
            </div>

            <div class="produto-lateral">
                <!-- preço e estoque -->
                <div class="produto-caixa">
                    <div class="produto-caixa-topo">
                        <a href="/staysharp/produtos?categoria=<?= $produto["categoria_id"] ?>">&bull; <?= htmlspecialchars($nomeCategoria) ?></a>
                        <span><?= $esgotado ? "Esgotado" : "Em estoque" ?></span>
                    </div>

                    <p class="produto-preco"><?= $preco ?></p>

                    <div class="produto-caixa-estoque">
                        <?php if ($tamanhoEscolhido !== ""): ?>
                            <strong>Estoque do tamanho <?= htmlspecialchars($tamanhoEscolhido) ?></strong>
                            <span><b><?= $estoqueEscolhido ?></b> un.</span>
                        <?php else: ?>
                            <strong>Estoque total</strong>
                            <span><b><?= $estoqueTotal ?></b> un.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- tamanhos -->
                <div class="produto-tamanhos">
                    <p class="produto-tamanhos-titulo">
                        Tamanho: <span><?= $tamanhoEscolhido !== "" ? htmlspecialchars($tamanhoEscolhido) : "escolha" ?></span>
                    </p>

                    <?php if (count($tamanhos) === 0): ?>
                        <p class="produto-vazio">Nenhum tamanho cadastrado.</p>
                    <?php else: ?>
                        <div class="produto-tamanhos-lista">
                            <?php foreach ($tamanhos as $t): ?>
                                <?php if ($t["estoque"] <= 0): ?>
                                    <span class="produto-tam esgotado" title="Esgotado"><?= htmlspecialchars($t["tamanho"]) ?></span>
                                <?php else: ?>
                                    <a class="produto-tam <?= $t["tamanho"] === $tamanhoEscolhido ? "ativo" : "" ?>"
                                       title="<?= $t["estoque"] ?> un. em estoque"
                                       href="/staysharp/produto?id=<?= $produto["id"] ?>&tamanho=<?= urlencode($t["tamanho"]) ?>"><?= htmlspecialchars($t["tamanho"]) ?></a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- descrição -->
                <?php if (trim($produto["descricao"] ?? "") !== ""): ?>
                    <details class="produto-bloco" open>
                        <summary>
                            <span>
                                <strong>Detalhes do Produto</strong>
                                <small>Descrição da peça</small>
                            </span>
                        </summary>
                        <p><?= nl2br(htmlspecialchars($produto["descricao"])) ?></p>
                    </details>
                <?php endif; ?>

                <!-- pedido -->
                <?php if ($esgotado): ?>
                    <span class="produto-cta desativado">Esgotado</span>
                <?php elseif ($tamanhoEscolhido === ""): ?>
                    <span class="produto-cta desativado">Escolha um tamanho</span>
                <?php else: ?>
                    <a class="produto-cta" href="<?= $linkWhatsapp ?>" target="_blank" rel="noopener">Pedir pelo WhatsApp (<?= htmlspecialchars($tamanhoEscolhido) ?>) &middot; <?= $preco ?></a>
                <?php endif; ?>

                <div class="produto-links">
                    <a href="/staysharp/produtos">&larr; Voltar para produtos</a>
                    <?php if (isset($_SESSION["usuario"])): ?>
                        <a href="/staysharp/produtos/editar?id=<?= $produto["id"] ?>">Editar produto</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // as miniaturas trocam a foto grande entre frente e costas
        (function () {
            var fotos = document.getElementById("galeria-fotos");
            var contador = document.getElementById("galeria-contador");
            var botoes = document.querySelectorAll("[data-lado]");
            botoes.forEach(function (botao) {
                botao.addEventListener("click", function () {
                    var verso = botao.dataset.lado === "verso";
                    fotos.classList.toggle("mostrar-verso", verso);
                    contador.textContent = verso ? "2/2" : "1/2";
                    botoes.forEach(function (b) { b.classList.toggle("ativo", b === botao); });
                });
            });
        })();
    </script>

<?php endif; ?>
