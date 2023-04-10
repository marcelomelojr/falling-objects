<?php

/**
 * @package Db
 * @author flavio
 * Classe config utilizada para criar a DB e Table caso nao existir
 */

class DbConfig
{

    public function __construct()
    {
        if ($_SERVER["HTTP_HOST"] == "localhost") {
            $servername = "localhost";
            $username = "root";
            $password = "";
        } else {
            $servername = "localhost";
            $username = "root";
            $password = "";
        }

        try {
            $conn = new PDO("mysql:host=$servername", $username, $password);
            // set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sqlDB = "CREATE DATABASE IF NOT EXISTS cacada_falls";
            $sqlTb = "CREATE TABLE IF NOT EXISTS cacada_falls.pontuacoes (id INT(11) NOT NULL AUTO_INCREMENT, nome VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Sem nome', email VARCHAR(255) NULL, telefone VARCHAR(20) NULL, pontos INT(11) NOT NULL DEFAULT '0', tempo INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (id))";

            $conn->exec($sqlDB);
            $conn->exec($sqlTb);
            //echo "Database created successfully<br>";
        } catch (PDOException $e) {
            //echo $sqlDB . "<br>" . $e->getMessage();
        }

        $conn = null;
    }
}
