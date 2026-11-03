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
    }
?>
