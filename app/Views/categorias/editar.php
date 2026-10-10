<h1>Editar Categoria</h1>

<form method="POST" action="/staysharp/categorias/atualizar">
    <input type="hidden" name="id" value="<?= $categoria["id"] ?>">

    <label for="nome">Nome</label>
    <input type="text" name="nome" id="nome" value="<?= $categoria["nome"] ?>" required>

    <button type="submit">Salvar</button>
</form>

<a href="/staysharp/categorias">Voltar</a>
