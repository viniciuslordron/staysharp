<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Staysharp' ?> | Staysharp</title>
    <link rel="stylesheet" href="/staysharp/public/css/style.css">
</head>
<body>
    <?php
        // descobre a página atual para marcar no menu com o ♠
        $paginaAtual = strtok($_SERVER["REQUEST_URI"], "?");
        $naListagem = str_ends_with(rtrim($paginaAtual, "/"), "/produtos");
        $categoriaAtual = $_GET["categoria"] ?? "";
    ?>
    <header class="topo">
        <div class="container topo-conteudo">
            <a class="logo" href="/staysharp/">
                <img src="/staysharp/public/img/logo-staysharp.png" alt="Stay Sharp">
            </a>

            <nav class="patch" aria-label="Categorias">
                <a href="/staysharp/produtos" <?= $naListagem && $categoriaAtual === "" ? 'aria-current="page"' : '' ?>>Todos</a>
                <a href="/staysharp/produtos?categoria=1" <?= $naListagem && $categoriaAtual === "1" ? 'aria-current="page"' : '' ?>>Camisas</a>
                <a href="/staysharp/produtos?categoria=2" <?= $naListagem && $categoriaAtual === "2" ? 'aria-current="page"' : '' ?>>Bonés</a>
                <a href="/staysharp/produtos?categoria=3" <?= $naListagem && $categoriaAtual === "3" ? 'aria-current="page"' : '' ?>>Adesivos</a>
            </nav>

            <nav class="menu-admin" aria-label="Gerenciar loja">
                <a href="/staysharp/categorias">Categorias</a>
                <a href="/staysharp/produtos/cadastrar">+ Produto</a>
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