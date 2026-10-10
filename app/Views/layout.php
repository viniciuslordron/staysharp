<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Staysharp'  ?></title>
</head>
<body>
    <header>
        <a href="/staysharp/">Inicio</a>
        <a href="/staysharp/produtos">Produtos</a>
        <a href="/staysharp/categorias">Categorias</a>
    </header>
    <?php if (isset($_SESSION["mensagem"])):?>
        <div class ="mensagem 
        <?= $_SESSION["tipo"] ?? ''?>">
        <?= $_SESSION["mensagem"] ?>
    </div>
    <?php unset($_SESSION["mensagem"], $_SESSION["tipo"]); ?>
    <?php endif; ?>
    <main>
        <?= $conteudo ?>
    </main>
</body>
</html>