<?php
    namespace App\Models;

    use App\Config\Database;
    use PDO;

    class UsuarioModel {
        private PDO $db;

        public function __construct() {
            $this->db = Database::getConnection();
        }

        //procura o usuario pelo email informado no login
        public function buscarPorEmail(string $email): ?array {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        }

        public function buscarPorId(int $id): ?array {
            $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        }

        //atualiza o nome e a foto do perfil (foto vazia = sem foto)
        public function atualizarPerfil(int $id, string $nome, string $foto): bool {
            $stmt = $this->db->prepare("UPDATE usuarios SET nome = :nome, foto = :foto WHERE id = :id");
            return $stmt->execute([
                ':nome' => $nome,
                ':foto' => $foto === "" ? null : $foto,
                ':id' => $id
            ]);
        }
    }
?>
