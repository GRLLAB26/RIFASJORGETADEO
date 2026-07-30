<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RaffleTicket extends Model
{
    protected static string $table = 'raffle_tickets';

    public static function byRaffle(int $raffleId): array
    {
        $stmt = self::db()->prepare("
            SELECT *
            FROM raffle_tickets
            WHERE raffle_id = ?
            ORDER BY ticket_number
        ");

        $stmt->execute([$raffleId]);

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public static function findTicket(int $id)
{
    $stmt = self::db()->prepare("
        SELECT *
        FROM raffle_tickets
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$id]);

    return $stmt->fetch(\PDO::FETCH_OBJ);
}
public static function updateTicket(int $id, array $data): bool
{
    $stmt = self::db()->prepare("
        UPDATE raffle_tickets
        SET
            customer_name = ?,
            phone = ?,
            payment_reference = ?,
            payment_status = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $data['customer_name'],
        $data['phone'],
        $data['payment_reference'],
        $data['payment_status'],
        $id
    ]);
}
}