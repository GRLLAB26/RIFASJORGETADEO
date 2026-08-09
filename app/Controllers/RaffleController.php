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
            'title'         => $_POST['title'] ?? '',
            'description'   => $_POST['description'] ?? '',
            'ticket_price'  => $_POST['ticket_price'] ?? 0,
            'total_numbers' => $_POST['total_numbers'] ?? 0,
            'draw_date'     => $_POST['draw_date'] ?? null,
            'whatsapp'      => $_POST['whatsapp'] ?? '',
            'clabe'         => $_POST['clabe'] ?? '',
            'status'        => 'active'
        ]);

        for ($i = 0; $i < (int) ($_POST['total_numbers'] ?? 0); $i++) {
            RaffleTicket::create([
                'raffle_id'         => $raffleId,
                'ticket_number'     => $i + 1,
                'customer_name'     => null,
                'phone'             => null,
                'payment_status'    => 'available',
                'payment_reference' => null
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
            'tickets' => $tickets
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
            'ticket' => $ticket
        ]);
    }

    public function updateTicket()
    {
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            exit('Boleto no válido.');
        }

        $ticket = RaffleTicket::findTicket($id);

        if (!$ticket) {
            exit('El boleto no existe.');
        }

        RaffleTicket::updateTicket($id, [
            'customer_name'     => $_POST['customer_name'] ?? '',
            'phone'             => $_POST['phone'] ?? '',
            'payment_reference' => $_POST['payment_reference'] ?? '',
            'payment_status'    => $_POST['payment_status'] ?? 'available',
            'reserved_at'       => date('Y-m-d H:i:s')
        ]);

        header('Location: /raffles/show?id=' . $ticket->raffle_id);
        exit;
    }

    public function participate()
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

        view('Raffles/Participate', [
            'raffle' => $raffle,
            'tickets' => $tickets
        ]);
    }

    public function reserve()
{
    $tickets = $_POST['tickets'] ?? [];

    $customer = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($tickets)) {
        exit('No seleccionaste boletos.');
    }

    if ($customer === '') {
        exit('Debes ingresar tu nombre.');
    }

    if ($phone === '') {
        exit('Debes ingresar tu WhatsApp.');
    }

    $db = \App\Core\Database::connect();

    try {

        $db->beginTransaction();

        /*
         * Verificar que todos los boletos existan
         * y estén disponibles.
         */
        $ticketIds = array_map('intval', $tickets);
        $ticketIds = array_values(array_unique($ticketIds));

        if (empty($ticketIds)) {
            throw new \Exception('No seleccionaste boletos válidos.');
        }

        $placeholders = implode(',', array_fill(0, count($ticketIds), '?'));

        $stmt = $db->prepare("
            SELECT
                rt.id,
                rt.raffle_id,
                rt.ticket_number,
                rt.payment_status,
                r.ticket_price
            FROM raffle_tickets rt
            INNER JOIN raffles r
                ON rt.raffle_id = r.id
            WHERE rt.id IN ($placeholders)
            FOR UPDATE
        ");

        $stmt->execute($ticketIds);

        $selectedTickets = $stmt->fetchAll(\PDO::FETCH_OBJ);

        if (count($selectedTickets) !== count($ticketIds)) {
            throw new \Exception('Uno o más boletos no existen.');
        }

        /*
         * Todos los boletos deben estar disponibles
         * y pertenecer a la misma rifa.
         */
        $raffleId = null;
        $amount = 0;

        foreach ($selectedTickets as $ticket) {

            if ($ticket->payment_status !== 'available') {
                throw new \Exception(
                    'El boleto ' . $ticket->ticket_number . ' ya no está disponible.'
                );
            }

            if ($raffleId === null) {
                $raffleId = (int) $ticket->raffle_id;
            }

            if ((int) $ticket->raffle_id !== $raffleId) {
                throw new \Exception(
                    'Los boletos seleccionados deben pertenecer a la misma rifa.'
                );
            }

            $amount += (float) $ticket->ticket_price;
        }

        /*
         * Crear la reserva principal.
         */
        $stmt = $db->prepare("
            INSERT INTO raffle_reservations
                (raffle_id, customer_name, phone, amount, status)
            VALUES
                (?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $raffleId,
            $customer,
            $phone,
            $amount
        ]);

        $reservationId = (int) $db->lastInsertId();

        if ($reservationId <= 0) {
            throw new \Exception('No se pudo crear la reserva.');
        }

        /*
         * Relacionar cada boleto con la reserva
         * y marcarlo como reservado.
         */
        foreach ($selectedTickets as $ticket) {

            $stmt = $db->prepare("
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
                $ticket->id
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo reservar el boleto ' . $ticket->ticket_number . '.'
                );
            }

            $stmt = $db->prepare("
                INSERT INTO raffle_reservation_tickets
                    (reservation_id, raffle_ticket_id)
                VALUES
                    (?, ?)
            ");

            $stmt->execute([
                $reservationId,
                $ticket->id
            ]);
        }

        $db->commit();

        /*
         * Recuperar boletos reservados para mostrar
         * la pantalla de confirmación.
         */
        $reservedTickets = [];

        foreach ($ticketIds as $ticketId) {

            $stmt = $db->prepare("
                SELECT *
                FROM raffle_tickets
                WHERE id = ?
                LIMIT 1
            ");

            $stmt->execute([$ticketId]);

            $ticket = $stmt->fetch(\PDO::FETCH_OBJ);

            if ($ticket) {
                $reservedTickets[] = $ticket;
            }
        }

        $raffle = Raffle::find($raffleId);

        if (!$raffle) {
            exit('No se encontró la rifa.');
        }

        view('Raffles/ReservationSuccess', [
            'customer'      => $customer,
            'phone'         => $phone,
            'tickets'       => $reservedTickets,
            'raffle'        => $raffle,
            'reservationId' => $reservationId,
            'amount'        => $amount
        ]);

    } catch (\Exception $e) {

        if ($db->inTransaction()) {
            $db->rollBack();
        }

        exit($e->getMessage());
    }
}

}
