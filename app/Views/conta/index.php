<?php
    $foto = $usuario["foto"] ?? "";
    $inicial = mb_strtoupper(mb_substr($usuario["nome"], 0, 1));
?>
<h1>Gerenciar conta</h1>
<p>Atualize a foto e o nome que aparecem no menu da administração.</p>

<form class="formulario formulario-conta" method="POST" enctype="multipart/form-data" action="/staysharp/conta/atualizar">
    <div class="conta-foto">
        <span class="avatar avatar-xl" id="avatar-previa">
            <img id="avatar-img" alt="Sua foto" <?= $foto !== "" ? 'src="/staysharp/public/img/' . htmlspecialchars($foto) . '"' : "hidden" ?>>
            <span id="avatar-inicial" <?= $foto !== "" ? "hidden" : "" ?>><?= htmlspecialchars($inicial) ?></span>
        </span>

        <div class="conta-foto-acoes">
            <label class="btn btn-secundario conta-escolher">
                Escolher foto
                <input type="file" name="foto" id="campo-foto" accept="image/jpeg,image/png,image/webp,image/gif" hidden>
            </label>
            <?php if ($foto !== ""): ?>
                <label class="opcao">
                    <input type="checkbox" name="remover_foto" id="campo-remover-foto" value="1">
                    Remover a foto atual
                </label>
            <?php endif; ?>
            <small>JPG, PNG, WEBP ou GIF, até 5 MB.</small>
        </div>
    </div>

    <label>
        Nome da conta
        <input type="text" name="nome" id="campo-nome-conta" maxlength="100" value="<?= htmlspecialchars($usuario["nome"]) ?>" required>
    </label>

    <label>
        E-mail
        <input type="email" value="<?= htmlspecialchars($usuario["email"]) ?>" disabled>
        <small>O e-mail não pode ser alterado por enquanto.</small>
    </label>

    <div class="form-botoes">
        <button class="btn" type="submit">Salvar alterações</button>
        <a class="btn btn-secundario" href="/staysharp/painel">Voltar</a>
    </div>
</form>

<script>
    (function () {
        var campo = document.getElementById("campo-foto");
        var img = document.getElementById("avatar-img");
        var inicial = document.getElementById("avatar-inicial");
        var remover = document.getElementById("campo-remover-foto");
        var nome = document.getElementById("campo-nome-conta");

        campo.addEventListener("change", function () {
            var arquivo = campo.files[0];
            if (!arquivo) { return; }

            if (arquivo.size > 5 * 1024 * 1024) {
                alert("A imagem deve ter no máximo 5 MB");
                campo.value = "";
                return;
            }

            img.src = URL.createObjectURL(arquivo);
            img.hidden = false;
            inicial.hidden = true;
            if (remover) { remover.checked = false; }
        });

        // sem foto, o círculo mostra a inicial do nome que esta sendo digitado
        nome.addEventListener("input", function () {
            inicial.textContent = (nome.value.trim().charAt(0) || "?").toUpperCase();
        });

        if (remover) {
            remover.addEventListener("change", function () {
                if (!remover.checked) { return; }
                campo.value = "";
                img.hidden = true;
                inicial.hidden = false;
            });
        }
    })();
</script>
