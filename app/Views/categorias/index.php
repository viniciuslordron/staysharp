<div class="cabecalho-pagina">
    <h1>Categorias</h1>
    <a class="btn" href="/staysharp/categorias/cadastrar">+ Nova categoria</a>
</div>

<table class="tabela tabela-fixa">
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th>Nome</th>
            <th class="col-acoes">Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($categorias as $c): ?>
        <tr>
            <td class="col-id"><?= $c["id"] ?></td>
            <td><?= $c["nome"] ?></td>
            <td class="col-acoes">
                <div class="menu-acoes">
                    <button class="menu-acoes-botao" type="button" aria-label="Ações" aria-haspopup="true" aria-expanded="false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <circle cx="12" cy="5" r="2"></circle>
                            <circle cx="12" cy="12" r="2"></circle>
                            <circle cx="12" cy="19" r="2"></circle>
                        </svg>
                    </button>

                    <div class="menu-acoes-lista" hidden>
                        <a href="/staysharp/categorias/editar?id=<?= $c["id"] ?>">Editar</a>
                        <form method="POST" action="/staysharp/categorias/excluir" onsubmit="return confirm('Excluir?')">
                            <input type="hidden" name="id" value="<?= $c["id"] ?>">
                            <button class="excluir" type="submit">Excluir</button>
                        </form>
                    </div>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<script>
    (function () {
        function fechar() {
            document.querySelectorAll(".menu-acoes-lista").forEach(function (l) {
                l.hidden = true;
                l.previousElementSibling.setAttribute("aria-expanded", "false");
            });
        }

        document.querySelectorAll(".menu-acoes-botao").forEach(function (b) {
            b.addEventListener("click", function (e) {
                e.stopPropagation();
                var lista = b.nextElementSibling;
                var abrir = lista.hidden;
                fechar();
                lista.hidden = !abrir;
                b.setAttribute("aria-expanded", abrir ? "true" : "false");
            });
        });

        document.addEventListener("click", fechar);
        document.addEventListener("keydown", function (e) {
            if (e.key === "Escape") fechar();
        });
    })();
</script>
