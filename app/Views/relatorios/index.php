<div class="cabecalho-pagina">
    <h1>Relatório de estoque</h1>
    <a class="btn" href="/staysharp/relatorios/exportar?status=<?= $status ?>">Exportar CSV</a>
</div>

<h2>Estoque total por categoria</h2>
<table class="tabela tabela-relatorio" id="estoque-categorias">
    <thead>
        <tr><th>Categoria</th><th>Estoque total</th></tr>
    </thead>
    <tbody>
        <?php if (count($categorias) === 0): ?>
            <tr><td colspan="2">Nenhuma categoria cadastrada.</td></tr>
        <?php endif; ?>
        <?php foreach ($categorias as $c): ?>
            <tr>
                <td><?= htmlspecialchars($c["nome"]) ?></td>
                <td><?= $c["estoque_total"] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Estoque dos produtos e tamanhos</h2>
<p>O filtro abaixo e a exportação CSV usam o status de estoque. Os totais por categoria mostram o estoque geral.</p>
<form class="busca" method="GET" action="/staysharp/relatorios">
    <label for="status">Status</label>
    <select name="status" id="status">
        <option value="">Todos</option>
        <option value="em_estoque" <?= $status === "em_estoque" ? "selected" : "" ?>>Em estoque</option>
        <option value="esgotado" <?= $status === "esgotado" ? "selected" : "" ?>>Esgotado</option>
    </select>
    <button class="btn" type="submit">Filtrar</button>
    <a class="btn btn-secundario" href="/staysharp/relatorios">Limpar</a>
</form>

<h3>Por produto</h3>
<table class="tabela tabela-relatorio" id="estoque-produtos">
    <thead>
        <tr><th>Produto</th><th>Categoria</th><th>Estoque total</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php if (count($produtos) === 0): ?>
            <tr><td colspan="4">Nenhum produto encontrado para este status.</td></tr>
        <?php endif; ?>
        <?php foreach ($produtos as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p["nome"]) ?></td>
                <td><?= htmlspecialchars($p["categoria"]) ?></td>
                <td><?= $p["estoque_total"] ?></td>
                <td><?= $p["estoque_total"] > 0 ? "Em estoque" : "Esgotado" ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h3>Por tamanho</h3>
<div class="tabela-scroll">
<table class="tabela tabela-relatorio" id="estoque-tamanhos">
    <thead>
        <tr><th>Produto</th><th>Categoria</th><th>Tamanho</th><th>Estoque</th><th>Status</th></tr>
    </thead>
    <tbody>
        <?php if (count($tamanhos) === 0): ?>
            <tr><td colspan="5">Nenhum tamanho encontrado para este status.</td></tr>
        <?php endif; ?>
        <?php foreach ($tamanhos as $t): ?>
            <tr>
                <td><?= htmlspecialchars($t["produto"]) ?></td>
                <td><?= htmlspecialchars($t["categoria"]) ?></td>
                <td><?= htmlspecialchars($t["tamanho"] ?? "Sem tamanho cadastrado") ?></td>
                <td><?= $t["estoque"] ?></td>
                <td><?= $t["estoque"] > 0 ? "Em estoque" : "Esgotado" ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
