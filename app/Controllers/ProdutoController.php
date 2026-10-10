<?php
    namespace App\Controllers;
    use App\Views\Render;

    class ProdutoController {
        // telas 
        public function index(): string {
            $categorias = $_SESSION["categorias"];

            $categoria = $_GET["categoria"] ?? "";
            $busca = trim ($_GET["busca"] ?? "");

            //montar o a lista com os produtos que veio do filtro
            $produtos = [];
            foreach ($_SESSION["produtos"] as $p){
                if ($categoria !== "" && $p["categoria_id"] != $categoria){
                    continue;
                }
                if ($busca !== "" && !str_contains(mb_strtolower($p["nome"]), mb_strtolower($busca))) {
                    continue;
                }
                $produtos[] = $p;
            }
            // relacionamento: [1 => "Camisas", 2 => "Bonés", 3 => "Adesivos"]
            $nomesCategorias = array_column($categorias, "nome", "id");

            // título muda com o filtro
            $titulo = "Produtos";
            if ($categoria !== "" && isset($nomesCategorias[$categoria])) {
                $titulo = $nomesCategorias[$categoria];
            }
            if ($busca !== "") {
                $titulo = "Resultados para: " . htmlspecialchars($busca);
            }
            return (new Render())->render(
                'produtos/index',
                compact('titulo', 'produtos', 'categorias', 'nomesCategorias', 'categoria', 'busca')
            );
        }

        public function detalhe(): string {
            return "Em construção";
        }

        public function criar(): string {
            return "Em construção";
        }

        public function editar(): string {
            return "Em construção"; 
        }

        //formulários 
        public function create(): string {
            return "Em construção"; 
        }

        public function update(): string {
            return "Em construção";
        }

        public function excluir(): string {
            return "Em construção"; 
        }

        // ===== apis =====
        public function list(): string {
            return "Em construção";
        }

        public function show(): string {
            return "Em construção";
        }

        public function apiCreate(): string {
            return "Em construção";
        }

        public function apiDelete(): string {
            return "Em construção";
        }
    }
?>