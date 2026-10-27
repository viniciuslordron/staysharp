<?php
    namespace App\Services;

    use App\Config\Database;
    use PDO;

    class CategoriaService {
        private PDO $db;

        public function __construct() {
            $this->db = Database::getConnection();
        }

        // lista todas as categorias em ordem alfabética
        public function listar(): array {
            $sql = "SELECT * FROM categorias ORDER BY nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // busca uma categoria pelo id; se não achar, devolve null
        public function buscarPorId(int $id): ?array {
            $sql = "SELECT * FROM categorias WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $categoria = $stmt->fetch(PDO::FETCH_ASSOC);
            return $categoria ?: null;   // retorna falso caso n ache
        }

        // insere e devolve o id criado pelo AUTO_INCREMENT
        public function criar(string $nome): int {
            $sql = "INSERT INTO categorias (nome) VALUES (:nome)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':nome' => $nome]);
            return (int) $this->db->lastInsertId();
        }

        // atualiza o nome
        public function atualizar(int $id, string $nome): bool {
            $sql = "UPDATE categorias SET nome = :nome WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome' => $nome,
                ':id' => $id
            ]);
        }

        // exclui pelo id
        public function excluir(int $id): bool {
            $sql = "DELETE FROM categorias WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        }

        // conta quantos produtos usam essa categoria (para bloquear a exclusão)
        public function contarProdutos(int $id): int {
            $sql = "SELECT COUNT(*) FROM produtos WHERE categoria_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            return (int) $stmt->fetchColumn();
        }
    }
?>