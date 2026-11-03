<h1>Entrar</h1>
<p>Entre para gerenciar os produtos, categorias e tamanhos da loja.</p>

<form class="formulario" method="POST" action="/staysharp/login">
    <label for="email">
        E-mail
        <input type="email" name="email" id="email" placeholder="seuemail@exemplo.com" autocomplete="username" required>
    </label>
    <label for="senha">
        Senha
        <input type="password" name="senha" id="senha" placeholder="Informe sua senha" autocomplete="current-password" required>
    </label>
    <div class="acoes">
        <button class="btn" type="submit">Entrar</button>
        <a class="btn btn-secundario" href="/staysharp/produtos">Voltar para a loja</a>
    </div>
</form>
