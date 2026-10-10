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
            $id = $_GET["id"] ?? 0;
            $categoria = null;

            foreach ($_SESSION["categorias"] as $c) {
                if ($c["id"] == $id) {
                    $categoria = $c;
                }
            }

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
            if (!isset($_POST["nome"]) || trim($_POST["nome"]) === "") {
                $_SESSION["mensagem"] = "Informe o nome da categoria";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias/cadastrar");
                exit;
            }

            $nome = trim($_POST["nome"]);
            $id = proximoId($_SESSION["categorias"]);

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

            $_SESSION["categorias"][$indice]["nome"] = trim($_POST["nome"]);

            $_SESSION["mensagem"] = "Categoria atualizada";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        public function excluir(): string {
            $id = $_POST["id"] ?? 0;
            $categoriaExiste = false;

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

            foreach ($_SESSION["produtos"] as $produto) {
                if ($produto["categoria_id"] == $id) {
                    $_SESSION["mensagem"] = "Não é possível excluir: existem produtos nesta categoria";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/categorias");
                    exit;
                }
            }

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
            return "Em construção";
        }

        public function apiDelete(): string {
            return "Em construção";
        }
    }
?>
