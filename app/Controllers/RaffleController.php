public function updateTicket()
{
    $id = (int) ($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('Boleto no válido.');
    }

    // Buscar el boleto antes de actualizarlo
    $ticket = RaffleTicket::findTicket($id);

    if (!$ticket) {
        exit('El boleto no existe.');
    }

    RaffleTicket::updateTicket($id, [
        'customer_name'     => $_POST['customer_name'] ?? '',
        'phone'             => $_POST['phone'] ?? '',
        'payment_reference' => $_POST['payment_reference'] ?? '',
        'payment_status'    => $_POST['payment_status'] ?? 'available',
        'reserved_at'       => (
            ($_POST['payment_status'] ?? 'available') === 'reserved'
                ? date('Y-m-d H:i:s')
                : null
        )
    ]);

    header('Location: /raffles/show?id=' . $ticket->raffle_id);
    exit;
}
