# Stay Sharp

Loja virtual da **Stay Sharp** ([@staysharp.br](https://instagram.com/staysharp.br)), com camisas, bonés e adesivos.
Trabalho da disciplina **Programação Web PHP**, feito em **PHP 8 puro** no padrão **MVC** visto em aula.

## Integrantes

- Vinicius Lordron
- Wendell

## Repositório

https://github.com/viniciuslordron/staysharp

## Requisitos

- XAMPP (Apache + PHP 8)
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
3. Inicie o **Apache** no painel do XAMPP.
4. Acesse: **http://localhost/staysharp/**

> O `mod_rewrite` do Apache precisa estar ativo, porque o `.htaccess` manda todas as rotas para o `index.php`.

## Estrutura de pastas

```
staysharp/
├── index.php              # roteador: lê a URL e chama o controller
├── .htaccess              # redireciona tudo para o index.php
├── composer.json          # autoload PSR-4 (App\ -> app/)
├── app/
│   ├── Controllers/       # HomeController, CategoriaController, ProdutoController
│   ├── Data/              # seed.php (dados iniciais) e funcoes.php (proximoId)
│   └── Views/             # render.php, layout.php e as telas
│       ├── home/
│       ├── categorias/
│       └── produtos/
└── public/
    ├── css/style.css
    ├── fonts/
    ├── img/
    └── teste-api.html     # página para testar as APIs
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
| `/reset` | Apaga a sessão e volta aos dados iniciais |

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
| GET | `/api/produtos` | Lista os produtos (aceita `?categoria=`) |
| GET | `/api/produto?id=1` | Mostra um produto |
| POST | `/api/produtos` | Cadastra um produto |
| POST | `/api/produtos/excluir` | Exclui um produto |
| POST | `/api/categorias` | Cadastra uma categoria |
| POST | `/api/categorias/excluir` | Exclui uma categoria |

As APIs respondem com os códigos **200** (ok), **201** (criado), **400** (dado inválido) e **404** (não encontrado).

## Banco de dados fake (`$_SESSION`)

O projeto não usa banco de dados. Os dados ficam guardados na sessão do PHP:

- `$_SESSION["categorias"]`: lista de categorias (`id`, `nome`)
- `$_SESSION["produtos"]`: lista de produtos (`id`, `nome`, `descricao`, `preco`, `categoria_id`, `imagem`)

Como funciona:

- **Seed:** no primeiro acesso, se `$_SESSION["produtos"]` ainda não existe, o `app/Data/seed.php` carrega 3 categorias e 6 produtos de exemplo.
- **Relacionamento:** cada produto aponta para a sua categoria pelo campo `categoria_id`.
- **Próximo id:** a função `proximoId()` pega o maior id da lista e soma 1 (`max + 1`).
- **Reset:** a rota `/reset` limpa a sessão e carrega o seed de novo.

> Os dados duram enquanto a sessão estiver aberta. Se o navegador for fechado ou os cookies forem limpos, tudo volta ao seed.

## Validações

- O nome não pode ficar vazio (produto e categoria).
- O preço tem que ser maior que 0.
- O produto precisa ter uma categoria que exista.
- Uma categoria que ainda tem produtos não pode ser excluída.
- Um id que não existe mostra "não encontrado" (404), sem erro do PHP.
- O texto digitado pelo usuário passa por `htmlspecialchars` antes de aparecer na tela.

## Como testar as APIs

Abra **http://localhost/staysharp/public/teste-api.html**. Lá tem links para os GET e formulários para os POST, e a resposta em JSON abre numa aba nova.

> Use o mesmo navegador do site: o Postman tem uma sessão separada e não enxerga os dados que você cadastrou pelo navegador.
