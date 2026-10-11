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
            $id = $_GET["id"] ?? "";

            // procura o produto com esse id
            $produto = null;
            foreach ($_SESSION["produtos"] as $p) {
                if ($p["id"] == $id) {
                    $produto = $p;
                    break;
                }
            }

            // caso n exista produto
            if ($produto === null) {
                http_response_code(404);
                $titulo = "Produto não encontrado";
                return (new Render())->render(
                    'produtos/detalhe',
                    compact('titulo', 'produto')
                );
            }

            // relacionamento
            $nomesCategorias = array_column($_SESSION["categorias"], "nome", "id");
            $nomeCategoria = $nomesCategorias[$produto["categoria_id"]] ?? "Sem categoria";

                       // pedido pelo WhatsApp: número da loja com 55 + DDD + número, só dígitos
            $whatsapp = "18997069289";
            $mensagem = "Olá! Quero fazer um pedido:\n"
                      . $produto["nome"] . "\n"
                      . "Valor: R$ " . number_format($produto["preco"], 2, ",", ".");
            $linkWhatsapp = "https://wa.me/" . $whatsapp . "?text=" . rawurlencode($mensagem);

            $titulo = $produto["nome"];
            return (new Render())->render(
                'produtos/detalhe',
                compact('titulo', 'produto', 'nomeCategoria', 'linkWhatsapp')
            );
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
            $id = $_GET["id"] ?? 0;
            $produto = null;

            foreach ($_SESSION["produtos"] as $p) {
                if ($p["id"] == $id) {
                    $produto = $p;
                }
            }

            if ($produto === null) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            $titulo = "Editar Produto";
            $categorias = $_SESSION["categorias"];

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias', 'produto')
            );
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
                "imagem" => trim($_POST["imagem"] ?? ""),
                "imagem_verso" => trim($_POST["imagem_verso"] ?? "")
            ];

            $_SESSION["mensagem"] = "Produto cadastrado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
        }

        public function update(): string {
            $id = $_POST["id"] ?? 0;
            $indice = null;

            foreach ($_SESSION["produtos"] as $i => $produto) {
                if ($produto["id"] == $id) {
                    $indice = $i;
                }
            }

            if ($indice === null) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            if (!isset($_POST["nome"], $_POST["preco"], $_POST["categoria_id"])) {
                $_SESSION["mensagem"] = "Preencha os campos obrigatórios";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$id");
                exit;
            }

            if (trim($_POST["nome"]) === "") {
                $_SESSION["mensagem"] = "Informe o nome do produto";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$id");
                exit;
            }

            if ((float) $_POST["preco"] <= 0) {
                $_SESSION["mensagem"] = "O preço deve ser maior que zero";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$id");
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
                header("Location: /staysharp/produtos/editar?id=$id");
                exit;
            }

            $_SESSION["produtos"][$indice] = [
                "id" => (int) $id,
                "nome" => trim($_POST["nome"]),
                "descricao" => trim($_POST["descricao"] ?? ""),
                "preco" => (float) $_POST["preco"],
                "categoria_id" => $categoria_id,
                "imagem" => trim($_POST["imagem"] ?? ""),
                "imagem_verso" => trim($_POST["imagem_verso"] ?? "")
            ];

            $_SESSION["mensagem"] = "Produto atualizado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
        }

        public function excluir(): string {
            $id = $_POST["id"] ?? 0;
            $produtoExiste = false;

            foreach ($_SESSION["produtos"] as $produto) {
                if ($produto["id"] == $id) {
                    $produtoExiste = true;
                }
            }

            if (!$produtoExiste) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            $_SESSION["produtos"] = array_filter(
                $_SESSION["produtos"],
                function ($produto) use ($id) {
                    return $produto["id"] != $id;
                }
            );

            $_SESSION["produtos"] = array_values($_SESSION["produtos"]);

            $_SESSION["mensagem"] = "Produto excluído";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
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
