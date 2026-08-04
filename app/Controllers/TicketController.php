<?php

namespace App\Controllers;

use App\Models\RaffleTicket;

class TicketController
{
    public function index()
    {
        $tickets = RaffleTicket::all();

        view('Tickets/Index', [
            'tickets' => $tickets,
            'title' => 'Boletos GRL RIFAS'
        ]);
    }

public function edit()
{
    $id = (int)($_GET['id'] ?? 0);

    $ticket = RaffleTicket::findTicket($id);

    view('Raffles/EditTicket', [
        'title'  => 'Editar boleto',
        'ticket' => $ticket
    ]);
}
    public function update()
    {
        // pendiente
    }
}
