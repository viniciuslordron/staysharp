<?php
    // autoload do composer 
    require_once __DIR__ . "/vendor/autoload.php";

    // chamar as classes
    use App\Controllers\HomeController;
    use App\Controllers\CategoriaController;
    use App\Controllers\ProdutoController;
    use App\Controllers\TamanhoController;
    use App\Controllers\LoginController;
    use App\Controllers\RelatorioController;

    //mantem a sessao para as mensagens
    session_start();

    //impede o acesso a administracao sem estar logado
    function exigirLogin() {
        if (!isset($_SESSION["usuario"])) {
            $_SESSION["mensagem"] = "Entre para acessar a administração";
            $_SESSION["tipo"] = "erro";
            header("Location: /staysharp/login");
            exit;
        }
    }

    // pasta base do projeto 
    $basedir = "/staysharp";

    // pegar a url crua sem tratamento
    $uri = $_SERVER["REQUEST_URI"] ?? "/";

    // tirar a query string
    $uri = strtok($uri, "?");

    // remover o /staysharp da frente da URI
    if (str_starts_with($uri, $basedir)) {
        $uri = substr($uri, strlen($basedir));
    }

    // remover a barra final
    $uri = rtrim($uri, "/");

    // a home ("/") vira ""
    if ($uri === "") {
        $uri = "/";
    }

    // pegar o método da requisição (GET ou POST)
    $metodo = $_SERVER["REQUEST_METHOD"];

    //trata falhas na conexao e nas consultas sem mostrar o erro tecnico
    try {
    // GET
    if ($metodo === "GET") {
        if ($uri === "/login") {
            echo (new LoginController())->index();
            exit;
        }
        if ($uri === "/logout") {
            echo (new LoginController())->sair();
            exit;
        }

        // home
        if ($uri === "/") {
            echo (new HomeController())->index();
            exit;
        }

        // telas de categorias
        if ($uri === "/categorias") {
            exigirLogin();
            echo (new CategoriaController())->index();
            exit;
        }
        if ($uri === "/categorias/cadastrar") {
            exigirLogin();
            echo (new CategoriaController())->criar();
            exit;
        }
        if ($uri === "/categorias/editar") {
            exigirLogin();
            echo (new CategoriaController())->editar();
            exit;
        }

        // telas de produtos
        if ($uri === "/produtos") {
            echo (new ProdutoController())->index();
            exit;
        }
        if ($uri === "/produto") {
            echo (new ProdutoController())->detalhe();
            exit;
        }
        if ($uri === "/produtos/cadastrar") {
            exigirLogin();
            echo (new ProdutoController())->criar();
            exit;
        }
        if ($uri === "/produtos/editar") {
            exigirLogin();
            echo (new ProdutoController())->editar();
            exit;
        }

        //relatorio de estoque e exportacao CSV
        if ($uri === "/relatorios") {
            exigirLogin();
            echo (new RelatorioController())->index();
            exit;
        }
        if ($uri === "/relatorios/exportar") {
            exigirLogin();
            echo (new RelatorioController())->exportar();
            exit;
        }

        // apis de consulta
        if ($uri === "/api/categorias") {
            echo (new CategoriaController())->list();
            exit;
        }
        if ($uri === "/api/produtos") {
            echo (new ProdutoController())->list();
            exit;
        }
        if ($uri === "/api/produto") {
            echo (new ProdutoController())->show();
            exit;
        }
    }

    //  POST 
    if ($metodo === "POST") {
        if ($uri === "/login") {
            echo (new LoginController())->entrar();
            exit;
        }

        // formulários de categorias
        if ($uri === "/categorias/salvar") {
            exigirLogin();
            echo (new CategoriaController())->create();
            exit;
        }
        if ($uri === "/categorias/atualizar") {
            exigirLogin();
            echo (new CategoriaController())->update();
            exit;
        }
        if ($uri === "/categorias/excluir") {
            exigirLogin();
            echo (new CategoriaController())->excluir();
            exit;
        }

        // formulários de produtos
        if ($uri === "/produtos/salvar") {
            exigirLogin();
            echo (new ProdutoController())->create();
            exit;
        }
        if ($uri === "/produtos/atualizar") {
            exigirLogin();
            echo (new ProdutoController())->update();
            exit;
        }
        if ($uri === "/produtos/excluir") {
            exigirLogin();
            echo (new ProdutoController())->excluir();
            exit;
        }

        //formulários de tamanhos do produto
        if ($uri === "/tamanhos/salvar") {
            exigirLogin();
            echo (new TamanhoController())->create();
            exit;
        }
        if ($uri === "/tamanhos/atualizar") {
            exigirLogin();
            echo (new TamanhoController())->update();
            exit;
        }
        if ($uri === "/tamanhos/excluir") {
            exigirLogin();
            echo (new TamanhoController())->excluir();
            exit;
        }

        // apis de inserção e exclusão
        if ($uri === "/api/categorias") {
            exigirLogin();
            echo (new CategoriaController())->apiCreate();
            exit;
        }
        if ($uri === "/api/categorias/excluir") {
            exigirLogin();
            echo (new CategoriaController())->apiDelete();
            exit;
        }
        if ($uri === "/api/produtos") {
            exigirLogin();
            echo (new ProdutoController())->apiCreate();
            exit;
        }
        if ($uri === "/api/produtos/excluir") {
            exigirLogin();
            echo (new ProdutoController())->apiDelete();
            exit;
        }
    }

    } catch (PDOException $e) {
        http_response_code(503);
        if (str_starts_with($uri, "/api/")) {
            header("Content-Type: application/json; charset=UTF-8");
            echo json_encode(["erro" => "Não foi possível acessar os dados da loja. Tente novamente mais tarde."], JSON_UNESCAPED_UNICODE);
        } else {
            $titulo = "Dados temporariamente indisponíveis";
            echo (new App\Views\Render())->render('erros/banco', compact('titulo'));
        }
        exit;
    }

    //nenhuma rota encontrada
    http_response_code(404);
    echo "Página não encontrada";
?>
