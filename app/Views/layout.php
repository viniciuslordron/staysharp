<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Staysharp' ?> | Staysharp</title>
    <link rel="stylesheet" href="/staysharp/public/css/style.css">
</head>
<body>
    <header class="topo">
        <div class="container topo-conteudo">
            <a class="logo" href="/staysharp/">STAYSHARP</a>
            <nav class="menu">
                <a href="/staysharp/">Início</a>
                <a href="/staysharp/produtos">Produtos</a>
                <a href="/staysharp/produtos?categoria=1">Camisas</a>
                <a href="/staysharp/produtos?categoria=2">Bonés</a>
                <a href="/staysharp/produtos?categoria=3">Adesivos</a>
                <a href="/staysharp/categorias">Categorias</a>
                <a class="menu-destaque" href="/staysharp/produtos/cadastrar">+ Novo produto</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION["mensagem"])): ?>
            <div class="mensagem <?= $_SESSION["tipo"] ?? '' ?>">
                <?= $_SESSION["mensagem"] ?>
            </div>
            <?php unset($_SESSION["mensagem"], $_SESSION["tipo"]); ?>
        <?php endif; ?>

        <?= $conteudo ?? '' ?>
    </main>

    <footer class="rodape">
        <div class="container">
            <p>Staysharp · Camisas, bonés e adesivos</p>
            <a href="https://www.instagram.com/staysharp.br/" target="_blank">@staysharp.br no Instagram</a>
        </div>
    </footer>
</body>
</html>