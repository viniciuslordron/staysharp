<section class="hero carrossel">
    <div class="slides">
        <img class="slide ativo" src="/staysharp/public/img/banner-bone.jpg" alt="Boné Stay Sharp">
        <img class="slide" src="/staysharp/public/img/banner-camisa.jpg" alt="Camiseta Stay Sharp">
    </div>

    <button class="seta seta-esq" type="button" aria-label="Anterior">&#8249;</button>
    <button class="seta seta-dir" type="button" aria-label="Próximo">&#8250;</button>

    <div class="hero-rodape">
        <a class="btn btn-claro" href="/staysharp/produtos">Ver produtos</a>
        <div class="pontos">
            <span class="ponto ativo"></span>
            <span class="ponto"></span>
        </div>
    </div>
</section>

<script>
    (function () {
        var slides = document.querySelectorAll(".carrossel .slide");
        var pontos = document.querySelectorAll(".carrossel .ponto");
        var atual = 0;
        var timer;

        function ir(n) {
            atual = (n + slides.length) % slides.length;
            slides.forEach(function (s, i) { s.classList.toggle("ativo", i === atual); });
            pontos.forEach(function (p, i) { p.classList.toggle("ativo", i === atual); });
        }

        function iniciar() {
            clearInterval(timer);
            timer = setInterval(function () { ir(atual + 1); }, 5000);
        }

        document.querySelector(".seta-esq").addEventListener("click", function () { ir(atual - 1); iniciar(); });
        document.querySelector(".seta-dir").addEventListener("click", function () { ir(atual + 1); iniciar(); });
        pontos.forEach(function (p, i) {
            p.addEventListener("click", function () { ir(i); iniciar(); });
        });

        iniciar();
    })();
</script>

<section class="secao">
    <h2>Categorias</h2>
    <div class="grid-categorias">
        <?php foreach ($categorias as $c): ?>
            <a class="card-categoria" href="/staysharp/produtos?categoria=<?= $c["id"] ?>">
                <?= $c["nome"] ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="secao">
    <h2>Destaques</h2>
    <?php if (count($destaques) === 0): ?>
        <p>Nenhum produto cadastrado ainda.</p>
    <?php else: ?>
        <div class="grid-produtos">
            <?php foreach ($destaques as $p): ?>
                <div class="card-produto">
                    <?php if ($p["imagem"] !== ""): ?>
                        <?php $verso = $p["imagem_verso"] ?? ""; ?>
                        <a class="troca-foto" href="/staysharp/produto?id=<?= $p["id"] ?>">
                            <img src="/staysharp/public/img/<?= $p["imagem"] ?>" alt="<?= $p["nome"] ?>">
                            <?php if ($verso !== ""): ?>
                                <img class="verso" src="/staysharp/public/img/<?= $verso ?>" alt="<?= $p["nome"] ?> (costas)">
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <div class="sem-foto">Sem foto</div>
                    <?php endif; ?>

                    <div class="card-corpo">
                        <span class="categoria"><?= $nomesCategorias[$p["categoria_id"]] ?? "Sem categoria" ?></span>
                        <h3><?= $p["nome"] ?></h3>
                        <span class="preco">R$ <?= number_format($p["preco"], 2, ",", ".") ?></span>
                        <div class="acoes">
                            <a class="btn" href="/staysharp/produto?id=<?= $p["id"] ?>">Ver detalhes</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="ver-todos"><a class="btn btn-secundario" href="/staysharp/produtos">Ver todos os produtos</a></p>
    <?php endif; ?>
</section>
