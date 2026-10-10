<h1><?= $titulo ?></h1>

<form class="busca" method="GET" action="/staysharp/produtos">
    <input type="text" name="busca" placeholder="Buscar pelo nome" value="<?= htmlspecialchars($busca) ?>">

    <select name="categoria">
        <option value="">Todas as categorias</option>
        <?php foreach ($categorias as $c): ?>
            <option value="<?= $c["id"] ?>" <?= $categoria == $c["id"] ? "selected" : "" ?>>
                <?= $c["nome"] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button class="btn" type="submit">Buscar</button>
    <a class="btn btn-secundario" href="/staysharp/produtos">Limpar</a>
</form>

<?php if (count($produtos) === 0): ?>
    <p>Nenhum produto encontrado.</p>
<?php else: ?>
    <div class="grid-produtos">
        <?php foreach ($produtos as $p): ?>
            <div class="card-produto">
                <?php if ($p["imagem"] !== ""): ?>
                    <img src="/staysharp/public/img/<?= $p["imagem"] ?>" alt="<?= $p["nome"] ?>">
                <?php else: ?>
                    <div class="sem-foto">Sem foto</div>
                <?php endif; ?>

                <div class="card-corpo">
                    <span class="categoria"><?= $nomesCategorias[$p["categoria_id"]] ?? "Sem categoria" ?></span>
                    <h3><?= $p["nome"] ?></h3>
                    <span class="preco">R$ <?= number_format($p["preco"], 2, ",", ".") ?></span>

                    <div class="acoes">
                        <a class="btn" href="/staysharp/produto?id=<?= $p["id"] ?>">Ver detalhes</a>
                        <a class="btn btn-secundario" href="/staysharp/produtos/editar?id=<?= $p["id"] ?>">Editar</a>
                        <form method="POST" action="/staysharp/produtos/excluir"
                              onsubmit="return confirm('Excluir este produto?')">
                            <input type="hidden" name="id" value="<?= $p["id"] ?>">
                            <button class="btn btn-perigo" type="submit">Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>