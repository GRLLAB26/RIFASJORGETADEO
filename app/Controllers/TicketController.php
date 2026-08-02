class TicketController
{
    public function edit()
    {
        $id = (int)($_GET['id'] ?? 0);

        $ticket = RaffleTicket::findTicket($id);

        view('Tickets/Edit', [
            'title'  => 'Editar boleto',
            'ticket' => $ticket
        ]);
    }

    public function update()
    {
        // ...
    }
}