# Testes da Avaliação III

Data: 28/10/2026<br>
Projeto: Stay Sharp<br>
Disciplina: Programação Web PHP<br>
Professor: Adriel Oliveira<br>
Integrantes: Vinicius Lordron e Wendell Ramos - 6º termo

## Ambiente

- Apache 2.4.58 e PHP 8.2.12 pelo XAMPP.
- MariaDB 10.4.32, compatível com o SQL usado no projeto, e conexão PDO MySQL.
- Banco `staysharp`, charset `utf8mb4`.
- Endereço: `http://localhost/staysharp/`.
- Conferência visual dos registros no phpMyAdmin.

## Telas e persistência

| Teste | Resultado conferido |
|---|---|
| Página inicial | Categorias e os quatro primeiros produtos consultados no banco |
| Busca e filtro | Busca por Camisa e categoria 1 retornam apenas as camisas |
| Detalhe | Nome, preço, categoria e imagens exibidos |
| Cadastrar categoria | Nova categoria aparece na listagem e na API |
| Editar categoria | Nome atualizado nas telas, na API e no phpMyAdmin |
| Cadastrar produto | Produto vinculado à categoria e salvo com imagens de frente e verso |
| Editar produto | Nome e preço alterados; imagens preservadas sem novo upload |
| Conferência no banco | Categoria e produto editados encontrados no phpMyAdmin |
| Excluir categoria com produto | Operação bloqueada e mensagem de erro exibida |
| Excluir produto | Mensagem de sucesso; consulta posterior pela API retorna 404 |
| Excluir categoria sem produtos | Operação permitida; categoria deixa de aparecer na listagem |
| Preço igual a zero | Envio recusado pelo servidor, com mensagem na view |
| Mensagem de sessão | Aparece uma vez e desaparece no acesso seguinte |
| Sessão antiga | Teste da SP-21 confirmou que dados antigos de produtos/categorias não são usados pela home |
| Rota `/reset` | Retorna 404; dados iniciais vêm do `seed.sql` |

O cadastro e a edição de categorias e produtos foram realizados pelos formulários. O upload foi testado com as imagens de exemplo já existentes no projeto. A exclusão foi realizada pelas telas e confirmada por uma nova consulta na API.

## APIs

Foram executadas 22 verificações de API, persistência e preservação dos registros anteriores, todas aprovadas.

| Operação | Resultado |
|---|---|
| GET `/api/categorias` | 200 e JSON válido |
| GET `/api/produtos` | 200 e quantidade correspondente ao banco |
| GET `/api/produtos?categoria=1&busca=Camisa` | Filtro, busca e nome da categoria corretos |
| GET `/api/produto?id=2147483647` | 404 |
| POST `/api/categorias` com nome vazio | 400 |
| POST `/api/produtos` sem campos obrigatórios | 400 |
| POST `/api/produtos` com preço zero | 400 |
| POST `/api/produtos` com categoria inexistente | 400 |
| POST `/api/categorias` com nome válido | 201 e ID gerado pelo MySQL |
| POST `/api/produtos` com dados válidos | 201 e categoria relacionada |
| Conferência SQL dos registros criados | Valores gravados correspondem aos enviados |
| Nova requisição HTTP | Produto continua disponível sem depender da sessão anterior |
| POST `/api/categorias/excluir` com produto vinculado | 400 |
| Editar categoria pelo formulário | 302 para a listagem |
| Editar produto pelo formulário | 302 e valores atualizados na API |
| POST `/api/produtos/excluir` | 200 |
| Consultar o produto excluído | 404 |
| POST `/api/categorias/excluir` sem produtos | 200 |
| Excluir novamente a categoria | 404 |
| Excluir novamente o produto | 404 |
| Comparação dos registros de categorias antes/depois | Registros anteriores preservados |
| Comparação dos registros de produtos antes/depois | Registros anteriores preservados |

## Repetir a conferência

1. Configure o ambiente conforme o README e abra o phpMyAdmin no banco `staysharp`.
2. Cadastre uma categoria de teste e um produto relacionado usando as telas.
3. Confira os registros nas tabelas `categorias` e `produtos`.
4. Edite os nomes e o preço, atualize as tabelas no phpMyAdmin e confirme os valores.
5. Tente excluir a categoria antes do produto: o sistema deve bloquear.
6. Exclua o produto e depois a categoria; confirme que os registros saíram do banco.
7. Consulte as APIs pelo navegador e envie os POSTs pelo Postman, usando `x-www-form-urlencoded`.
8. Teste dados inválidos e confira as mensagens e os códigos HTTP.

## Arquivos de entrega

- `README.md`: instalação, configuração e rotas.
- `schema.sql`: banco, tabelas e chave estrangeira.
- `seed.sql`: três categorias e seis produtos de exemplo.
- `docs/prints/avaliacao-iii/`: prints desta etapa.
- `docs/avaliacao-iii-staysharp.pdf`: documentação com identificação e evidências.

O anexo do PDF registra o quadro do Trello e os pull requests incorporados no GitHub, mostrando a organização e a colaboração dos integrantes.

Os registros temporários dos testes foram removidos. Ao final, o banco manteve as três categorias e os seis produtos que existiam antes da demonstração. Os IDs automáticos podem ter intervalos após os testes; isso é normal no `AUTO_INCREMENT`.

O Termo e o envio na plataforma da disciplina serão finalizados pelo grupo separadamente.
