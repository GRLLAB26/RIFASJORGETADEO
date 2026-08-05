<?php

namespace App\Models;

use App\Core\Model;

class Raffle extends Model
{
    protected static string $table = 'raffles';


    public static function activeCount(): int
    {
        $stmt = self::db()->query("
            SELECT COUNT(*)
            FROM raffles
            WHERE status = 'active'
        ");

        return (int)$stmt->fetchColumn();
    }
}
