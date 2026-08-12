<?php

namespace App\Controllers;

use App\Models\Raffle;
use App\Models\RaffleTicket;
use App\Core\Database;
use PDO;


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

    $customer = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $customer = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($tickets)) {
        exit('No seleccionaste boletos.');
    }

    if ($customer === '') {
        exit('El nombre del cliente es obligatorio.');
    }

    if ($phone === '') {
        exit('El WhatsApp es obligatorio.');
    }

    // Limpiar y validar IDs
    $tickets = array_values(array_unique(array_map('intval', $tickets)));

    if (in_array(0, $tickets, true)) {
        exit('Hay un boleto inválido.');
    }

    $db = Database::connect();

    try {

        $db->beginTransaction();

        /*
         * 1. Bloquear y comprobar TODOS los boletos.
         *    FOR UPDATE evita que dos clientes reserven
         *    simultáneamente el mismo boleto.
         */
        $placeholders = implode(',', array_fill(0, count($tickets), '?'));

        $stmt = $db->prepare("
            SELECT
                id,
                raffle_id,
                ticket_number,
                customer_name,
                phone,
                payment_status
            FROM raffle_tickets
            WHERE id IN ($placeholders)
            ORDER BY ticket_number
            FOR UPDATE
        ");

        $stmt->execute($tickets);

        $ticketRows = $stmt->fetchAll(PDO::FETCH_OBJ);

        if (count($ticketRows) !== count($tickets)) {
            throw new \Exception(
                'Uno o más boletos no existen.'
            );
        }

        /*
         * 2. Todos los boletos deben pertenecer a la misma rifa.
         */
        $raffleId = (int)$ticketRows[0]->raffle_id;

        foreach ($ticketRows as $ticket) {

            if ((int)$ticket->raffle_id !== $raffleId) {
                throw new \Exception(
                    'Los boletos seleccionados pertenecen a rifas diferentes.'
                );
            }

            if ($ticket->payment_status !== 'available') {
                throw new \Exception(
                    'El boleto ' .
                    str_pad(
                        $ticket->ticket_number,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) .
                    ' ya no está disponible.'
                );
            }
        }

        /*
         * 3. Obtener la información de la rifa.
         */
        $stmt = $db->prepare("
            SELECT id, title, ticket_price
            FROM raffles
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$raffleId]);

        $raffle = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$raffle) {
            throw new \Exception('La rifa no existe.');
        }

        /*
         * 4. Calcular el total.
         */
        $ticketPrice = (float)$raffle->ticket_price;
        $amount = count($ticketRows) * $ticketPrice;

        /*
         * 5. Crear UNA reserva para todos los boletos.
         */
        $stmt = $db->prepare("
            INSERT INTO raffle_reservations
            (
                raffle_id,
                customer_name,
                phone,
                amount,
                status
            )
            VALUES (?, ?, ?, ?, 'pending')
        ");

        $stmt->execute([
            $raffleId,
            $customer,
            $phone,
            $amount
        ]);

        $reservationId = (int)$db->lastInsertId();

        if ($reservationId <= 0) {
            throw new \Exception(
                'No se pudo crear la reserva.'
            );
        }

        /*
         * 6. Relacionar TODOS los boletos con la reserva.
         */
        $stmtReservationTicket = $db->prepare("
            INSERT INTO raffle_reservation_tickets
            (
                reservation_id,
                raffle_ticket_id
            )
            VALUES (?, ?)
        ");

        /*
         * 7. Reservar todos los boletos.
         */
        $stmtUpdateTicket = $db->prepare("
            UPDATE raffle_tickets
            SET
                customer_name = ?,
                phone = ?,
                payment_status = 'reserved',
                reserved_at = NOW()
            WHERE id = ?
            AND payment_status = 'available'
        ");

        foreach ($ticketRows as $ticket) {

            $stmtUpdateTicket->execute([
                $customer,
                $phone,
                $ticket->id
            ]);

            if ($stmtUpdateTicket->rowCount() !== 1) {
                throw new \Exception(
                    'El boleto ' .
                    str_pad(
                        $ticket->ticket_number,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) .
                    ' ya no está disponible.'
                );
            }

            $stmtReservationTicket->execute([
                $reservationId,
                $ticket->id
            ]);
        }

        /*
         * 8. Crear UN solo pago pendiente para toda la reserva.
         *
         * Si ya existe un pago para esta reserva,
         * no crear otro.
         */
        $stmt = $db->prepare("
            SELECT id
            FROM raffle_payments
            WHERE reservation_id = ?
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([
            $reservationId
        ]);

        $existingPayment = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$existingPayment) {

            /*
             * raffle_ticket_id sigue siendo obligatorio actualmente,
             * así que usamos el primer boleto como referencia.
             */
            $firstTicketId = (int)$ticketRows[0]->id;

            $stmt = $db->prepare("
                INSERT INTO raffle_payments
                (
                    raffle_ticket_id,
                    reservation_id,
                    amount,
                    payment_method,
                    reference,
                    status
                )
                VALUES (?, ?, ?, NULL, NULL, 'pending')
            ");

            $stmt->execute([
                $firstTicketId,
                $reservationId,
                $amount
            ]);
        }

        /*
         * 9. Confirmar TODO.
         */
        $db->commit();

        /*
         * Obtener los boletos nuevamente para la vista.
         */
        $reservedTickets = [];

        foreach ($ticketRows as $ticket) {
            $reservedTickets[] = RaffleTicket::findTicket(
                (int)$ticket->id
            );
        }

        /*
         * Mostrar comprobante de reserva.
         */
        view('Raffles/ReservationSuccess', [
            'customer' => $customer,
            'phone' => $phone,
            'tickets' => $reservedTickets,
            'raffle' => $raffle,
            'reservationId' => $reservationId,
            'amount' => $amount
        ]);

    } catch (\Exception $e) {

        if ($db->inTransaction()) {
            $db->rollBack();
        }

        exit(
            'No se pudo realizar la reserva: ' .
            htmlspecialchars($e->getMessage())
        );
    }
}

}
