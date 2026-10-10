<h1>Categorias</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($categorias as $c): ?>
        <tr>
            <td><?= $c["id"] ?></td>
            <td><?= $c["nome"] ?></td>
            <td>-</td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="/staysharp/categorias/cadastrar">Nova categoria</a>
