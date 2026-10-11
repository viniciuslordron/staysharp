<h1>Editar Categoria</h1>

<form class="formulario" method="POST" action="/staysharp/categorias/atualizar">
    <input type="hidden" name="id" value="<?= $categoria["id"] ?>">

    <label for="nome">
        Nome
        <input type="text" name="nome" id="nome" value="<?= $categoria["nome"] ?>" required>
    </label>

    <div class="acoes">
        <button class="btn" type="submit">Salvar</button>
        <a class="btn btn-secundario" href="/staysharp/categorias">Voltar</a>
    </div>
</form>
