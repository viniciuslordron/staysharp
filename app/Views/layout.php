<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?? 'Staysharp' ?> | Staysharp</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@5.0.27/bootstrap-4.min.css">
    <link rel="stylesheet" href="/staysharp/public/css/style.css?v=<?= filemtime(__DIR__ . '/../../public/css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.22.5/dist/sweetalert2.all.min.js"></script>
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
                <?php if (isset($_SESSION["usuario"])): ?>

                <?php
                    $nomeUsuario = $_SESSION["usuario"]["nome"];
                    $fotoUsuario = $_SESSION["usuario"]["foto"] ?? "";
                    $inicialUsuario = mb_strtoupper(mb_substr($nomeUsuario, 0, 1));
                ?>
                <div class="perfil">
                    <button type="button" class="perfil-botao" id="perfil-botao" aria-haspopup="true" aria-expanded="false" aria-controls="perfil-menu" title="Minha conta">
                        <span class="avatar">
                            <?php if ($fotoUsuario !== ""): ?>
                                <img src="/staysharp/public/img/<?= htmlspecialchars($fotoUsuario) ?>" alt="Foto de <?= htmlspecialchars($nomeUsuario) ?>">
                            <?php else: ?>
                                <span><?= htmlspecialchars($inicialUsuario) ?></span>
                            <?php endif; ?>
                        </span>
                    </button>

                    <div class="perfil-menu" id="perfil-menu" hidden>
                        <div class="perfil-cabecalho">
                            <span class="avatar avatar-menu">
                                <?php if ($fotoUsuario !== ""): ?>
                                    <img src="/staysharp/public/img/<?= htmlspecialchars($fotoUsuario) ?>" alt="">
                                <?php else: ?>
                                    <span><?= htmlspecialchars($inicialUsuario) ?></span>
                                <?php endif; ?>
                            </span>
                            <strong>Olá, <?= htmlspecialchars(explode(" ", trim($nomeUsuario))[0]) ?>!</strong>
                            <small><?= htmlspecialchars($_SESSION["usuario"]["email"]) ?></small>
                        </div>

                        <div class="perfil-lista">
                            <a href="/staysharp/conta">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.8-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.8 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.8.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.8V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></svg>
                                <span>Gerenciar conta</span>
                                <svg class="seta" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                            </a>
                            <a href="/staysharp/painel">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
                                <span>Painel Administrador</span>
                                <svg class="seta" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                            </a>
                            <a href="/staysharp/logout">
                                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                <span>Sair</span>
                                <svg class="seta" viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <a href="/staysharp/login">Entrar</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if (isset($_SESSION["mensagem"])): ?>
            <?php
                $mensagemAviso = (string) $_SESSION["mensagem"];
                $erroAviso = ($_SESSION["tipo"] ?? "") === "erro";
                unset($_SESSION["mensagem"], $_SESSION["tipo"]);
            ?>
            <noscript><div class="mensagem <?= $erroAviso ? "erro" : "sucesso" ?>"><?= htmlspecialchars($mensagemAviso) ?></div></noscript>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var mensagem = <?= json_encode($mensagemAviso, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

                    <?php if ($erroAviso): ?>
                    // erro: abre o modal e espera o usuario ler
                    Swal.fire({
                        title: "<strong>Atenção</strong>",
                        text: mensagem,
                        icon: "error",
                        confirmButtonText: "Entendi",
                        confirmButtonColor: "#111111"
                    });
                    <?php else: ?>
                    // sucesso: aviso rapido no canto, some sozinho
                    Swal.fire({
                        toast: true,
                        position: "top-end",
                        icon: "success",
                        title: mensagem,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                    <?php endif; ?>
                });
            </script>
        <?php endif; ?>

        <?= $conteudo ?? '' ?>
    </main>

    <footer class="rodape">
        <div class="container">
            <p>Staysharp · Camisas, bonés e adesivos</p>
            <a href="https://www.instagram.com/staysharp.br/" target="_blank">@staysharp.br no Instagram</a>
        </div>
    </footer>

    <script>
        // formularios com data-confirmar pedem confirmacao num modal antes de enviar
        document.addEventListener("submit", function (e) {
            var form = e.target;
            var pergunta = form.getAttribute && form.getAttribute("data-confirmar");
            if (!pergunta) { return; }

            e.preventDefault();
            Swal.fire({
                title: "<strong>Atenção?</strong>",
                text: pergunta,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sim, excluir!",
                cancelButtonText: "Não",
                confirmButtonColor: "#d7263d"
            }).then(function (resposta) {
                if (resposta.isConfirmed) { form.submit(); }
            });
        });
    </script>

    <script>
        (function () {
            var botao = document.getElementById("perfil-botao");
            var menu = document.getElementById("perfil-menu");
            if (!botao || !menu) { return; }

            function alternar(abrir) {
                menu.hidden = !abrir;
                botao.setAttribute("aria-expanded", abrir ? "true" : "false");
            }

            botao.addEventListener("click", function (e) {
                e.stopPropagation();
                alternar(menu.hidden);
            });
            document.addEventListener("click", function (e) {
                if (!menu.hidden && !menu.contains(e.target)) { alternar(false); }
            });
            document.addEventListener("keydown", function (e) {
                if (e.key === "Escape" && !menu.hidden) { alternar(false); botao.focus(); }
            });
        })();
    </script>
</body>
</html>
