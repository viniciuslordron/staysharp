<h1>Categorias</h1>

<ul>
    <?php foreach ($categorias as $c): ?>
        <li><?= $c["nome"] ?></li>
    <?php endforeach; ?>
</ul>

<a href="/staysharp/categorias/cadastrar">Nova categoria</a>