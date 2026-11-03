# Stay Sharp

Loja virtual da **Stay Sharp** ([@staysharp.br](https://instagram.com/staysharp.br)), com camisas, bonés e adesivos.
Trabalho da disciplina **Programação Web PHP**, feito em **PHP 8 puro** no padrão **MVC** visto em aula.

## Integrantes

- Vinicius Lordron - 6º termo
- Wendell Ramos - 6º termo

Professor: Adriel Oliveira<br>
Disciplina: Programação Web PHP<br>
Tema: Stay Sharp - gerenciamento de produtos e categorias

## Repositório

https://github.com/viniciuslordron/staysharp

## Requisitos

- XAMPP (Apache, MySQL/MariaDB e PHP 8.2 ou superior)
- Extensão `pdo_mysql` habilitada no PHP
- Composer

## Como rodar

1. Clone o projeto dentro do `htdocs`:
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/viniciuslordron/staysharp.git staysharp
   ```
2. Entre na pasta e gere o autoload:
   ```
   cd staysharp
   composer install
   ```
3. Inicie o **Apache** e o **MySQL** no painel do XAMPP.
4. Abra **http://localhost/phpmyadmin/** e importe o `database/schema.sql` na aba **Importar**. Ele cria o banco `staysharp` com charset `utf8mb4`, as tabelas e a chave estrangeira.
5. Selecione o banco `staysharp` e importe o `database/seed.sql` para carregar 3 categorias e 6 produtos de exemplo. Importe os scripts uma vez, em uma instalação nova. Se já possui dados, não é necessário importá-los novamente.
6. Confira as configurações de conexão no arquivo `config.php`, na raiz do projeto, conforme seu MySQL local:
   ```php
   return [
       'host' => '127.0.0.1',
       'db' => 'staysharp',
       'user' => 'root',
       'pass' => '',
       'charset' => 'utf8mb4'
   ];
   ```
   Se o usuário do banco tiver senha, preencha `pass`.
7. Acesse: **http://localhost/staysharp/**

> O `mod_rewrite` do Apache precisa estar ativo, porque o `.htaccess` manda todas as rotas para o `index.php`.

## Login da administração

A vitrine e as APIs de consulta são públicas. Para cadastrar, editar ou excluir produtos, categorias e tamanhos, entre em **http://localhost/staysharp/login**.

Usuário de exemplo do `database/seed.sql`:

- E-mail: `admin@teste.com`
- Senha: `123456`

O menu mostra **Entrar** sem sessão e **Sair (nome)** após o login. A rota `/logout` encerra a sessão.

Se a tabela `usuarios` já foi criada com a coluna antiga `usuario`, renomeie essa coluna antes de usar o login:

```sql
ALTER TABLE usuarios CHANGE usuario email VARCHAR(150) NOT NULL;
```

Em um banco existente, adicione somente a tabela `usuarios` do schema e o usuário de exemplo do seed caso ainda não existam, sem importar novamente os produtos e as categorias.

## Relatório de estoque

Após entrar, use o menu **Relatórios** ou acesse `/relatorios`. A página mostra o total por categoria e o estoque por produto e tamanho, incluindo produtos sem tamanhos cadastrados com estoque zero.

O filtro **Todos / Em estoque / Esgotado** é aplicado às listas e ao botão **Exportar CSV**. Os totais por categoria mostram sempre o estoque geral. A rota `/relatorios/exportar` gera um arquivo UTF-8 separado por ponto e vírgula, com as colunas Produto, Categoria, Tamanho, Estoque e Status, para abrir no Excel.

As duas rotas exigem login.

## Estrutura de pastas

```
staysharp/
├── index.php              # roteador: lê a URL e chama o controller
├── .htaccess              # redireciona tudo para o index.php
├── composer.json          # autoload PSR-4 (App\ -> app/)
├── config.php             # configurações de conexão com o banco
├── database/
│   ├── schema.sql         # criação do banco, tabelas e FK
│   └── seed.sql           # dados de exemplo
├── app/
│   ├── Config/            # Database.php (conexão PDO)
│   ├── Controllers/       # HomeController, CategoriaController, ProdutoController
│   ├── Models/            # CategoriaModel e ProdutoModel (acesso a dados, SQL)
│   └── Views/             # render.php, layout.php e as telas
│       ├── home/
│       ├── categorias/
│       └── produtos/
└── public/
    ├── css/style.css
    ├── fonts/
    └── img/
```

## Rotas

### Telas (GET)

| Rota | O que faz |
|---|---|
| `/` | Página inicial |
| `/produtos` | Lista de produtos (filtros `?categoria=` e `?busca=`) |
| `/produto?id=1` | Detalhe do produto, com botão de pedido pelo WhatsApp |
| `/produtos/cadastrar` | Formulário de novo produto |
| `/produtos/editar?id=1` | Formulário de edição de produto |
| `/categorias` | Lista de categorias |
| `/categorias/cadastrar` | Formulário de nova categoria |
| `/categorias/editar?id=1` | Formulário de edição de categoria |

### Formulários (POST)

| Rota | O que faz |
|---|---|
| `/produtos/salvar` | Cadastra um produto |
| `/produtos/atualizar` | Atualiza um produto |
| `/produtos/excluir` | Exclui um produto |
| `/categorias/salvar` | Cadastra uma categoria |
| `/categorias/atualizar` | Atualiza uma categoria |
| `/categorias/excluir` | Exclui uma categoria |

### APIs (JSON)

| Método | Rota | O que faz |
|---|---|---|
| GET | `/api/categorias` | Lista as categorias |
| GET | `/api/produtos` | Lista os produtos (aceita `?categoria=` e `?busca=`) |
| GET | `/api/produto?id=1` | Mostra um produto |
| POST | `/api/produtos` | Cadastra um produto |
| POST | `/api/produtos/excluir` | Exclui um produto |
| POST | `/api/categorias` | Cadastra uma categoria |
| POST | `/api/categorias/excluir` | Exclui uma categoria |

As APIs respondem com os códigos **200** (ok), **201** (criado), **400** (dado inválido) e **404** (não encontrado).

## Banco de dados MySQL + PDO

Os dados ficam no banco `staysharp`. A classe `app/Config/Database.php` lê as configurações do `config.php` da raiz e cria uma conexão PDO, seguindo a aula 9.

- `categorias`: `id`, `nome`.
- `produtos`: `id`, `nome`, `descricao`, `preco`, `categoria_id`, `imagem`, `imagem_verso`.

O `categoria_id` é uma chave estrangeira que aponta para `categorias.id`. Os IDs são gerados pelo `AUTO_INCREMENT`.

Os Models executam o CRUD com `prepare()` e `execute()`. O `ProdutoModel` usa `JOIN` para consultar o nome da categoria junto com o produto. Os controllers mantêm as mesmas rotas e views da primeira etapa, incluindo a página inicial.

Os produtos e categorias continuam salvos depois de fechar o navegador. A sessão permanece apenas para as mensagens de sucesso e erro. O carregamento do seed em PHP, a função `proximoId()` e a rota `/reset` foram removidos; os dados iniciais vêm do `database/seed.sql`.

As imagens enviadas pelo formulário ficam em `public/img/produtos/`. O banco guarda os caminhos das imagens de frente e verso.

## Validações

- O nome não pode ficar vazio (produto e categoria).
- O preço tem que ser maior que 0.
- O produto precisa ter uma categoria que exista.
- Uma categoria que ainda tem produtos não pode ser excluída.
- Um id que não existe mostra "não encontrado" (404), sem erro do PHP.
- O upload aceita JPG, PNG, WEBP e GIF de até 5 MB; a imagem de costas é opcional.

## Como testar as APIs

**GET:** abra direto no navegador, por exemplo:

- http://localhost/staysharp/api/categorias
- http://localhost/staysharp/api/produtos
- http://localhost/staysharp/api/produtos?categoria=1
- http://localhost/staysharp/api/produtos?categoria=1&busca=Camisa
- http://localhost/staysharp/api/produto?id=1
- http://localhost/staysharp/api/produto?id=99 (retorna 404)

**POST:** use o Postman (ou Insomnia), com o Body em `x-www-form-urlencoded`:

| Rota | Campos |
|---|---|
| `POST /api/produtos` | `nome`, `preco`, `categoria_id`; opcionais: `descricao`, `imagem`, `imagem_verso` |
| `POST /api/produtos/excluir` | `id` |
| `POST /api/categorias` | `nome` |
| `POST /api/categorias/excluir` | `id` |

O Postman e o navegador consultam o mesmo banco. Um produto criado pela API também aparece nas telas. Na API, os campos de imagem recebem os caminhos; o upload dos arquivos é feito pelo formulário.

## Testes e entrega da Avaliação III

O roteiro e os resultados da conferência estão em [docs/testes-avaliacao-iii.md](docs/testes-avaliacao-iii.md).

Os prints atuais ficam em `docs/prints/avaliacao-iii/` e o PDF em [docs/avaliacao-iii-staysharp.pdf](docs/avaliacao-iii-staysharp.pdf).

A documentação também inclui capturas do quadro do Trello e do histórico de pull requests do GitHub.

O projeto foi organizado pelo Trello, com uma branch e um pull request para cada cartão. O Termo de entrega será finalizado pelo grupo separadamente.
