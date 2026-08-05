<?php

namespace App\Models;

use App\Core\Model;

class RafflePayment extends Model
{
    protected string $table = 'raffle_payments';

    protected array $fillable = [
        'raffle_ticket_id',
        'amount',
        'payment_method',
        'reference',
        'proof_image',
        'status'
    ];
public static function approvedTotal(): float
{
    $stmt = self::db()->query("
        SELECT COALESCE(SUM(amount),0)
        FROM raffle_payments
        WHERE status = 'approved'
    ");

    return (float)$stmt->fetchColumn();
}


public static function pendingCount(): int
{
    $stmt = self::db()->query("
        SELECT COUNT(*)
        FROM raffle_payments
        WHERE status = 'pending'
    ");

    return (int)$stmt->fetchColumn();
}

}
