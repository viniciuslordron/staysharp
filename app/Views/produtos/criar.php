<h1><?= isset($produto) ? "Editar Produto" : "Novo Produto" ?></h1>

<form class="formulario" method="POST" action="<?= isset($produto) ? "/staysharp/produtos/atualizar" : "/staysharp/produtos/salvar" ?>">
    <?php if (isset($produto)): ?>
        <input type="hidden" name="id" value="<?= $produto["id"] ?>">
    <?php endif; ?>

    <label>
        Nome
        <input type="text" name="nome" value="<?= $produto["nome"] ?? "" ?>" required>
    </label>

    <label>
        Descrição
        <textarea name="descricao"><?= $produto["descricao"] ?? "" ?></textarea>
    </label>

    <label>
        Preço
        <input type="number" name="preco" step="0.01" value="<?= $produto["preco"] ?? "" ?>" required>
    </label>

    <label>
        Categoria
        <select name="categoria_id" required>
            <option value="">Selecione uma categoria</option>
            <?php foreach ($categorias as $categoria): ?>
                <option value="<?= $categoria["id"] ?>" <?= isset($produto) && $produto["categoria_id"] == $categoria["id"] ? "selected" : "" ?>>
                    <?= $categoria["nome"] ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Imagem
        <input type="text" name="imagem" value="<?= $produto["imagem"] ?? "" ?>" placeholder="Nome do arquivo em public/img/">
    </label>

    <button class="btn" type="submit">Salvar</button>
</form>

<a class="btn btn-secundario" href="/staysharp/produtos">Voltar</a>
