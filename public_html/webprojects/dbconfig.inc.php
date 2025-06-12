<?php

function getPDOConnection()
{
    $host = "127.0.0.1";
    $port = "3306";
    $dbname = "silvenstaydb";
    $username = "root";
    $password = "root";

    $dsn = "mysql:host=$host;port=$port;dbname=$dbname";

    // for cs panel

//    $host = "localhost";
//    $port = "3306";
//    $dbname = "web1220040_silvenstaydb.sql";
//    $username = "web1220040_proj1220040";
//    $password = "pass1220040";
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
