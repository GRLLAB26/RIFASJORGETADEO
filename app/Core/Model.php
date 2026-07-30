<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected static string $table;

    protected static function db(): PDO
    {
        return Database::connect();
    }

    public static function all(): array
    {
        $table = static::$table;

        $stmt = self::db()->query("SELECT * FROM {$table}");

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public static function find(int $id)
    {
        $table = static::$table;

        $stmt = self::db()->prepare("SELECT * FROM {$table} WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public static function create(array $data): int
    {
        $table = static::$table;

        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $stmt = self::db()->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) self::db()->lastInsertId();
    }
}