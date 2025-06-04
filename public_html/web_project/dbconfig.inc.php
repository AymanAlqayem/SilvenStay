<?php

function getPDOConnection()
{
    $host = "127.0.0.1";
    $port = "3306";
    $dbname = "flatrent";
    $username = "root";
    $password = "root";

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    try {

        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
