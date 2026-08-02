<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class User extends Model
{
    protected static string $table = 'users';


    public static function findByEmail(string $email)
    {
        $stmt = self::db()->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    public static function customers()
    {
        $stmt = self::db()->query("
            SELECT *
            FROM users
            WHERE role = 'customer'
            ORDER BY id DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
