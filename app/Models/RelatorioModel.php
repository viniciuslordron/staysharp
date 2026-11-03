<?php
    namespace App\Models;

    use App\Config\Database;
    use PDO;

    class RelatorioModel {
        private PDO $db;

        public function __construct() {
            $this->db = Database::getConnection();
        }

        //soma o estoque dos tamanhos de todos os produtos da categoria
        public function estoquePorCategoria(): array {
            $sql = "SELECT c.id, c.nome, COALESCE(SUM(t.estoque), 0) AS estoque_total
                    FROM categorias c
                    LEFT JOIN produtos p ON p.categoria_id = c.id
                    LEFT JOIN tamanhos t ON t.produto_id = p.id
                    GROUP BY c.id, c.nome
                    ORDER BY c.nome";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function estoquePorProduto(string $status = ""): array {
            $sql = "SELECT p.id, p.nome, c.nome AS categoria, COALESCE(SUM(t.estoque), 0) AS estoque_total
                    FROM produtos p
                    JOIN categorias c ON c.id = p.categoria_id
                    LEFT JOIN tamanhos t ON t.produto_id = p.id
                    GROUP BY p.id, p.nome, c.nome";

            //o status do produto considera a soma de todos os seus tamanhos
            if ($status === "em_estoque") {
                $sql .= " HAVING COALESCE(SUM(t.estoque), 0) > 0";
            } elseif ($status === "esgotado") {
                $sql .= " HAVING COALESCE(SUM(t.estoque), 0) = 0";
            }

            $sql .= " ORDER BY c.nome, p.id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public function estoquePorTamanho(string $status = ""): array {
            $sql = "SELECT p.id AS produto_id, p.nome AS produto, c.nome AS categoria,
                           t.tamanho, COALESCE(t.estoque, 0) AS estoque
                    FROM produtos p
                    JOIN categorias c ON c.id = p.categoria_id
                    LEFT JOIN tamanhos t ON t.produto_id = p.id";

            //o LEFT JOIN tambem mostra produtos que ainda nao possuem tamanhos
            if ($status === "em_estoque") {
                $sql .= " WHERE COALESCE(t.estoque, 0) > 0";
            } elseif ($status === "esgotado") {
                $sql .= " WHERE COALESCE(t.estoque, 0) = 0";
            }

            $sql .= " ORDER BY c.nome, p.id, FIELD(t.tamanho, 'P', 'M', 'G', 'GG', 'Único'), t.tamanho";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>
