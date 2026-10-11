<?php 
    namespace App\Controllers;
    use App\Views\Render;
    class HomeController {
        public function index(): string {
            $titulo = "StaySharp";

            $categorias = $_SESSION["categorias"] ?? [];
            $nomesCategorias = array_column($categorias, "nome", "id");

            // destaques: os 4 primeiros produtos cadastrados
            $destaques = array_slice($_SESSION["produtos"] ?? [], 0, 4);

            return (new Render()) -> render (
                'home/index',
                compact('titulo', 'categorias', 'nomesCategorias', 'destaques')
            );
        }
    }
?>
