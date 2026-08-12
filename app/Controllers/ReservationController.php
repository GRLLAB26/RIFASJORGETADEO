<?php

namespace App\Controllers;

use App\Models\RaffleTicket;

class ReservationController
{
    public function index()
    {
        $raffleId = 2;

        $tickets = RaffleTicket::reserved($raffleId);
        $stats = RaffleTicket::stats($raffleId);

        view('Reservations/Index', [
            'tickets' => $tickets,
            'stats' => $stats
        ]);
    }

    public function confirm()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('Boleto no encontrado');
        }

        $ticket = RaffleTicket::findTicket($id);

        if (!$ticket) {
            exit('Boleto no encontrado');
        }

        if ($ticket->payment_status !== 'reserved') {
            exit('El boleto ya no está reservado');
        }

        RaffleTicket::updateTicket($id, [
            'customer_name' => $ticket->customer_name,
            'phone' => $ticket->phone,
            'payment_reference' => $ticket->payment_reference,
            'payment_status' => 'paid',
            'reserved_at' => $ticket->reserved_at
        ]);

        header('Location: /reservations');
        exit;
    }

    public function release()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            exit('Boleto no encontrado');
        }

        $ticket = RaffleTicket::findTicket($id);

        if (!$ticket) {
            exit('Boleto no encontrado');
        }

        RaffleTicket::updateTicket($id, [
            'customer_name' => null,
            'phone' => null,
            'payment_reference' => null,
            'payment_status' => 'available',
            'reserved_at' => null
        ]);

        header('Location: /reservations');
        exit;
    }
}
