<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class TamanhoModel
{
    private $pdo;

    public function __construct($pdo = null)
    {
        $this->pdo = $pdo ?? Database::getConnection();
    }

    // Tamanhos de um produto
    public function listarPorProduto($produtoId)
    {
        $sql = "SELECT * FROM tamanhos WHERE produto_id = ?
                ORDER BY FIELD(tamanho, 'P', 'M', 'G', 'GG', 'Único'), tamanho";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$produtoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tamanhos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function existe($produtoId, $tamanho)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM tamanhos WHERE produto_id = ? AND tamanho = ?");
        $stmt->execute([$produtoId, $tamanho]);
        return $stmt->fetchColumn() > 0;
    }

    public function criar($produtoId, $tamanho, $estoque)
    {
        $stmt = $this->pdo->prepare("INSERT INTO tamanhos (produto_id, tamanho, estoque) VALUES (?, ?, ?)");
        $stmt->execute([$produtoId, $tamanho, $estoque]);
        return $this->pdo->lastInsertId();
    }

    public function atualizar($id, $tamanho, $estoque)
    {
        $stmt = $this->pdo->prepare("UPDATE tamanhos SET tamanho = ?, estoque = ? WHERE id = ?");
        return $stmt->execute([$tamanho, $estoque, $id]);
    }

    public function excluir($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM tamanhos WHERE id = ?");
        return $stmt->execute([$id]);
    }

    // Soma do estoque se for = 0 produto esgotado
    public function estoqueTotal($produtoId)
    {
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(estoque), 0) FROM tamanhos WHERE produto_id = ?");
        $stmt->execute([$produtoId]);
        return (int) $stmt->fetchColumn();
    }
}