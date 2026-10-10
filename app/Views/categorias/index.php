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
            <td>
                <a href="/staysharp/categorias/editar?id=<?= $c["id"] ?>">Editar</a>

                <form method="POST" action="/staysharp/categorias/excluir" onsubmit="return confirm('Excluir?')">
                    <input type="hidden" name="id" value="<?= $c["id"] ?>">
                    <button type="submit">Excluir</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<a href="/staysharp/categorias/cadastrar">Nova categoria</a>
