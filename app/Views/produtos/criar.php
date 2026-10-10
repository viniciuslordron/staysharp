<h1>Novo Produto</h1>

<form class="formulario" method="POST" action="/staysharp/produtos/salvar">
    <label>
        Nome
        <input type="text" name="nome" required>
    </label>

    <label>
        Descrição
        <textarea name="descricao"></textarea>
    </label>

    <label>
        Preço
        <input type="number" name="preco" step="0.01" required>
    </label>

    <label>
        Categoria
        <select name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria["id"] ?>"><?= $categoria["nome"] ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Imagem
        <input type="text" name="imagem" placeholder="Nome do arquivo em public/img/">
    </label>

    <button class="btn" type="submit">Salvar</button>
</form>

<a class="btn btn-secundario" href="/staysharp/produtos">Voltar</a>
