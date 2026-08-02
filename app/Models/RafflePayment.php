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
}