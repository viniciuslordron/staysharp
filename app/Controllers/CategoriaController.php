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
            return "Em construção"; 
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