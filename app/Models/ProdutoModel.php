<?php
    namespace App\Models;

    use App\Config\Database;
    use PDO;

    class ProdutoModel {
        private PDO $db;

        public function __construct() {
            $this->db = Database::getConnection();
        }

        // lista os produtos com o nome da categoria (JOIN); os filtros são opcionais
        public function listar(string $categoria = "", string $busca = ""): array {
            $sql = "SELECT p.*, c.nome AS categoria_nome
                    FROM produtos p
                    JOIN categorias c ON c.id = p.categoria_id
                    WHERE 1 = 1";
            $params = [];

            // filtro por categoria: só entra no SQL se veio na URL
            if ($categoria !== "") {
                $sql .= " AND p.categoria_id = :categoria";
                $params[':categoria'] = $categoria;
            }

            // busca pelo nome: o % quer dizer "qualquer coisa antes e depois"
            if ($busca !== "") {
                $sql .= " AND p.nome LIKE :busca";
                $params[':busca'] = "%" . $busca . "%";
            }

            $sql .= " ORDER BY p.id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // busca um produto pelo id, com o nome da categoria; se não achar, devolve null
        public function buscarPorId(int $id): ?array {
            $sql = "SELECT p.*, c.nome AS categoria_nome
                    FROM produtos p
                    JOIN categorias c ON c.id = p.categoria_id
                    WHERE p.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);
            return $produto ?: null;
        }

        // insere e devolve o id novo
        public function criar(string $nome, string $descricao, float $preco, int $categoria_id, string $imagem, string $imagemVerso = ""): int {
            $sql = "INSERT INTO produtos (nome, descricao, preco, categoria_id, imagem, imagem_verso)
                    VALUES (:nome, :descricao, :preco, :categoria_id, :imagem, :imagem_verso)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco' => $preco,
                ':categoria_id' => $categoria_id,
                ':imagem' => $imagem,
                ':imagem_verso' => $imagemVerso
            ]);
            return (int) $this->db->lastInsertId();
        }

        // atualiza todos os campos
        public function atualizar(int $id, string $nome, string $descricao, float $preco, int $categoria_id, string $imagem, string $imagemVerso = ""): bool {
            $sql = "UPDATE produtos
                    SET nome = :nome, descricao = :descricao, preco = :preco,
                        categoria_id = :categoria_id, imagem = :imagem, imagem_verso = :imagem_verso
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':nome' => $nome,
                ':descricao' => $descricao,
                ':preco' => $preco,
                ':categoria_id' => $categoria_id,
                ':imagem' => $imagem,
                ':imagem_verso' => $imagemVerso,
                ':id' => $id
            ]);
        }

        // exclui pelo id
        public function excluir(int $id): bool {
            $sql = "DELETE FROM produtos WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id]);
        }
    }
?>