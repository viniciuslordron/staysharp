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
            $titulo = "Novo Produto";
            $categorias = $_SESSION["categorias"];

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias')
            );
        }

        public function editar(): string {
            return "Em construção"; 
        }

        //formulários 
        public function create(): string {
            if (!isset($_POST["nome"], $_POST["preco"], $_POST["categoria_id"])) {
                $_SESSION["mensagem"] = "Preencha os campos obrigatórios";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }

            if (trim($_POST["nome"]) === "") {
                $_SESSION["mensagem"] = "Informe o nome do produto";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }

            if ((float) $_POST["preco"] <= 0) {
                $_SESSION["mensagem"] = "O preço deve ser maior que zero";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }

            $categoria_id = (int) $_POST["categoria_id"];
            $categoriaExiste = false;

            foreach ($_SESSION["categorias"] as $categoria) {
                if ($categoria["id"] == $categoria_id) {
                    $categoriaExiste = true;
                }
            }

            if (!$categoriaExiste) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }

            $id = proximoId($_SESSION["produtos"]);

            $_SESSION["produtos"][] = [
                "id" => $id,
                "nome" => trim($_POST["nome"]),
                "descricao" => trim($_POST["descricao"] ?? ""),
                "preco" => (float) $_POST["preco"],
                "categoria_id" => $categoria_id,
                "imagem" => trim($_POST["imagem"] ?? "")
            ];

            $_SESSION["mensagem"] = "Produto cadastrado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
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
