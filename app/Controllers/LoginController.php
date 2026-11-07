<?php
    namespace App\Controllers;
    use PDOException;

    use App\Models\UsuarioModel;
    use App\Views\Render;

    class LoginController {
        public function index(): string {
            if (isset($_SESSION["usuario"])) {
                header("Location: /staysharp/produtos");
                exit;
            }

            $titulo = "Entrar";
            return (new Render())->render('login/index', compact('titulo'));
        }

        public function entrar(): string {
            try {
                $email = is_string($_POST["email"] ?? null) ? trim($_POST["email"]) : "";
                $senha = is_string($_POST["senha"] ?? null) ? $_POST["senha"] : "";

                if ($email === "" || $senha === "") {
                    $_SESSION["mensagem"] = "Preencha o e-mail e a senha";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/login");
                    exit;
                }

                //confere a senha com o hash que esta salvo no banco
                $usuario = (new UsuarioModel())->buscarPorEmail($email);
                if ($usuario === null || !password_verify($senha, $usuario["senha"])) {
                    $_SESSION["mensagem"] = "E-mail ou senha incorretos";
                    $_SESSION["tipo"] = "erro";
                    header("Location: /staysharp/login");
                    exit;
                }

                //renova a sessao e guarda apenas os dados do usuario, sem a senha
                session_regenerate_id(true);
                $_SESSION["usuario"] = [
                    "id" => $usuario["id"],
                    "nome" => $usuario["nome"],
                    "email" => $usuario["email"],
                    "foto" => $usuario["foto"] ?? ""
                ];
                $_SESSION["mensagem"] = "Login realizado com sucesso";
                $_SESSION["tipo"] = "sucesso";
                header("Location: /staysharp/produtos");
                exit;
            } catch (PDOException $e) {
                $_SESSION["mensagem"] = "Não foi possível entrar no momento. Tente novamente mais tarde.";
                $_SESSION["tipo"] = "erro";
                header("Location: /staysharp/login");
                exit;
            }
        }

        public function sair(): string {
            //limpa os dados e encerra a sessao do usuario
            $_SESSION = [];
            session_destroy();
            session_start();
            session_regenerate_id(true);
            $_SESSION["mensagem"] = "Sessão encerrada com sucesso";
            $_SESSION["tipo"] = "sucesso";
            header("Location: /staysharp/login");
            exit;
        }
    }
?>
