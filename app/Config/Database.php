<?php

namespace App\Config;

use PDO;

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

        self::$conn = new PDO($dns, $user, $password);
        //permite tratar os erros do banco com try/catch nos controllers
        self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return self::$conn;
    }
}
?>
