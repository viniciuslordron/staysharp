<section id="tamanhos" class="gerenciar-tamanhos">
    <h2>Tamanhos</h2>
    <p>Cadastre os tamanhos disponíveis e informe o estoque de cada um.</p>

    <table class="tabela">
        <thead>
            <tr>
                <th>Tamanho</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($tamanhos) === 0): ?>
                <tr><td colspan="3">Nenhum tamanho cadastrado para este produto.</td></tr>
            <?php endif; ?>

            <?php foreach ($tamanhos as $t): ?>
                <tr>
                    <td><?= htmlspecialchars($t["tamanho"]) ?></td>
                    <td>
                        <form class="estoque-form" method="POST" action="/staysharp/tamanhos/atualizar">
                            <input type="hidden" name="id" value="<?= $t["id"] ?>">
                            <label for="estoque-<?= $t["id"] ?>">Quantidade</label>
                            <input type="number" name="estoque" id="estoque-<?= $t["id"] ?>" value="<?= $t["estoque"] ?>" min="0" max="2147483647" step="1" required>
                            <button class="btn" type="submit">Salvar</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" action="/staysharp/tamanhos/excluir" onsubmit="return confirm('Excluir este tamanho?')">
                            <input type="hidden" name="id" value="<?= $t["id"] ?>">
                            <button class="btn btn-perigo" type="submit">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Adicionar tamanho</h3>
    <form class="formulario" method="POST" action="/staysharp/tamanhos/salvar">
        <input type="hidden" name="produto_id" value="<?= $produto["id"] ?>">
        <div class="linha-campos">
            <label for="novo-tamanho">
                Tamanho
                <input type="text" name="tamanho" id="novo-tamanho" maxlength="10" placeholder="Ex.: P, M, G, GG ou Único" required>
            </label>
            <label for="novo-estoque">
                Estoque
                <input type="number" name="estoque" id="novo-estoque" min="0" max="2147483647" step="1" value="0" required>
            </label>
        </div>
        <button class="btn" type="submit">Adicionar tamanho</button>
    </form>
</section>
