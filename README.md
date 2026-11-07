# Stay Sharp

Aplicação web para gerenciar o catálogo e o estoque da **Stay Sharp**, loja de camisas, bonés e adesivos. A vitrine permite buscar produtos, consultar tamanhos disponíveis e iniciar um pedido pelo WhatsApp. A administração permite cadastrar produtos e categorias, controlar o estoque por tamanho e consultar relatórios.

Trabalho da disciplina **Programação Web PHP**, desenvolvido em PHP no padrão MVC visto em aula, com MySQL e PDO.

## Integrantes

- Vinicius Lordron - 6º termo
- Wendell Ramos - 6º termo

Professor: Adriel Oliveira<br>
Disciplina: Programação Web PHP

## Requisitos

- PHP 8.2 ou superior, conforme o `composer.json`.
- Extensões `pdo_mysql` e `mbstring` habilitadas no PHP.
- MySQL ou MariaDB.
- Apache com `mod_rewrite` habilitado e suporte ao `.htaccess`.
- Composer e Git.
- Navegador.

O projeto pode ser executado no XAMPP, que reúne Apache, PHP e MariaDB.

## Como rodar

### 1. Clonar o projeto

No terminal, dentro do `htdocs` do XAMPP:

```powershell
cd C:\xampp\htdocs
git clone https://github.com/viniciuslordron/staysharp.git staysharp
cd staysharp
composer install
```

O Composer gera o autoload usado pelo `index.php`.

### 2. Iniciar o XAMPP

Abra o painel do XAMPP e inicie **Apache** e **MySQL**.

### 3. Criar e preencher o banco

O arquivo `database/seed.sql` faz tudo de uma vez: apaga o banco `staysharp` (se existir), recria o banco, as quatro tabelas e as chaves estrangeiras, e insere 3 categorias, 6 produtos, 10 registros de tamanhos e um usuário de teste.

No **MySQL Workbench**, conecte ao MySQL local, abra o `database/seed.sql` e execute o script inteiro.

Também é possível importar pelo **phpMyAdmin**, em `http://localhost/phpmyadmin/`, na aba **Importar**, escolhendo o `database/seed.sql`.

O `database/schema.sql` continua disponível para criar só a estrutura, sem dados.

Os scripts são para uma instalação nova. Se já possui dados, não importe tudo novamente.

### 4. Configurar a conexão

Edite o `config.php`, na raiz, conforme seu banco local:

```php
<?php
    return [
        'host' => '127.0.0.1',
        'db' => 'staysharp',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ];
?>
```

`user` e `pass` são as credenciais do MySQL. Se o usuário do banco tiver senha, preencha `pass`.

### 5. Abrir o sistema

Acesse **http://localhost/staysharp/**.

O projeto deve ficar na pasta `staysharp`, pois as rotas e os arquivos públicos usam esse caminho. O `.htaccess` encaminha as requisições para o `index.php`.

## Usuário de teste

O `database/seed.sql` cria este acesso à administração:

| Nome | E-mail | Senha |
|---|---|---|
| Admin | `admin@teste.com` | `123456` |

Entre em **http://localhost/staysharp/login**. Esse acesso é da aplicação, separado das credenciais do MySQL no `config.php`.

A senha fica armazenada como hash e é conferida com `password_verify()`. A sessão guarda o usuário autenticado e as mensagens de sucesso e erro. O menu mostra **Entrar** sem login e **Sair (nome)** após entrar.

A vitrine e as consultas da API são públicas. O gerenciamento de categorias, produtos e tamanhos, os relatórios e as APIs de escrita exigem login. Sem sessão, as rotas protegidas redirecionam para `/login`.

## Estrutura do projeto

```text
staysharp/
├── index.php              # roteamento e proteção das rotas
├── .htaccess
├── composer.json
├── config.php             # credenciais do banco
├── database/
│   ├── schema.sql
│   └── seed.sql
├── app/
│   ├── Config/            # conexão PDO
│   ├── Controllers/       # produtos, categorias, tamanhos, login e relatórios
│   ├── Models/            # consultas e alterações no banco
│   └── Views/             # layout e telas do sistema
└── public/
    ├── css/
    ├── fonts/
    └── img/
```

## Banco de dados

| Tabela | Campos |
|---|---|
| `categorias` | `id`, `nome` |
| `produtos` | `id`, `nome`, `descricao`, `preco`, `categoria_id`, `imagem`, `imagem_verso` |
| `tamanhos` | `id`, `produto_id`, `tamanho`, `estoque` |
| `usuarios` | `id`, `nome`, `email`, `senha`, `foto` |

Uma categoria possui vários produtos, e um produto pode ter vários tamanhos. As chaves estrangeiras são `produtos.categoria_id` e `tamanhos.produto_id`. O mesmo tamanho não pode ser repetido no mesmo produto. Ao excluir um produto, seus tamanhos também são excluídos.

Os Models usam PDO com `prepare()`, `execute()` e `PDO::FETCH_ASSOC`. As imagens enviadas pelos formulários ficam em `public/img/produtos/`, e o banco guarda seus caminhos.

## Rotas principais

Todas as rotas abaixo usam o prefixo `http://localhost/staysharp`.

### Web - GET

| Rota | Função | Exige login |
|---|---|---|
| `/` | Página inicial | Não |
| `/produtos` | Listagem com busca e filtro por categoria | Não |
| `/produto?id=1` | Detalhe, tamanhos, estoque e pedido pelo WhatsApp | Não |
| `/login` | Tela de login | Não |
| `/logout` | Encerra a sessão | Não |
| `/painel` | Painel Administrador, com atalhos para produtos, categorias e relatórios | Sim |
| `/conta` | Gerenciar conta: trocar a foto e o nome do perfil | Sim |
| `/categorias` | Listagem de categorias | Sim |
| `/categorias/cadastrar` | Cadastro de categoria | Sim |
| `/categorias/editar?id=1` | Edição de categoria | Sim |
| `/produtos/cadastrar` | Cadastro de produto, com fotos, tamanhos e prévia do anúncio | Sim |
| `/produtos/editar?id=1` | Edição do produto, das fotos e dos tamanhos | Sim |
| `/relatorios` | Relatório de estoque | Sim |
| `/relatorios/exportar` | Download do estoque em CSV | Sim |

Exemplos de busca e filtros:

- `/produtos?busca=Camisa`
- `/produtos?categoria=1&busca=Camisa`
- `/produto?id=1&tamanho=M`
- `/relatorios?status=em_estoque`
- `/relatorios?status=esgotado`

### Web - POST

| Rota | Função | Exige login |
|---|---|---|
| `/login` | Autentica com `email` e `senha` | Não |
| `/conta/atualizar` | Atualiza o nome e a foto do perfil | Sim |
| `/categorias/salvar` | Cadastra categoria | Sim |
| `/categorias/atualizar` | Atualiza categoria | Sim |
| `/categorias/excluir` | Exclui categoria sem produtos | Sim |
| `/produtos/salvar` | Cadastra produto com upload de imagem e seus tamanhos | Sim |
| `/produtos/atualizar` | Atualiza produto e seus tamanhos | Sim |
| `/produtos/excluir` | Exclui produto e seus tamanhos | Sim |
| `/tamanhos/salvar` | Adiciona tamanho e estoque ao produto | Sim |
| `/tamanhos/atualizar` | Atualiza o estoque de um tamanho | Sim |
| `/tamanhos/excluir` | Exclui um tamanho | Sim |

### API JSON

As respostas da API usam `Content-Type: application/json; charset=UTF-8`.

| Método | Rota | Função | Exige login |
|---|---|---|---|
| GET | `/api/categorias` | Lista categorias | Não |
| GET | `/api/produtos` | Lista produtos, com filtros `categoria` e `busca` | Não |
| GET | `/api/produto?id=1` | Detalha um produto com seus tamanhos | Não |
| POST | `/api/categorias` | Cadastra categoria | Sim |
| POST | `/api/categorias/excluir` | Exclui categoria | Sim |
| POST | `/api/produtos` | Cadastra produto | Sim |
| POST | `/api/produtos/excluir` | Exclui produto | Sim |

Os retornos usam **200** para sucesso, **201** para cadastro, **400** para dados inválidos, **404** para registro não encontrado, **500** para falha de gravação e **503** para indisponibilidade de conexão ou consulta. Sem login, as rotas de escrita redirecionam para `/login`.

## Como testar a API

As consultas podem ser abertas no navegador:

```text
http://localhost/staysharp/api/produtos
http://localhost/staysharp/api/produtos?categoria=1&busca=Camisa
http://localhost/staysharp/api/produto?id=1
```

Para as operações POST no Postman ou Insomnia:

1. Envie `POST http://localhost/staysharp/login`, com o Body em `x-www-form-urlencoded`, usando `email=admin@teste.com` e `senha=123456`.
2. Mantenha o cookie `PHPSESSID` recebido nesse cliente nas próximas requisições.
3. Envie os campos da operação também em `x-www-form-urlencoded`:

| Rota | Campos |
|---|---|
| `POST /api/categorias` | `nome` |
| `POST /api/categorias/excluir` | `id` |
| `POST /api/produtos` | `nome`, `preco`, `categoria_id`; opcionais: `descricao`, `imagem`, `imagem_verso` |
| `POST /api/produtos/excluir` | `id` |

Exemplo de cadastro de produto: `nome=Camisa exemplo`, `preco=59.90` e `categoria_id=1`.

O login feito no navegador não autentica automaticamente o Postman ou o Insomnia. Na API, os campos de imagem recebem caminhos; o upload do arquivo é feito pelo formulário. Os dados cadastrados pela API também aparecem nas telas.

## Estoque e relatórios

Os tamanhos e o estoque são preenchidos no próprio formulário do produto (cadastro e edição) e salvos junto com ele, no botão **Salvar produto**. Há botões rápidos para P, M, G, GG e Único, além de um campo para outro tamanho, e a prévia do anúncio mostra o resultado. O estoque deve ser inteiro e maior ou igual a zero. Um produto sem tamanhos ou com estoque total zero aparece como esgotado na vitrine. No seed, os adesivos começam sem tamanhos cadastrados.

O relatório mostra o total geral por categoria e a lista de produtos, com a coluna **Tamanhos** (por exemplo `P: 10 · M: 15 · G: 8`), o estoque total e o status. O filtro **Todos / Em estoque / Esgotado** considera o estoque total do produto e é aplicado à lista e ao CSV. Os totais por categoria continuam mostrando o estoque geral.

O botão **Exportar CSV** baixa `estoque-staysharp.csv`, com uma linha por tamanho, em UTF-8 com BOM e separado por ponto e vírgula, com Produto, Categoria, Tamanho, Estoque e Status. Para importar no Excel, selecione UTF-8 e ponto e vírgula caso as colunas não sejam reconhecidas automaticamente.

## Validações e mensagens

- Nomes obrigatórios, com limite de 100 caracteres para categoria e 150 para produto.
- Preço numérico maior que zero, com até duas casas decimais e limite de 99999999.99.
- Categoria e produto precisam existir antes de salvar seus relacionamentos.
- Tamanho obrigatório, com até 10 caracteres, sem repetição no mesmo produto.
- Estoque inteiro entre 0 e 2147483647.
- Categoria com produtos vinculados não pode ser excluída.
- Upload de JPG, PNG, WEBP ou GIF de até 5 MB; a foto de costas é opcional.

Os formulários mostram mensagens de sucesso ou erro. As falhas do banco são tratadas com `PDOException`; quando a conexão ou uma consulta fica indisponível, o sistema apresenta uma tela amigável ou um erro JSON nas rotas da API.

## Organização do trabalho

As tarefas foram organizadas no [Trello](https://trello.com/b/wX4a81CO/staysharp), usando uma branch por cartão e pull requests para revisão no [GitHub](https://github.com/viniciuslordron/staysharp).

A documentação da etapa anterior permanece em [docs/testes-avaliacao-iii.md](docs/testes-avaliacao-iii.md) e [docs/avaliacao-iii-staysharp.pdf](docs/avaliacao-iii-staysharp.pdf).
