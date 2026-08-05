<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RaffleTicket extends Model
{
    protected static string $table = 'raffle_tickets';

    public static function create(array $data): int
    {
        return parent::create($data);
    }

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

        return $stmt->fetch(PDO::FETCH_OBJ);
    }

public static function updateTicket(int $id, array $data)
{
    $stmt = self::db()->prepare("
        UPDATE raffle_tickets
        SET
            customer_name = ?,
            phone = ?,
            payment_reference = ?,
            payment_status = ?,
            reserved_at = ?
        WHERE id = ?
    ");

    return $stmt->execute([
        $data['customer_name'],
        $data['phone'],
        $data['payment_reference'],
        $data['payment_status'],
        $data['reserved_at'],
        $id
    ]);

    }

    public static function reserved(): array
    {
        $stmt = self::db()->query("
            SELECT *
            FROM raffle_tickets
            WHERE payment_status = 'reserved'
            ORDER BY ticket_number
        ");

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
public static function stats(): array
{
    $stmt = self::db()->query("
        SELECT 
            payment_status,
            COUNT(*) as total
        FROM raffle_tickets
        WHERE raffle_id = 2
        GROUP BY payment_status
    ");

    $stats = [
        'available' => 0,
        'reserved' => 0,
        'paid' => 0,
        'income' => 0
    ];

    foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $row){
        $stats[$row->payment_status] = $row->total;
    }

    $price = self::db()->query("
        SELECT ticket_price
        FROM raffles
        WHERE id = 2
    ")->fetch(PDO::FETCH_OBJ);

    if($price){
        $stats['income'] = $stats['paid'] * $price->ticket_price;
    }

    return $stats;
}

public static function expireReservations(int $hours = 24): int
{
    $stmt = self::db()->prepare("
        UPDATE raffle_tickets
        SET
            payment_status = 'available',
            customer_name = NULL,
            phone = NULL,
            payment_reference = NULL,
            reserved_at = NULL
        WHERE payment_status = 'reserved'
        AND reserved_at <= DATE_SUB(NOW(), INTERVAL ? HOUR)
    ");

    $stmt->execute([$hours]);

    return $stmt->rowCount();
}

}

