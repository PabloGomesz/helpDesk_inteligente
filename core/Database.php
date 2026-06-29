<?php

class Database{
    private static $conexao = null;

    public static function getConnection(){
        if (self::$conexao !== null) {
            return self::$conexao;
        }

        // Carrega o array de configuração do outro arquivo.
        $config = require __DIR__ . '/../config/database.php';

        // Monta o "endereço" do banco (DSN = Data Source Name).
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            self::$conexao = new PDO($dsn, $config['usuario'], $config['senha']);
            self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Resultados vêm como array associativo: $linha['nome'] em vez de $linha[0].
            self::$conexao->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            return self::$conexao;
        } catch (PDOException $e) {
            die("Erro na conexão com o banco de dados: " . $e->getMessage());
        }

        return self::$conexao;
    }
}