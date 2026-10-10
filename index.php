<?php
    // autoload do composer 
    require_once __DIR__ . "/vendor/autoload.php";

    // funções de apoio 
    require_once __DIR__ . "/app/Data/funcoes.php";

    // chamar as classes
    use App\Controllers\HomeController;
    use App\Controllers\CategoriaController;
    use App\Controllers\ProdutoController;

    // fake db: só carrega o seed se a session ainda estiver vazia
    session_start();
    if (!isset($_SESSION["produtos"])) {
        require __DIR__ . "/app/Data/seed.php";
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

    // GET
    if ($metodo === "GET") {
        // home
        if ($uri === "/") {
            echo (new HomeController())->index();
            exit;
        }

        // utilitário: reiniciar os dados
        if ($uri === "/reset") {
            session_unset();
            require __DIR__ . "/app/Data/seed.php";
            $_SESSION["mensagem"] = "Dados reiniciados";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/");
            exit;
        }

        // telas de categorias
        if ($uri === "/categorias") {
            echo (new CategoriaController())->index();
            exit;
        }
        if ($uri === "/categorias/cadastrar") {
            echo (new CategoriaController())->criar();
            exit;
        }
        if ($uri === "/categorias/editar") {
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
            echo (new ProdutoController())->criar();
            exit;
        }
        if ($uri === "/produtos/editar") {
            echo (new ProdutoController())->editar();
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
        // formulários de categorias
        if ($uri === "/categorias/salvar") {
            echo (new CategoriaController())->create();
            exit;
        }
        if ($uri === "/categorias/atualizar") {
            echo (new CategoriaController())->update();
            exit;
        }
        if ($uri === "/categorias/excluir") {
            echo (new CategoriaController())->excluir();
            exit;
        }

        // formulários de produtos
        if ($uri === "/produtos/salvar") {
            echo (new ProdutoController())->create();
            exit;
        }
        if ($uri === "/produtos/atualizar") {
            echo (new ProdutoController())->update();
            exit;
        }
        if ($uri === "/produtos/excluir") {
            echo (new ProdutoController())->excluir();
            exit;
        }

        // apis de inserção e exclusão
        if ($uri === "/api/categorias") {
            echo (new CategoriaController())->apiCreate();
            exit;
        }
        if ($uri === "/api/categorias/excluir") {
            echo (new CategoriaController())->apiDelete();
            exit;
        }
        if ($uri === "/api/produtos") {
            echo (new ProdutoController())->apiCreate();
            exit;
        }
        if ($uri === "/api/produtos/excluir") {
            echo (new ProdutoController())->apiDelete();
            exit;
        }
    }

    //nenhuma rota encontrada
    http_response_code(404);
    echo "Página não encontrada";
?>