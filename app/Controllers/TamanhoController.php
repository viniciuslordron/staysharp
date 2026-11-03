<?php
    namespace App\Controllers;

    use App\Models\ProdutoModel;
    use App\Models\TamanhoModel;

    class TamanhoController {
        private ProdutoModel $produtoModel;
        private TamanhoModel $model;

        public function __construct() {
            $this->produtoModel = new ProdutoModel();
            $this->model = new TamanhoModel();
        }

        public function create(): string {
            $produtoId = (int) ($_POST["produto_id"] ?? 0);

            //confere se o produto existe antes de cadastrar o tamanho
            if ($this->produtoModel->buscarPorId($produtoId) === null) {
                $_SESSION["mensagem"] = "Produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            $tamanho = is_string($_POST["tamanho"] ?? null) ? trim($_POST["tamanho"]) : "";
            $estoque = filter_var($_POST["estoque"] ?? "", FILTER_VALIDATE_INT, [
                "options" => ["min_range" => 0, "max_range" => 2147483647]
            ]);

            //valida os campos e impede tamanho repetido no mesmo produto
            $erro = null;
            if ($tamanho === "") {
                $erro = "Informe o tamanho";
            } elseif (mb_strlen($tamanho) > 10) {
                $erro = "O tamanho deve ter no máximo 10 caracteres";
            } elseif ($estoque === false) {
                $erro = "O estoque deve ser um número inteiro entre 0 e 2147483647";
            } elseif ($this->model->existe($produtoId, $tamanho)) {
                $erro = "Este tamanho já está cadastrado neste produto";
            }

            if ($erro !== null) {
                $_SESSION["mensagem"] = $erro;
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$produtoId#tamanhos");
                exit;
            }

            $this->model->criar($produtoId, $tamanho, $estoque);
            $_SESSION["mensagem"] = "Tamanho cadastrado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos/editar?id=$produtoId#tamanhos");
            exit;
        }

        public function update(): string {
            $id = (int) ($_POST["id"] ?? 0);
            $tamanho = $this->model->buscarPorId($id);

            if (!$tamanho || $this->produtoModel->buscarPorId($tamanho["produto_id"]) === null) {
                $_SESSION["mensagem"] = "Tamanho ou produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            //usa o produto e o tamanho salvos no banco, alterando apenas o estoque
            $produtoId = $tamanho["produto_id"];
            $estoque = filter_var($_POST["estoque"] ?? "", FILTER_VALIDATE_INT, [
                "options" => ["min_range" => 0, "max_range" => 2147483647]
            ]);

            if ($estoque === false) {
                $_SESSION["mensagem"] = "O estoque deve ser um número inteiro entre 0 e 2147483647";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos/editar?id=$produtoId#tamanhos");
                exit;
            }

            $this->model->atualizar($id, $tamanho["tamanho"], $estoque);
            $_SESSION["mensagem"] = "Estoque atualizado com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos/editar?id=$produtoId#tamanhos");
            exit;
        }

        public function excluir(): string {
            $id = (int) ($_POST["id"] ?? 0);
            $tamanho = $this->model->buscarPorId($id);

            if (!$tamanho || $this->produtoModel->buscarPorId($tamanho["produto_id"]) === null) {
                $_SESSION["mensagem"] = "Tamanho ou produto não encontrado";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/produtos");
                exit;
            }

            //exclui o tamanho e volta para a edição do produto dele
            $produtoId = $tamanho["produto_id"];
            $this->model->excluir($id);
            $_SESSION["mensagem"] = "Tamanho excluído com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/produtos/editar?id=$produtoId#tamanhos");
            exit;
        }
    }
?>
