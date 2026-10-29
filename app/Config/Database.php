<?php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static ?PDO $conn = null;

    public static function getConnection(): PDO {
        //reutiliza a conexao se ja existir
        if (self::$conn) return self::$conn;

        //pega as configuracoes do banco no config.php da raiz
        $config = require __DIR__ . '/../../config.php';
        $host = $config['host'];
        $dbname = $config['db'];
        $user = $config['user'];
        $password = $config['pass'];
        $charset = $config['charset'];

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
