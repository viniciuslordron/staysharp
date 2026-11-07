<h1>Painel Administrador</h1>
<p>Olá, <?= htmlspecialchars($_SESSION["usuario"]["nome"]) ?>. Escolha o que deseja gerenciar.</p>

<div class="painel-grid">
    <a class="painel-card" href="/staysharp/produtos">
        <span class="painel-numero"><?= $totalProdutos ?></span>
        <strong>Produtos</strong>
        <small>Ver, editar e excluir produtos</small>
    </a>

    <a class="painel-card" href="/staysharp/produtos/cadastrar">
        <span class="painel-numero">+</span>
        <strong>Novo produto</strong>
        <small>Cadastrar fotos, preço e tamanhos</small>
    </a>

    <a class="painel-card" href="/staysharp/categorias">
        <span class="painel-numero"><?= $totalCategorias ?></span>
        <strong>Categorias</strong>
        <small>Organizar camisas, bonés e adesivos</small>
    </a>

    <a class="painel-card" href="/staysharp/relatorios">
        <span class="painel-numero">▤</span>
        <strong>Relatórios</strong>
        <small>Estoque por produto e exportação CSV</small>
    </a>
</div>
