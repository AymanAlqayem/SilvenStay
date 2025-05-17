<?php

function getPDOConnection()
{
    $host = "127.0.0.1";
    $port = "3306";
    $dbname = "web1220040_clothingStore";
    $username = "root";
    $password = "root";

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    //For cs panel server.

//    $host = "localhost";
//    $port = "3306";
//    $dbname = "web1220040_clothingStore";
//    $username = "web1220040_dbuser";
//    $password = "dJQ!6Xckbt";
//
//    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    try {

        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
