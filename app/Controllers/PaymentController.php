<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PaymentController
{
    public function index()
    {
        $db = Database::connect();

        $stmt = $db->query("
            SELECT
                rp.id,
                r.title,
                rt.ticket_number,
                rt.customer_name,
                rt.phone,
                rt.payment_status,
                rp.amount,
                rp.payment_method,
                rp.reference,
                rp.status,
                rp.created_at
            FROM raffle_payments rp
            INNER JOIN raffle_tickets rt
                ON rp.raffle_ticket_id = rt.id
            INNER JOIN raffles r
                ON rt.raffle_id = r.id
            ORDER BY rp.id DESC
        ");

        $payments = $stmt->fetchAll(PDO::FETCH_OBJ);

        view('Payments/Index', [
            'payments' => $payments
        ]);
    }

public function approve()
{
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('Pago no válido.');
    }

    $db = Database::connect();

    try {
        $db->beginTransaction();

        // Obtener y bloquear el pago
        $stmt = $db->prepare("
            SELECT
                id,
                raffle_ticket_id,
                reservation_id,
                reference,
                status
            FROM raffle_payments
            WHERE id = ?
            LIMIT 1
            FOR UPDATE
        ");

        $stmt->execute([$id]);

        $payment = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$payment) {
            throw new \Exception('Pago no encontrado.');
        }

        if ($payment->status !== 'pending') {
            throw new \Exception('El pago ya fue procesado.');
        }

        // Aprobar pago
        $stmt = $db->prepare("
            UPDATE raffle_payments
            SET status = 'approved'
            WHERE id = ?
            AND status = 'pending'
        ");

        $stmt->execute([$id]);

        if ($stmt->rowCount() !== 1) {
            throw new \Exception('No se pudo aprobar el pago.');
        }

        // Marcar boleto como pagado
        $stmt = $db->prepare("
            UPDATE raffle_tickets
            SET
                payment_status = 'paid',
                payment_reference = ?,
                paid_at = NOW()
            WHERE id = ?
        ");

        $stmt->execute([
            $payment->reference,
            $payment->raffle_ticket_id
        ]);

        if ($stmt->rowCount() !== 1) {
            throw new \Exception(
                'No se pudo marcar el boleto como pagado.'
            );
        }

        /*
         * Si el pago pertenece a una reserva,
         * verificar todos los boletos de esa reserva.
         */
        if (!empty($payment->reservation_id)) {

            $stmt = $db->prepare("
                SELECT
                    COUNT(*) AS total,
                    SUM(
                        CASE
                            WHEN payment_status = 'paid' THEN 1
                            ELSE 0
                        END
                    ) AS paid
                FROM raffle_reservation_tickets rrt
                INNER JOIN raffle_tickets rt
                    ON rt.id = rrt.raffle_ticket_id
                WHERE rrt.reservation_id = ?
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            $reservation = $stmt->fetch(PDO::FETCH_OBJ);

            if (
                $reservation &&
                (int)$reservation->total > 0 &&
                (int)$reservation->total === (int)$reservation->paid
            ) {

                $stmt = $db->prepare("
                    UPDATE raffle_reservations
                    SET status = 'paid'
                    WHERE id = ?
                    AND status = 'pending'
                ");

                $stmt->execute([
                    $payment->reservation_id
                ]);
            }
        }

        $db->commit();

    } catch (\Exception $e) {

        if ($db->inTransaction()) {
            $db->rollBack();
        }

        exit($e->getMessage());
    }

    header('Location: /payments');
    exit;
}

    public function reject()
    {
        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            exit('Pago no válido.');
        }

        $db = Database::connect();

        try {
            $db->beginTransaction();

            /*
             * Obtener el pago.
             */
            $stmt = $db->prepare("
                SELECT
                    raffle_ticket_id,
                    reservation_id,
                    status
                FROM raffle_payments
                WHERE id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([$id]);

            $payment = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$payment) {
                throw new \Exception('Pago no encontrado.');
            }

            if ($payment->status !== 'pending') {
                throw new \Exception('El pago ya fue procesado.');
            }

            /*
             * Rechazar pago.
             */
            $stmt = $db->prepare("
                UPDATE raffle_payments
                SET status = 'rejected'
                WHERE id = ?
                AND status = 'pending'
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception('No se pudo rechazar el pago.');
            }

            /*
             * Liberar boleto.
             */
            $stmt = $db->prepare("
                UPDATE raffle_tickets
                SET
                    payment_status = 'available',
                    payment_reference = NULL,
                    paid_at = NULL,
                    reserved_at = NULL
                WHERE id = ?
            ");

$stmt->execute([
    $payment->raffle_ticket_id
]);

/*
 * rowCount() puede devolver 0 si el boleto
 * ya tenía estos mismos valores.
 *
 * Lo importante es que el boleto exista.
 */
$check = $db->prepare("
    SELECT id
    FROM raffle_tickets
    WHERE id = ?
    LIMIT 1
");

$check->execute([
    $payment->raffle_ticket_id
]);

if (!$check->fetch(PDO::FETCH_OBJ)) {
    throw new \Exception('El boleto no existe.');
}
            /*
             * Si pertenece a una reserva, devolverla a pending
             * mientras existan boletos que no estén pagados.
             */
            if (!empty($payment->reservation_id)) {

                $stmt = $db->prepare("
                    UPDATE raffle_reservations
                    SET status = 'pending'
                    WHERE id = ?
                    AND status <> 'paid'
                ");

                $stmt->execute([
                    $payment->reservation_id
                ]);
            }

            $db->commit();

        } catch (\Exception $e) {

            if ($db->inTransaction()) {
                $db->rollBack();
            }

            exit($e->getMessage());
        }

        header('Location: /payments');
        exit;
    }
}
