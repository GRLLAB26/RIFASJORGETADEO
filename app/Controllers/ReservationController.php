<?php

namespace App\Controllers;

use App\Models\RaffleTicket;

class ReservationController
{
public function index()
{
    $tickets = RaffleTicket::reserved();
    $stats = RaffleTicket::stats();

    view('Reservations/Index', [
        'tickets' => $tickets,
        'stats' => $stats
    ]);
}

    public function confirm()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            exit('Boleto no encontrado');
        }

$ticket = RaffleTicket::findTicket($id);

RaffleTicket::updateTicket($id, [
    'customer_name' => $ticket->customer_name,
    'phone' => $ticket->phone,
    'payment_reference' => $ticket->payment_reference,
    'payment_status' => 'paid'

        ]);

        header('Location: /reservations');
        exit;
    }


    public function release()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            exit('Boleto no encontrado');
        }

        RaffleTicket::updateTicket($id, [
            'customer_name' => null,
            'phone' => null,
            'payment_reference' => null,
            'payment_status' => 'available'
        ]);

        header('Location: /reservations');
        exit;
    }
}
