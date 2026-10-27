<?php
    namespace App\Controllers;
    use App\Views\Render;
    use App\Services\CategoriaService;

    class CategoriaController {
        private CategoriaService $service;

        public function __construct() {
            $this->service = new CategoriaService();
        }

        // ===== telas =====
        public function index(): string {
            $titulo = "Categorias";
            $categorias = $this->service->listar();
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
            $id = (int) ($_GET["id"] ?? 0);

            //procura a categoria na sessão
            $categoria = $this->service->buscarPorId($id);

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

            //adiciona a categoria na sessão
            $this->service->criar($nome);

            $_SESSION["mensagem"] = "Categoria cadastrada com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        public function update(): string {
            $id = (int) ($_POST["id"] ?? 0);

            //procura a posição da categoria na sessão
            if ($this->service->buscarPorId($id) === null) {
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
            $this->service->atualizar($id, trim($_POST["nome"]));

            $_SESSION["mensagem"] = "Categoria atualizada";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        public function excluir(): string {
            $id = (int) ($_POST["id"] ?? 0);

            //verifica se a categoria existe
            if ($this->service->buscarPorId($id) === null) {
                $_SESSION["mensagem"] = "Categoria não encontrada";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias");
                exit;
            }

            //impede excluir categoria que possui produtos
            if ($this->service->contarProdutos($id) > 0) {
                $_SESSION["mensagem"] = "Não é possível excluir: existem produtos nesta categoria";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/categorias");
                exit;
            }

            //remove a categoria da sessão
            $this->service->excluir($id);

            $_SESSION["mensagem"] = "Categoria excluída";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/categorias");
            exit;
        }

        // apis
        public function list(): string {
            header("Content-Type: application/json; charset=UTF-8");
            return json_encode($this->service->listar(), JSON_UNESCAPED_UNICODE);
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

            //salva a nova categoria na sessão
            $id = $this->service->criar(trim($_POST["nome"]));
            $categoria = $this->service->buscarPorId($id);

            http_response_code(201);
            return json_encode($categoria, JSON_UNESCAPED_UNICODE);
        }

        public function apiDelete(): string {
            //informa que a resposta será em json
            header("Content-Type: application/json; charset=UTF-8");

            $id = (int) ($_POST["id"] ?? 0);

            //verifica se a categoria existe
            if ($this->service->buscarPorId($id) === null) {
                http_response_code(404);
                return json_encode([
                    "erro" => "Categoria não encontrada"
                ], JSON_UNESCAPED_UNICODE);
            }

            //impede excluir categoria que possui produtos
            if ($this->service->contarProdutos($id) > 0) {
                http_response_code(400);
                return json_encode([
                    "erro" => "Não é possível excluir: existem produtos nesta categoria"
                ], JSON_UNESCAPED_UNICODE);
            }

            //remove a categoria da sessão
            $this->service->excluir($id);

            return json_encode([
                "mensagem" => "Excluído"
            ], JSON_UNESCAPED_UNICODE);
        }
    }
?>