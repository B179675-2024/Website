<?php //This file sets up a $pdo connection to the PSAW database. $pdo must be closed in files that include sql_connect.
    $sql_db = "mysql:host=127.0.0.1;dbname=s2013679_psaw;charset=utf8mb4";
    $user = "s2013679";
    $passwd = "tropical_SM00THiE";

    try {
        $pdo = new PDO($sql_db, $user, $passwd, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
?>
