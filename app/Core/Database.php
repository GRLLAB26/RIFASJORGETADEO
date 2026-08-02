<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $connection = null;

    public static function connect()
    {
        if (self::$connection === null) {

            $host = "127.0.0.1";
            $database = "rifas_jorge_tadeo";
            $username = "grl_user";
            $password = "GrlUnlock2026!";

            try {

                self::$connection = new PDO(
                    "mysql:host=$host;port=3306;dbname=$database;charset=utf8mb4",
                    $username,
                    $password
                );

                self::$connection->setAttribute(
                    PDO::ATTR_ERRMODE,
                    PDO::ERRMODE_EXCEPTION
                );

            } catch (PDOException $e) {

                die("Error de conexión: " . $e->getMessage());

            }
        }

        return self::$connection;
    }
}
