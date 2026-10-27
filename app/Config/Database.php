<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $conn = null;

    public static function getConnection(): PDO {
        //reutiliza a conexao se ja existir
        if (self::$conn) return self::$conn;

        //pega as configuracoes do banco
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $charset = $_ENV['DB_CHARSET'];

        //string de conexao
        $dns = "mysql:host=$host;dbname=$dbname;charset=$charset";

        try {
            self::$conn = new PDO($dns, $user, $password);
            return self::$conn;
        } catch (PDOException $e) {
            //mostra o erro e encerra se nao conectar
            echo $e->getMessage();
            exit;
        }
    }
}
?>
