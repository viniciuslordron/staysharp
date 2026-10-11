<?php if ($produto === null): ?>

    <h1>Produto não encontrado</h1>
    <p>O produto que você procurou não existe ou foi excluído.</p>
    <a class="btn" href="/staysharp/produtos">Voltar para produtos</a>

<?php else: ?>

    <div class="detalhe">
        <div class="detalhe-foto">
            <?php if ($produto["imagem"] !== ""): ?>
                <?php $verso = $produto["imagem_verso"] ?? ""; ?>
                <div class="troca-foto">
                    <img src="/staysharp/public/img/<?= $produto["imagem"] ?>" alt="<?= $produto["nome"] ?>">
                    <?php if ($verso !== ""): ?>
                        <img class="verso" src="/staysharp/public/img/<?= $verso ?>" alt="<?= $produto["nome"] ?> (costas)">
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="sem-foto">Sem foto</div>
            <?php endif; ?>
        </div>

        <div class="detalhe-info">
            <a class="categoria" href="/staysharp/produtos?categoria=<?= $produto["categoria_id"] ?>">
                <?= $nomeCategoria ?>
            </a>
            <h1><?= $produto["nome"] ?></h1>
            <p class="preco">R$ <?= number_format($produto["preco"], 2, ",", ".") ?></p>
            <p><?= $produto["descricao"] ?></p>

            <div class="acoes">
                <a class="btn btn-whatsapp" href="<?= $linkWhatsapp ?>" target="_blank">Pedir pelo WhatsApp</a>
                <a class="btn btn-secundario" href="/staysharp/produtos/editar?id=<?= $produto["id"] ?>">Editar</a>
                <a class="btn btn-secundario" href="/staysharp/produtos">Voltar para produtos</a>
            </div>
        </div>
    </div>

<?php endif; ?>