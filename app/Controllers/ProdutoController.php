<?php
    namespace App\Controllers;
    use App\Views\Render;
    use App\Services\ProdutoService;
    use App\Services\CategoriaService;

    class ProdutoController {
        private ProdutoService $service;
        private CategoriaService $categoriaService;

        public function __construct() {
            $this->service = new ProdutoService();
            $this->categoriaService = new CategoriaService();
        }

        // telas
        public function index(): string {
            $categorias = $this->categoriaService->listar();

            $categoria = $_GET["categoria"] ?? "";
            $busca = trim ($_GET["busca"] ?? "");

            //montar o a lista com os produtos que veio do filtro
            $produtos = $this->service->listar($categoria, $busca);

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
            $id = (int) ($_GET["id"] ?? 0);

            // procura o produto com esse id
            $produto = $this->service->buscarPorId($id);

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
            $nomeCategoria = $produto["categoria_nome"] ?? "Sem categoria";

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
            $categorias = $this->categoriaService->listar();

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias')
            );
        }

        public function editar(): string {
            //pega o id do produto pela url
            $id = (int) ($_GET["id"] ?? 0);

            //procura o produto no banco
            $produto = $this->service->buscarPorId($id);

            if ($produto === null) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            $titulo = "Editar Produto";
            $categorias = $this->categoriaService->listar();

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias', 'produto')
            );
        }

        // recebe o upload de uma imagem e devolve o caminho relativo a public/img/
        // sem arquivo novo, mantém o valor atual
        private function enviarImagem(string $campo, string $atual, string $voltar): string {
            if (!isset($_FILES[$campo]) || $_FILES[$campo]["error"] === UPLOAD_ERR_NO_FILE) {
                return $atual;
            }

            $arquivo = $_FILES[$campo];
            $tipos = [
                "image/jpeg" => "jpg",
                "image/png" => "png",
                "image/webp" => "webp",
                "image/gif" => "gif"
            ];

            $erro = null;
            $info = false;

            if ($arquivo["error"] !== UPLOAD_ERR_OK) {
                $erro = "Não foi possível enviar a imagem";
            } elseif ($arquivo["size"] > 5 * 1024 * 1024) {
                $erro = "A imagem deve ter no máximo 5 MB";
            } else {
                $info = @getimagesize($arquivo["tmp_name"]);

                if ($info === false || !isset($tipos[$info["mime"]])) {
                    $erro = "Envie uma imagem JPG, PNG, WEBP ou GIF";
                }
            }

            if ($erro !== null) {
                $_SESSION["mensagem"] = $erro;
                $_SESSION["tipo"] = "erro";
                header("Location: $voltar");
                exit;
            }

            $pasta = __DIR__ . "/../../public/img/produtos";

            if (!is_dir($pasta)) {
                mkdir($pasta, 0755, true);
            }

            $nome = bin2hex(random_bytes(8)) . "." . $tipos[$info["mime"]];

            if (!move_uploaded_file($arquivo["tmp_name"], "$pasta/$nome")) {
                $_SESSION["mensagem"] = "Não foi possível salvar a imagem";
                $_SESSION["tipo"] = "erro";
                header("Location: $voltar");
                exit;
            }

            return "produtos/$nome";
        }

        //formulários
        public function create(): string {
            //verifica se os campos obrigatórios foram enviados
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

            //verifica se a categoria escolhida existe
            $categoriaExiste = $this->categoriaService->buscarPorId($categoria_id) !== null;

            if (!$categoriaExiste) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }

            $voltar = "/staysharp/produtos/cadastrar";
            $imagem = $this->enviarImagem("imagem", "", $voltar);
            $imagemVerso = $this->enviarImagem("imagem_verso", "", $voltar);

            //adiciona o produto no banco
            $this->service->criar(
                trim($_POST["nome"]),
                trim($_POST["descricao"] ?? ""),
                (float) $_POST["preco"],
                $categoria_id,
                $imagem,
                $imagemVerso
            );

            $_SESSION["mensagem"] = "Produto cadastrado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
        }

        public function update(): string {
            $id = (int) ($_POST["id"] ?? 0);

            //procura o produto atual no banco
            $produtoAtual = $this->service->buscarPorId($id);

            if ($produtoAtual === null) {
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
            $categoriaExiste = $this->categoriaService->buscarPorId($categoria_id) !== null;

            if (!$categoriaExiste) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$id");
                exit;
            }

            // sem arquivo novo, mantém a imagem que o produto já tinha
            $voltar = "/staysharp/produtos/editar?id=$id";
            $imagem = $this->enviarImagem("imagem", $produtoAtual["imagem"] ?? "", $voltar);
            $imagemVerso = $this->enviarImagem("imagem_verso", $produtoAtual["imagem_verso"] ?? "", $voltar);

            if (isset($_POST["remover_verso"])) {
                $imagemVerso = "";
            }

            //atualiza os dados do produto no banco
            $this->service->atualizar(
                $id,
                trim($_POST["nome"]),
                trim($_POST["descricao"] ?? ""),
                (float) $_POST["preco"],
                $categoria_id,
                $imagem,
                $imagemVerso
            );

            $_SESSION["mensagem"] = "Produto atualizado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
        }

        public function excluir(): string {
            $id = (int) ($_POST["id"] ?? 0);

            //verifica se o produto existe
            $produtoExiste = $this->service->buscarPorId($id) !== null;

            if (!$produtoExiste) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            //remove o produto do banco
            $this->service->excluir($id);

            $_SESSION["mensagem"] = "Produto excluído";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos");
            exit;
        }

        // ===== apis =====
        public function list(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $categoria = $_GET["categoria"] ?? "";
            $busca = trim($_GET["busca"] ?? "");

            //filtra os produtos pela categoria e pela busca
            $produtos = $this->service->listar($categoria, $busca);

            return json_encode($produtos, JSON_UNESCAPED_UNICODE);
        }

        public function show(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $id = (int) ($_GET["id"] ?? 0);

            //procura o produto pelo id
            $produtoEncontrado = $this->service->buscarPorId($id);

            if ($produtoEncontrado === null) {
                http_response_code(404);
                return json_encode([
                    "erro" => "Produto não encontrado"
                ], JSON_UNESCAPED_UNICODE);
            }

            return json_encode($produtoEncontrado, JSON_UNESCAPED_UNICODE);
        }

        public function apiCreate(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            //valida os campos recebidos pela api
            if (!isset($_POST["nome"], $_POST["preco"], $_POST["categoria_id"])) {
                http_response_code(400);
                return json_encode([
                    "erro" => "Preencha os campos obrigatórios"
                ], JSON_UNESCAPED_UNICODE);
            }

            if (trim($_POST["nome"]) === "") {
                http_response_code(400);
                return json_encode([
                    "erro" => "Informe o nome do produto"
                ], JSON_UNESCAPED_UNICODE);
            }

            if ((float) $_POST["preco"] <= 0) {
                http_response_code(400);
                return json_encode([
                    "erro" => "O preço deve ser maior que zero"
                ], JSON_UNESCAPED_UNICODE);
            }

            $categoria_id = (int) $_POST["categoria_id"];

            //verifica se a categoria existe
            $categoriaExiste = $this->categoriaService->buscarPorId($categoria_id) !== null;

            if (!$categoriaExiste) {
                http_response_code(400);
                return json_encode([
                    "erro" => "Categoria não encontrada"
                ], JSON_UNESCAPED_UNICODE);
            }

            //salva o produto no banco
            $id = $this->service->criar(
                trim($_POST["nome"]),
                trim($_POST["descricao"] ?? ""),
                (float) $_POST["preco"],
                $categoria_id,
                trim($_POST["imagem"] ?? ""),
                trim($_POST["imagem_verso"] ?? "")
            );
            $produto = $this->service->buscarPorId($id);

            http_response_code(201);
            return json_encode($produto, JSON_UNESCAPED_UNICODE);
        }

        public function apiDelete(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $id = (int) ($_POST["id"] ?? 0);

            //verifica se o produto existe
            $produtoExiste = $this->service->buscarPorId($id) !== null;

            if (!$produtoExiste) {
                http_response_code(404);
                return json_encode([
                    "erro" => "Produto não encontrado"
                ], JSON_UNESCAPED_UNICODE);
            }

            //remove o produto do banco
            $this->service->excluir($id);

            return json_encode([
                "mensagem" => "Excluído"
            ], JSON_UNESCAPED_UNICODE);
        }
    }
?>
