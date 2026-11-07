<?php
    namespace App\Controllers;
    use PDOException;

    use App\Helpers\Upload;
    use App\Models\UsuarioModel;
    use App\Models\ProdutoModel;
    use App\Models\CategoriaModel;
    use App\Views\Render;

    class ContaController {
        // painel com os atalhos da administracao
        public function painel(): string {
            $titulo = "Painel Administrador";
            $totalProdutos = count((new ProdutoModel())->listar("", ""));
            $totalCategorias = count((new CategoriaModel())->listar());

            return (new Render())->render('painel/index', compact('titulo', 'totalProdutos', 'totalCategorias'));
        }

        // tela de gerenciar conta (foto e nome)
        public function index(): string {
            $usuario = (new UsuarioModel())->buscarPorId((int) $_SESSION["usuario"]["id"]);

            if ($usuario === null) {
                header("Location: /staysharp/logout");
                exit;
            }

            $titulo = "Gerenciar conta";
            return (new Render())->render('conta/index', compact('titulo', 'usuario'));
        }

        public function atualizar(): string {
            $voltar = "/staysharp/conta";

            try {
                $model = new UsuarioModel();
                $usuario = $model->buscarPorId((int) $_SESSION["usuario"]["id"]);

                if ($usuario === null) {
                    header("Location: /staysharp/logout");
                    exit;
                }

                $nome = is_string($_POST["nome"] ?? null) ? trim($_POST["nome"]) : "";
                $erro = null;

                if ($nome === "") {
                    $erro = "Informe o nome da conta";
                } elseif (mb_strlen($nome) > 100) {
                    $erro = "O nome deve ter no máximo 100 caracteres";
                }

                $fotoAtual = $usuario["foto"] ?? "";
                $foto = $fotoAtual;

                if ($erro === null && isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {
                    [$caminho, $erro] = Upload::imagem($_FILES["foto"], "usuarios");
                    if ($erro === null) {
                        $foto = $caminho;
                    }
                } elseif ($erro === null && isset($_POST["remover_foto"])) {
                    $foto = "";
                }

                if ($erro !== null) {
                    $_SESSION["mensagem"] = $erro;
                    $_SESSION["tipo"] = "erro";
                    header("Location: $voltar");
                    exit;
                }

                $model->atualizarPerfil((int) $usuario["id"], $nome, $foto);

                // a foto antiga so e apagada depois de o banco guardar a nova
                if ($foto !== $fotoAtual) {
                    Upload::apagar($fotoAtual, "usuarios");
                }

                $_SESSION["usuario"]["nome"] = $nome;
                $_SESSION["usuario"]["foto"] = $foto;
                $_SESSION["mensagem"] = "Conta atualizada com sucesso";
                $_SESSION["tipo"] = "sucesso";
                header("Location: $voltar");
                exit;
            } catch (PDOException $e) {
                $_SESSION["mensagem"] = "Não foi possível atualizar a conta. Tente novamente.";
                $_SESSION["tipo"] = "erro";
                header("Location: $voltar");
                exit;
            }
        }
    }
?>
