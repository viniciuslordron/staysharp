<h1><?= isset($produto) ? "Editar Produto" : "Novo Produto" ?></h1>

<form class="formulario" method="POST" enctype="multipart/form-data" action="<?= isset($produto) ? "/staysharp/produtos/atualizar" : "/staysharp/produtos/salvar" ?>">
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
        <?php if (!empty($produto["imagem"])): ?>
            <img class="previa-upload" src="/staysharp/public/img/<?= $produto["imagem"] ?>" alt="Imagem atual">
        <?php endif; ?>
        <input type="file" name="imagem" accept="image/jpeg,image/png,image/webp,image/gif">
        <?php if (isset($produto)): ?>
            <small>Escolha um arquivo só se quiser trocar a imagem atual.</small>
        <?php endif; ?>
    </label>

    <label>
        Imagem de costas (opcional)
        <?php if (!empty($produto["imagem_verso"])): ?>
            <img class="previa-upload" src="/staysharp/public/img/<?= $produto["imagem_verso"] ?>" alt="Imagem de costas atual">
        <?php endif; ?>
        <input type="file" name="imagem_verso" accept="image/jpeg,image/png,image/webp,image/gif">
        <small>Aparece quando o cliente passa o mouse sobre o produto.</small>
    </label>

    <?php if (!empty($produto["imagem_verso"])): ?>
        <label class="opcao">
            <input type="checkbox" name="remover_verso" value="1">
            Remover a imagem de costas
        </label>
    <?php endif; ?>

    <button class="btn" type="submit">Salvar</button>
</form>

<a class="btn btn-secundario" href="/staysharp/produtos">Voltar</a>
