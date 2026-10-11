<?php
    namespace App\Controllers;
    use App\Views\Render;

    class CategoriaController {
        // ===== telas =====
        public function index(): string {
            $titulo = "Categorias";
            $categorias = $_SESSION["categorias"];
            return (new Render())->render(
                'categorias/index',
                compact('titulo', 'categorias')
            );
        }

        public function criar(): string {
            $titulo = "Nova Categoria";
            return (new Render())->render(
                'categorias/criar',
                compact('titulo')
            );
        }

        public function editar(): string {
            //pega o id da categoria pela url
            $id = $_GET["id"] ?? 0;
            $categoria = null;

            //procura a categoria na sessão
            foreach ($_SESSION["categorias"] as $c) {
                if ($c["id"] == $id) {
                    $categoria = $c;
                }
            }

            //volta para a lista caso não encontre
            if ($categoria === null) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias");
                exit;
            }

            $titulo = "Editar Categoria";
            return (new Render())->render(
                'categorias/editar',
                compact('titulo', 'categoria')
            );
        }

        // formulários
        public function create(): string {
            //verifica se o nome foi preenchido
            if (!isset($_POST["nome"]) || trim($_POST["nome"]) === "") {
                $_SESSION["mensagem"] = "Informe o nome da categoria";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias/cadastrar");
                exit;
            }

            $nome = trim($_POST["nome"]);
            $id = proximoId($_SESSION["categorias"]);

            //adiciona a categoria na sessão
            $_SESSION["categorias"][] = [
                "id" => $id,
                "nome" => $nome
            ];

            $_SESSION["mensagem"] = "Categoria cadastrada com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        public function update(): string {
            $id = $_POST["id"] ?? 0;
            $indice = null;

            //procura a posição da categoria na sessão
            foreach ($_SESSION["categorias"] as $i => $c) {
                if ($c["id"] == $id) {
                    $indice = $i;
                }
            }

            if ($indice === null) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias");
                exit;
            }

            if (!isset($_POST["nome"]) || trim($_POST["nome"]) === "") {
                $_SESSION["mensagem"] = "Informe o nome da categoria";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias/editar?id=$id");
                exit;
            }

            //atualiza o nome da categoria
            $_SESSION["categorias"][$indice]["nome"] = trim($_POST["nome"]);

            $_SESSION["mensagem"] = "Categoria atualizada";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        public function excluir(): string {
            $id = $_POST["id"] ?? 0;
            $categoriaExiste = false;

            //verifica se a categoria existe
            foreach ($_SESSION["categorias"] as $c) {
                if ($c["id"] == $id) {
                    $categoriaExiste = true;
                }
            }

            if (!$categoriaExiste) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias");
                exit;
            }

            //impede excluir categoria que possui produtos
            foreach ($_SESSION["produtos"] as $produto) {
                if ($produto["categoria_id"] == $id) {
                    $_SESSION["mensagem"] = "Não é possível excluir: existem produtos nesta categoria";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/categorias");
                    exit;
                }
            }

            //remove a categoria da sessão
            $_SESSION["categorias"] = array_filter(
                $_SESSION["categorias"],
                function ($c) use ($id) {
                    return $c["id"] != $id;
                }
            );

            $_SESSION["categorias"] = array_values($_SESSION["categorias"]);

            $_SESSION["mensagem"] = "Categoria excluída";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        // apis 
        public function list(): string {
            header("Content-Type: application/json; charset=UTF-8");
            return json_encode($_SESSION["categorias"], JSON_UNESCAPED_UNICODE);
        }

        public function apiCreate(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            //valida o nome recebido pela api
            if (!isset($_POST["nome"]) || trim($_POST["nome"]) === "") {
                http_response_code(400);
                return json_encode([
                    "erro" => "Informe o nome da categoria"
                ], JSON_UNESCAPED_UNICODE);
            }

            $categoria = [
                "id" => proximoId($_SESSION["categorias"]),
                "nome" => trim($_POST["nome"])
            ];

            //salva a nova categoria na sessão
            $_SESSION["categorias"][] = $categoria;

            http_response_code(201);
            return json_encode($categoria, JSON_UNESCAPED_UNICODE);
        }

        public function apiDelete(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $id = $_POST["id"] ?? 0;
            $categoriaExiste = false;

            //verifica se a categoria existe
            foreach ($_SESSION["categorias"] as $categoria) {
                if ($categoria["id"] == $id) {
                    $categoriaExiste = true;
                }
            }

            if (!$categoriaExiste) {
                http_response_code(404);
                return json_encode([
                    "erro" => "Categoria não encontrada"
                ], JSON_UNESCAPED_UNICODE);
            }

            //impede excluir categoria que possui produtos
            foreach ($_SESSION["produtos"] as $produto) {
                if ($produto["categoria_id"] == $id) {
                    http_response_code(400);
                    return json_encode([
                        "erro" => "Não é possível excluir: existem produtos nesta categoria"
                    ], JSON_UNESCAPED_UNICODE);
                }
            }

            //remove a categoria da sessão
            $_SESSION["categorias"] = array_filter(
                $_SESSION["categorias"],
                function ($categoria) use ($id) {
                    return $categoria["id"] != $id;
                }
            );

            $_SESSION["categorias"] = array_values($_SESSION["categorias"]);

            return json_encode([
                "mensagem" => "Excluído"
            ], JSON_UNESCAPED_UNICODE);
        }
    }
?>
