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

    /**
     * Obtener todos los boletos de una rifa.
     */
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

    /**
     * Obtener un boleto por ID.
     */
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

    /**
     * Actualizar un boleto.
     */
    public static function updateTicket(int $id, array $data): bool
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
            $data['customer_name'] ?? null,
            $data['phone'] ?? null,
            $data['payment_reference'] ?? null,
            $data['payment_status'] ?? 'available',
            $data['reserved_at'] ?? null,
            $id
        ]);
    }

    /**
     * Obtener boletos reservados de una rifa.
     */
public static function reserved(int $raffleId): array
{
    $stmt = self::db()->prepare("
        SELECT *
        FROM raffle_tickets
        WHERE raffle_id = ?
          AND payment_status = 'reserved'
        ORDER BY ticket_number
    ");

    $stmt->execute([$raffleId]);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}


    /**
     * Estadísticas de una rifa.
     */
    public static function stats(int $raffleId): array
    {
        $stmt = self::db()->prepare("
            SELECT
                payment_status,
                COUNT(*) AS total
            FROM raffle_tickets
            WHERE raffle_id = ?
            GROUP BY payment_status
        ");

        $stmt->execute([$raffleId]);

        $stats = [
            'available' => 0,
            'reserved'  => 0,
            'paid'      => 0,
            'income'    => 0
        ];

        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row) {
            if (array_key_exists($row->payment_status, $stats)) {
                $stats[$row->payment_status] = (int) $row->total;
            }
        }

        $stmt = self::db()->prepare("
            SELECT ticket_price
            FROM raffles
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$raffleId]);

        $price = $stmt->fetch(PDO::FETCH_OBJ);

        if ($price) {
            $stats['income'] =
                $stats['paid'] * (float) $price->ticket_price;
        }

        return $stats;
    }

    /**
     * Liberar reservas antiguas.
     */
    public static function expireReservations(
        int $raffleId,
        int $hours = 24
    ): int {
        $stmt = self::db()->prepare("
            UPDATE raffle_tickets
            SET
                payment_status = 'available',
                customer_name = NULL,
                phone = NULL,
                payment_reference = NULL,
                reserved_at = NULL
            WHERE raffle_id = ?
              AND payment_status = 'reserved'
              AND reserved_at IS NOT NULL
              AND reserved_at <= DATE_SUB(NOW(), INTERVAL ? HOUR)
        ");

        $stmt->execute([
            $raffleId,
            $hours
        ]);

        return $stmt->rowCount();
    }

    /**
     * Contar boletos vendidos de una rifa.
     */
    public static function soldCount(int $raffleId): int
    {
        $stmt = self::db()->prepare("
            SELECT COUNT(*)
            FROM raffle_tickets
            WHERE raffle_id = ?
              AND payment_status IN ('paid', 'winner')
        ");

        $stmt->execute([$raffleId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Contar boletos reservados de una rifa.
     */
    public static function reservedCount(int $raffleId): int
    {
        $stmt = self::db()->prepare("
            SELECT COUNT(*)
            FROM raffle_tickets
            WHERE raffle_id = ?
              AND payment_status = 'reserved'
        ");

        $stmt->execute([$raffleId]);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Reservar un boleto solamente si sigue disponible.
     *
     * Esta operación es atómica y evita que dos personas
     * puedan reservar el mismo boleto simultáneamente.
     */
    public static function reserveIfAvailable(
        int $id,
        string $customer,
        string $phone
    ): bool {
        $stmt = self::db()->prepare("
            UPDATE raffle_tickets
            SET
                customer_name = ?,
                phone = ?,
                payment_status = 'reserved',
                reserved_at = NOW()
            WHERE id = ?
              AND payment_status = 'available'
        ");

        $stmt->execute([
            $customer,
            $phone,
            $id
        ]);

        return $stmt->rowCount() === 1;
    }
}
