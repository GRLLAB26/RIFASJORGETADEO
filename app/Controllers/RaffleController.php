<?php

namespace App\Controllers;

use App\Models\Raffle;
use App\Models\RaffleTicket;

class RaffleController
{
    public function index()
    {
        $raffles = Raffle::all();

        view('Raffles/Index', [
    'raffles' => $raffles
]);
    }

    public function create()
    {
        view('Raffles/Create');
    }

   public function store()
{
    $raffleId = Raffle::create([
    'title'         => $_POST['title'],
    'description'   => $_POST['description'],
    'ticket_price'  => $_POST['ticket_price'],
    'total_numbers' => $_POST['total_numbers'],
    'draw_date'     => $_POST['draw_date'],
    'whatsapp'      => $_POST['whatsapp'],
    'clabe'         => $_POST['clabe'],
    'status'        => 'active'
]);

    for ($i = 0; $i < (int) $_POST['total_numbers']; $i++) {

        RaffleTicket::create([
            'raffle_id' => $raffleId,
            'ticket_number' => $i + 1,
            'customer_name' => null,
            'phone' => null,
            'payment_status' => 'available',
            'payment_reference' => null,
        ]);

    }
    header('Location: /dashboard');

    exit;
   }
   public function show()
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        exit('Rifa no encontrada.');
    }

    $raffle = Raffle::find($id);

    if (!$raffle) {
        exit('La rifa no existe.');
    }

    $tickets = RaffleTicket::byRaffle($id);

   view('Raffles/Show', [
    'raffle' => $raffle,
    'tickets' => $tickets,
]);
}
public function editTicket()
{
    $id = (int) ($_GET['id'] ?? 0);

    if ($id <= 0) {
        exit('Boleto no encontrado.');
    }

    $ticket = RaffleTicket::findTicket($id);

    if (!$ticket) {
        exit('El boleto no existe.');
    }

    view('Raffles/EditTicket', [
        'ticket' => $ticket,
    ]);
}

public function updateTicket()
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('Boleto no válido.');
    }

RaffleTicket::updateTicket($id, [
    'customer_name'      => $_POST['customer_name'] ?? '',
    'phone'              => $_POST['phone'] ?? '',
    'payment_reference'  => $_POST['payment_reference'] ?? '',
    'payment_status'     => $_POST['payment_status'] ?? 'available',
    'reserved_at'        => date('Y-m-d H:i:s')
]);

    header('Location: /raffles/show?id=' . $ticket->raffle_id);
    exit;
}
public function participate()
{
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        exit('Rifa no encontrada');
    }

    $raffle = Raffle::find($id);

    if (!$raffle) {
        exit('La rifa no existe');
    }

    $tickets = RaffleTicket::byRaffle($id);

    view('Raffles/Participate', [
        'raffle' => $raffle,
        'tickets' => $tickets
    ]);

}

public function reserve()
{
    $tickets = $_POST['tickets'] ?? [];

    $customer = $_POST['customer_name'] ?? '';
    $phone = $_POST['phone'] ?? '';

    if (empty($tickets)) {
        exit('No seleccionaste boletos.');
    }

    foreach ($tickets as $ticketId) {

        RaffleTicket::updateTicket($ticketId, [
            'customer_name'      => $customer,
            'phone'              => $phone,
            'payment_reference'  => null,
            'payment_status'     => 'reserved',
            'reserved_at'        => date('Y-m-d H:i:s'),
        ]);

    }

    $reservedTickets = [];

    foreach ($tickets as $ticketId) {

        $ticket = RaffleTicket::findTicket($ticketId);

        if ($ticket) {
            $reservedTickets[] = $ticket;
        }

    }

    $raffle = Raffle::find($reservedTickets[0]->raffle_id);

    view('Raffles/ReservationSuccess', [
        'customer' => $customer,
        'phone' => $phone,
        'tickets' => $reservedTickets,
        'raffle' => $raffle
    ]);

}

}
