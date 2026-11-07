<?php
    namespace App\Controllers;
    use PDOException;
    use App\Views\Render;
    use App\Models\ProdutoModel;
    use App\Models\CategoriaModel;
    use App\Models\TamanhoModel;
    use App\Config\Database;

    class ProdutoController {
        private ProdutoModel $model;
        private CategoriaModel $categoriaModel;
        private TamanhoModel $tamanhoModel;

        public function __construct() {
            $this->model = new ProdutoModel();
            $this->categoriaModel = new CategoriaModel();
            $this->tamanhoModel = new TamanhoModel();
        }

        // telas
        public function index(): string {
            $categorias = $this->categoriaModel->listar();

            $categoria = is_string($_GET["categoria"] ?? null) ? $_GET["categoria"] : "";
            $busca = is_string($_GET["busca"] ?? null) ? trim($_GET["busca"]) : "";

            //montar o a lista com os produtos que veio do filtro
            $produtos = $this->model->listar($categoria, $busca);

            // soma o estoque de todos os tamanhos (0 = esgotado)
            foreach ($produtos as $i => $p) {
                $produtos[$i]["estoque_total"] = $this->tamanhoModel->estoqueTotal($p["id"]);
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
            $id = (filter_var($_GET["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

            // procura o produto com esse id
            $produto = $this->model->buscarPorId($id);

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

            // tamanhos do produto (relacionamento produto -> tamanhos)
            $tamanhos = $this->tamanhoModel->listarPorProduto($id);
            $esgotado = $this->tamanhoModel->estoqueTotal($id) === 0;

            // tamanho escolhido pela URL (/produto?id=1&tamanho=M)
            // só vale se existir nesse produto e tiver estoque
            $tamanhoEscolhido = is_string($_GET["tamanho"] ?? null) ? $_GET["tamanho"] : "";
            $tamanhoValido = false;
            foreach ($tamanhos as $t) {
                if ($t["tamanho"] === $tamanhoEscolhido && $t["estoque"] > 0) {
                    $tamanhoValido = true;
                }
            }
            if (!$tamanhoValido) {
                $tamanhoEscolhido = "";
            }

                       // pedido pelo WhatsApp: número da loja com 55 + DDD + número, só dígitos
            $whatsapp = "18997069289";
            $mensagem = "Olá! Quero fazer um pedido:\n"
                      . $produto["nome"] . "\n"
                      . "Tamanho: " . $tamanhoEscolhido . "\n"
                      . "Valor: R$ " . number_format($produto["preco"], 2, ",", ".");
            $linkWhatsapp = "https://wa.me/" . $whatsapp . "?text=" . rawurlencode($mensagem);

            $titulo = $produto["nome"];
            return (new Render())->render(
                'produtos/detalhe',
                compact('titulo', 'produto', 'nomeCategoria', 'linkWhatsapp', 'tamanhos', 'tamanhoEscolhido', 'esgotado')
            );
        }

        public function criar(): string {
            $titulo = "Novo Produto";
            $categorias = $this->categoriaModel->listar();

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias')
            );
        }

        public function editar(): string {
            //pega o id do produto pela url
            $id = (filter_var($_GET["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

            //procura o produto no banco
            $produto = $this->model->buscarPorId($id);

            if ($produto === null) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            $titulo = "Editar Produto";
            $categorias = $this->categoriaModel->listar();
            //carrega os tamanhos para gerenciar o estoque na edição
            $tamanhos = $this->tamanhoModel->listarPorProduto($id);

            return (new Render())->render(
                'produtos/criar',
                compact('titulo', 'categorias', 'produto', 'tamanhos')
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

        //valida os mesmos dados nos formularios e na API
        private function validarDados(): ?string {
            if (!isset($_POST["nome"], $_POST["preco"], $_POST["categoria_id"])) {
                return "Preencha os campos obrigatórios";
            }
            if (!is_string($_POST["nome"]) || trim($_POST["nome"]) === "") {
                return "Informe o nome do produto";
            }
            if (mb_strlen(trim($_POST["nome"])) > 150) {
                return "O nome do produto deve ter no máximo 150 caracteres";
            }

            //evita converter texto em preco e respeita as duas casas decimais
            $preco = filter_var($_POST["preco"], FILTER_VALIDATE_FLOAT);
            if ($preco === false || $preco <= 0 || $preco > 99999999.99 || round($preco, 2) != $preco) {
                return "O preço deve ser um número maior que zero, com até duas casas decimais e no máximo 99999999.99";
            }

            $categoriaId = filter_var($_POST["categoria_id"], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
            if ($categoriaId === false || $this->categoriaModel->buscarPorId($categoriaId) === null) {
                return "Categoria não encontrada";
            }
            foreach (["descricao", "imagem", "imagem_verso"] as $campo) {
                if (isset($_POST[$campo]) && !is_string($_POST[$campo])) {
                    return "Dados do produto inválidos";
                }
            }
            foreach (["imagem", "imagem_verso"] as $campo) {
                if (mb_strlen($_POST[$campo] ?? "") > 255) {
                    return "O caminho da imagem deve ter no máximo 255 caracteres";
                }
            }
            return null;
        }

        // le os tamanhos enviados junto com o formulario do produto
        // devolve [erro, plano]; o plano diz o que atualizar, remover e criar
        private function lerTamanhos(array $existentes): array {
            $plano = ["atualizar" => [], "remover" => [], "novos" => []];
            $porId = [];
            foreach ($existentes as $t) {
                $porId[(int) $t["id"]] = $t;
            }
            $limite = ["options" => ["min_range" => 0, "max_range" => 2147483647]];
            $msgEstoque = "O estoque deve ser um número inteiro entre 0 e 2147483647";

            $remover = $_POST["remover_tamanhos"] ?? [];
            if (!is_array($remover)) {
                return ["Dados de tamanhos inválidos", $plano];
            }
            foreach ($remover as $idRemover) {
                $idRemover = filter_var($idRemover, FILTER_VALIDATE_INT);
                if ($idRemover !== false && isset($porId[$idRemover])) {
                    $plano["remover"][$idRemover] = true;
                }
            }

            $atuais = $_POST["tamanhos"] ?? [];
            if (!is_array($atuais)) {
                return ["Dados de tamanhos inválidos", $plano];
            }
            foreach ($atuais as $idTamanho => $estoque) {
                $idTamanho = (int) $idTamanho;
                if (!isset($porId[$idTamanho]) || isset($plano["remover"][$idTamanho])) {
                    continue;
                }
                $estoque = is_string($estoque) ? filter_var($estoque, FILTER_VALIDATE_INT, $limite) : false;
                if ($estoque === false) {
                    return [$msgEstoque, $plano];
                }
                $plano["atualizar"][$idTamanho] = $estoque;
            }

            // nomes que continuam no produto, para impedir tamanho repetido
            $usados = [];
            foreach ($porId as $idTamanho => $t) {
                if (!isset($plano["remover"][$idTamanho])) {
                    $usados[mb_strtolower($t["tamanho"])] = true;
                }
            }

            $novos = $_POST["novos_tamanhos"] ?? [];
            if (!is_array($novos)) {
                return ["Dados de tamanhos inválidos", $plano];
            }
            foreach ($novos as $novo) {
                if (!is_array($novo) || !is_string($novo["nome"] ?? null) || !is_string($novo["estoque"] ?? null)) {
                    return ["Dados de tamanhos inválidos", $plano];
                }
                $nome = trim($novo["nome"]);
                if ($nome === "") {
                    continue;
                }
                if (mb_strlen($nome) > 10) {
                    return ["O tamanho deve ter no máximo 10 caracteres", $plano];
                }
                $estoque = filter_var($novo["estoque"], FILTER_VALIDATE_INT, $limite);
                if ($estoque === false) {
                    return [$msgEstoque, $plano];
                }
                if (isset($usados[mb_strtolower($nome)])) {
                    return ["O tamanho $nome está repetido neste produto", $plano];
                }
                $usados[mb_strtolower($nome)] = true;
                $plano["novos"][] = [$nome, $estoque];
            }

            return [null, $plano];
        }

        // grava o plano dos tamanhos para o produto
        private function salvarTamanhos(int $produtoId, array $plano): void {
            foreach (array_keys($plano["remover"]) as $idTamanho) {
                $this->tamanhoModel->excluir($idTamanho);
            }
            foreach ($plano["atualizar"] as $idTamanho => $estoque) {
                $atual = $this->tamanhoModel->buscarPorId($idTamanho);
                $this->tamanhoModel->atualizar($idTamanho, $atual["tamanho"], $estoque);
            }
            foreach ($plano["novos"] as [$nome, $estoque]) {
                $this->tamanhoModel->criar($produtoId, $nome, $estoque);
            }
        }

        //formulários
        public function create(): string {
            try {
                $erro = $this->validarDados();
                if ($erro !== null) {
                    $_SESSION["mensagem"] = $erro;
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/produtos/cadastrar");
                    exit;
                }
                $categoria_id = (int) $_POST["categoria_id"];

                $voltar = "/staysharp/produtos/cadastrar";

                [$erro, $plano] = $this->lerTamanhos([]);
                if ($erro !== null) {
                    $_SESSION["mensagem"] = $erro;
                    $_SESSION["tipo"] = "erro";
                    header("Location: $voltar");
                    exit;
                }

                //a foto de frente é obrigatória no cadastro
                if (!isset($_FILES["imagem"]) || $_FILES["imagem"]["error"] === UPLOAD_ERR_NO_FILE) {
                    $_SESSION["mensagem"] = "Envie a foto de frente do produto";
                    $_SESSION["tipo"] = "erro";
                    header("Location: $voltar");
                    exit;
                }

                $imagem = $this->enviarImagem("imagem", "", $voltar);
                $imagemVerso = $this->enviarImagem("imagem_verso", "", $voltar);

                //adiciona o produto e os tamanhos no banco, tudo ou nada
                $pdo = Database::getConnection();
                $pdo->beginTransaction();
                try {
                    $novoId = $this->model->criar(
                        trim($_POST["nome"]),
                        trim($_POST["descricao"] ?? ""),
                        (float) $_POST["preco"],
                        $categoria_id,
                        $imagem,
                        $imagemVerso
                    );
                    $this->salvarTamanhos($novoId, $plano);
                    $pdo->commit();
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    throw $e;
                }

                $_SESSION["mensagem"] = "Produto cadastrado com sucesso";
                $_SESSION["tipo"] = "sucesso";
                header("Location: /staysharp/produtos");
                exit;
            } catch (PDOException $e) {
                $_SESSION["mensagem"] = "Não foi possível cadastrar o produto. Tente novamente.";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/cadastrar");
                exit;
            }
        }

        public function update(): string {
            try {
                $id = (filter_var($_POST["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

                //procura o produto atual no banco
                $produtoAtual = $this->model->buscarPorId($id);

                if ($produtoAtual === null) {
                    $_SESSION["mensagem"] = "Produto não encontrado";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/produtos");
                    exit;
                }

                $erro = $this->validarDados();
                if ($erro !== null) {
                    $_SESSION["mensagem"] = $erro;
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/produtos/editar?id=$id");
                    exit;
                }
                $categoria_id = (int) $_POST["categoria_id"];

                // sem arquivo novo, mantém a imagem que o produto já tinha
                $voltar = "/staysharp/produtos/editar?id=$id";

                [$erro, $plano] = $this->lerTamanhos($this->tamanhoModel->listarPorProduto($id));
                if ($erro !== null) {
                    $_SESSION["mensagem"] = $erro;
                    $_SESSION["tipo"] = "erro";
                    header("Location: $voltar");
                    exit;
                }
                $imagem = $this->enviarImagem("imagem", $produtoAtual["imagem"] ?? "", $voltar);
                $imagemVerso = $this->enviarImagem("imagem_verso", $produtoAtual["imagem_verso"] ?? "", $voltar);

                if (isset($_POST["remover_verso"])) {
                    $imagemVerso = "";
                }

                //atualiza o produto e os tamanhos no banco, tudo ou nada
                $pdo = Database::getConnection();
                $pdo->beginTransaction();
                try {
                    $this->model->atualizar(
                        $id,
                        trim($_POST["nome"]),
                        trim($_POST["descricao"] ?? ""),
                        (float) $_POST["preco"],
                        $categoria_id,
                        $imagem,
                        $imagemVerso
                    );
                    $this->salvarTamanhos($id, $plano);
                    $pdo->commit();
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    throw $e;
                }

                $_SESSION["mensagem"] = "Produto atualizado com sucesso";
                $_SESSION["tipo"] = "sucesso";
                header("Location: /staysharp/produtos");
                exit;
            } catch (PDOException $e) {
                $_SESSION["mensagem"] = "Não foi possível atualizar o produto. Tente novamente.";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }
        }

        public function excluir(): string {
            try {
                $id = (filter_var($_POST["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

                //verifica se o produto existe
                $produtoExiste = $this->model->buscarPorId($id) !== null;

                if (!$produtoExiste) {
                    $_SESSION["mensagem"] = "Produto não encontrado";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/produtos");
                    exit;
                }

                //remove o produto do banco
                $this->model->excluir($id);

                $_SESSION["mensagem"] = "Produto excluído";
                $_SESSION["tipo"] = "sucesso";
                header("Location: /staysharp/produtos");
                exit;
            } catch (PDOException $e) {
                $_SESSION["mensagem"] = "Não foi possível excluir o produto. Tente novamente.";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }
        }

        // ===== apis =====
        public function list(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $categoria = is_string($_GET["categoria"] ?? null) ? $_GET["categoria"] : "";
            $busca = is_string($_GET["busca"] ?? null) ? trim($_GET["busca"]) : "";

            //filtra os produtos pela categoria e pela busca
            $produtos = $this->model->listar($categoria, $busca);

            return json_encode($produtos, JSON_UNESCAPED_UNICODE);
        }

        public function show(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $id = (filter_var($_GET["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

            //procura o produto pelo id
            $produtoEncontrado = $this->model->buscarPorId($id);

            if ($produtoEncontrado === null) {
                http_response_code(404);
                return json_encode([
                    "erro" => "Produto não encontrado"
                ], JSON_UNESCAPED_UNICODE);
            }

            // junta a lista de tamanhos do produto
            $produtoEncontrado["tamanhos"] = $this->tamanhoModel->listarPorProduto($id);

            return json_encode($produtoEncontrado, JSON_UNESCAPED_UNICODE);
        }

        public function apiCreate(): string {
            try {
                //informa que a resposta será em json
                header("Content-Type: application/json; charset=UTF-8");

                $erro = $this->validarDados();
                if ($erro !== null) {
                    http_response_code(400);
                    return json_encode(["erro" => $erro], JSON_UNESCAPED_UNICODE);
                }
                $categoria_id = (int) $_POST["categoria_id"];

                //salva o produto no banco
                $id = $this->model->criar(
                    trim($_POST["nome"]),
                    trim($_POST["descricao"] ?? ""),
                    (float) $_POST["preco"],
                    $categoria_id,
                    trim($_POST["imagem"] ?? ""),
                    trim($_POST["imagem_verso"] ?? "")
                );
                $produto = $this->model->buscarPorId($id);

                http_response_code(201);
                return json_encode($produto, JSON_UNESCAPED_UNICODE);
            } catch (PDOException $e) {
                http_response_code(500);
                return json_encode(["erro" => "Não foi possível cadastrar o produto. Tente novamente."], JSON_UNESCAPED_UNICODE);
            }
        }

        public function apiDelete(): string {
            try {
                //informa que a resposta será em json
                header("Content-Type: application/json; charset=UTF-8");

                $id = (filter_var($_POST["id"] ?? 0, FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) ?: 0);

                //verifica se o produto existe
                $produtoExiste = $this->model->buscarPorId($id) !== null;

                if (!$produtoExiste) {
                    http_response_code(404);
                    return json_encode([
                        "erro" => "Produto não encontrado"
                    ], JSON_UNESCAPED_UNICODE);
                }

                //remove o produto do banco
                $this->model->excluir($id);

                return json_encode([
                    "mensagem" => "Excluído"
                ], JSON_UNESCAPED_UNICODE);
            } catch (PDOException $e) {
                http_response_code(500);
                return json_encode(["erro" => "Não foi possível excluir o produto. Tente novamente."], JSON_UNESCAPED_UNICODE);
            }
        }
    }
?>
