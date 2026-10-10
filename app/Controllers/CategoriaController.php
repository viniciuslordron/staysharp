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
            return "Em construção"; 
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
            return "Em construção"; 
        }

        public function excluir(): string {
            return "Em construção";
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
