<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class PaymentController
{
/**
 * Listado de pagos.
 */
public function index()
{
    $db = Database::connect();

    $stmt = $db->query("
        SELECT
            rp.id,
            rp.reservation_id,
            r.title,

            /*
             * Boleto de referencia.
             * Se mantiene para compatibilidad con pagos antiguos.
             */
            rt.ticket_number,

            /*
             * Todos los boletos asociados a la reserva.
             */
            CASE
                WHEN rp.reservation_id IS NOT NULL THEN
                    (
                        SELECT GROUP_CONCAT(
                            LPAD(rt2.ticket_number, 3, '0')
                            ORDER BY rt2.ticket_number
                            SEPARATOR ', '
                        )
                        FROM raffle_reservation_tickets rrt2
                        INNER JOIN raffle_tickets rt2
                            ON rt2.id = rrt2.raffle_ticket_id
                        WHERE rrt2.reservation_id = rp.reservation_id
                    )
                ELSE
                    LPAD(rt.ticket_number, 3, '0')
            END AS tickets,

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


    /**
     * Aprobar pago.
     *
     * Flujo:
     * 1. Bloquear el pago.
     * 2. Verificar que esté pendiente.
     * 3. Aprobarlo.
     * 4. Marcar el boleto como pagado.
     * 5. Si pertenece a una reserva, comprobar todos sus boletos.
     * 6. Marcar la reserva como paid solamente cuando todos
     *    sus boletos estén pagados.
     */
/**
 * Aprobar pago.
 *
 * Flujo:
 * 1. Bloquear el pago.
 * 2. Verificar que esté pendiente.
 * 3. Si pertenece a una reserva:
 *      - bloquear la reserva
 *      - bloquear todos sus boletos
 *      - verificar que puedan pagarse
 *      - aprobar todos los boletos
 *      - marcar la reserva como paid
 * 4. Si es un pago antiguo sin reserva:
 *      - aprobar solamente su boleto.
 */
public function approve()
{
    $id = (int)($_POST['id'] ?? 0);

    if ($id <= 0) {
        exit('Pago no válido.');
    }

    $db = Database::connect();

    try {

        $db->beginTransaction();

        /*
         * 1. Obtener y bloquear el pago.
         */
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
            throw new \Exception(
                'Pago no encontrado.'
            );
        }

        /*
         * El pago solamente puede aprobarse
         * si está pendiente.
         */
        if ($payment->status !== 'pending') {
            throw new \Exception(
                'El pago ya fue procesado.'
            );
        }

        /*
         * =====================================================
         * PAGO PERTENECIENTE A UNA RESERVA
         * =====================================================
         */
        if (!empty($payment->reservation_id)) {

            /*
             * 2. Bloquear la reserva.
             */
            $stmt = $db->prepare("
                SELECT
                    id,
                    status
                FROM raffle_reservations
                WHERE id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            $reservation = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$reservation) {
                throw new \Exception(
                    'La reserva no existe.'
                );
            }

            /*
             * Una reserva ya pagada no debe volver
             * a procesarse.
             */
            if ($reservation->status === 'paid') {
                throw new \Exception(
                    'La reserva ya está pagada.'
                );
            }

            /*
             * 3. Obtener y bloquear TODOS los boletos
             * de la reserva.
             */
            $stmt = $db->prepare("
                SELECT
                    rt.id,
                    rt.ticket_number,
                    rt.payment_status
                FROM raffle_reservation_tickets rrt
                INNER JOIN raffle_tickets rt
                    ON rt.id = rrt.raffle_ticket_id
                WHERE rrt.reservation_id = ?
                ORDER BY rt.ticket_number
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            $tickets = $stmt->fetchAll(PDO::FETCH_OBJ);

            if (!$tickets) {
                throw new \Exception(
                    'La reserva no tiene boletos asociados.'
                );
            }

            /*
             * 4. Validar el estado de TODOS los boletos.
             *
             * Deben estar reservados o, en caso de una
             * operación parcialmente procesada anterior,
             * ya pagados.
             *
             * Un boleto disponible o ganador indica
             * una inconsistencia y NO debemos aprobar.
             */
            foreach ($tickets as $ticket) {

                if (
                    $ticket->payment_status !== 'reserved' &&
                    $ticket->payment_status !== 'paid'
                ) {
                    throw new \Exception(
                        'El boleto ' .
                        str_pad(
                            $ticket->ticket_number,
                            3,
                            '0',
                            STR_PAD_LEFT
                        ) .
                        ' no está reservado y no puede aprobarse.'
                    );
                }

                if (
                    $ticket->payment_status === 'winner'
                ) {
                    throw new \Exception(
                        'El boleto ' .
                        str_pad(
                            $ticket->ticket_number,
                            3,
                            '0',
                            STR_PAD_LEFT
                        ) .
                        ' ya está marcado como ganador.'
                    );
                }
            }

            /*
             * 5. Aprobar el pago.
             */
            $stmt = $db->prepare("
                UPDATE raffle_payments
                SET status = 'approved'
                WHERE id = ?
                AND status = 'pending'
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo aprobar el pago.'
                );
            }

            /*
             * 6. Marcar TODOS los boletos reservados
             * de la reserva como pagados.
             *
             * Los que ya estaban paid se conservan.
             */
            $stmt = $db->prepare("
                UPDATE raffle_tickets rt
                INNER JOIN raffle_reservation_tickets rrt
                    ON rrt.raffle_ticket_id = rt.id
                SET
                    rt.payment_status = 'paid',
                    rt.payment_reference = ?,
                    rt.paid_at = NOW(),
                    rt.reserved_at = NULL
                WHERE rrt.reservation_id = ?
                AND rt.payment_status = 'reserved'
            ");

            $stmt->execute([
                $payment->reference,
                $payment->reservation_id
            ]);

            /*
             * 7. Verificar nuevamente que TODOS los boletos
             * de la reserva estén pagados.
             */
            $stmt = $db->prepare("
                SELECT
                    COUNT(*) AS total,
                    SUM(
                        CASE
                            WHEN rt.payment_status = 'paid'
                            THEN 1
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

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            if (
                !$result ||
                (int)$result->total <= 0
            ) {
                throw new \Exception(
                    'No se pudieron verificar los boletos de la reserva.'
                );
            }

            /*
             * La reserva solamente pasa a paid
             * cuando TODOS sus boletos están pagados.
             */
            if (
                (int)$result->total ===
                (int)$result->paid
            ) {

                $stmt = $db->prepare("
                    UPDATE raffle_reservations
                    SET status = 'paid'
                    WHERE id = ?
                    AND status <> 'paid'
                ");

                $stmt->execute([
                    $payment->reservation_id
                ]);

            } else {

                throw new \Exception(
                    'No todos los boletos de la reserva pudieron pagarse.'
                );
            }

        } else {

            /*
             * =================================================
             * PAGO INDIVIDUAL / SISTEMA ANTERIOR
             * =================================================
             */

            /*
             * 2. Bloquear el boleto.
             */
            $stmt = $db->prepare("
                SELECT
                    id,
                    ticket_number,
                    payment_status
                FROM raffle_tickets
                WHERE id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->raffle_ticket_id
            ]);

            $ticket = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$ticket) {
                throw new \Exception(
                    'El boleto no existe.'
                );
            }

            /*
             * Un boleto ganador no puede volver a pagarse.
             */
            if ($ticket->payment_status === 'winner') {
                throw new \Exception(
                    'El boleto ya está marcado como ganador.'
                );
            }

            /*
             * Un boleto ya pagado no debe procesarse otra vez.
             */
            if ($ticket->payment_status === 'paid') {
                throw new \Exception(
                    'El boleto ya está pagado.'
                );
            }

            /*
             * 3. Aprobar pago.
             */
            $stmt = $db->prepare("
                UPDATE raffle_payments
                SET status = 'approved'
                WHERE id = ?
                AND status = 'pending'
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo aprobar el pago.'
                );
            }

            /*
             * 4. Marcar boleto como pagado.
             */
            $stmt = $db->prepare("
                UPDATE raffle_tickets
                SET
                    payment_status = 'paid',
                    payment_reference = ?,
                    paid_at = NOW(),
                    reserved_at = NULL
                WHERE id = ?
                AND payment_status <> 'winner'
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
        }

        /*
         * 8. Confirmar toda la operación.
         */
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



    /**
     * Rechazar pago.
     *
     * Flujo:
     * 1. Bloquear el pago.
     * 2. Verificar que esté pendiente.
     * 3. Rechazarlo.
     * 4. Liberar el boleto.
     * 5. Si pertenece a una reserva, comprobar el resto.
     * 6. La reserva vuelve a pending mientras tenga boletos
     *    que todavía puedan pagarse.
     */
/**
 * Rechazar pago.
 *
 * Flujo:
 * 1. Bloquear el pago.
 * 2. Verificar que esté pendiente.
 * 3. Si pertenece a una reserva:
 *      - bloquear la reserva
 *      - bloquear todos sus boletos
 *      - rechazar el pago
 *      - liberar todos los boletos reservados
 *      - marcar la reserva como rejected
 * 4. Si es un pago antiguo sin reserva:
 *      - rechazar solamente su boleto
 *      - liberar el boleto
 */
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
         * 1. Obtener y bloquear el pago.
         */
        $stmt = $db->prepare("
            SELECT
                id,
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
            throw new \Exception(
                'Pago no encontrado.'
            );
        }

        /*
         * El pago solamente puede rechazarse
         * si está pendiente.
         */
        if ($payment->status !== 'pending') {
            throw new \Exception(
                'El pago ya fue procesado.'
            );
        }

        /*
         * =====================================================
         * PAGO PERTENECIENTE A UNA RESERVA
         * =====================================================
         */
        if (!empty($payment->reservation_id)) {

            /*
             * 2. Bloquear la reserva.
             */
            $stmt = $db->prepare("
                SELECT
                    id,
                    status
                FROM raffle_reservations
                WHERE id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            $reservation = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$reservation) {
                throw new \Exception(
                    'La reserva no existe.'
                );
            }

            /*
             * Una reserva ya pagada no puede rechazarse.
             */
            if ($reservation->status === 'paid') {
                throw new \Exception(
                    'La reserva ya está pagada.'
                );
            }

            /*
             * 3. Obtener y bloquear TODOS los boletos
             * de la reserva.
             */
            $stmt = $db->prepare("
                SELECT
                    rt.id,
                    rt.ticket_number,
                    rt.payment_status
                FROM raffle_reservation_tickets rrt
                INNER JOIN raffle_tickets rt
                    ON rt.id = rrt.raffle_ticket_id
                WHERE rrt.reservation_id = ?
                ORDER BY rt.ticket_number
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            $tickets = $stmt->fetchAll(PDO::FETCH_OBJ);

            if (!$tickets) {
                throw new \Exception(
                    'La reserva no tiene boletos asociados.'
                );
            }

            /*
             * 4. Rechazar el pago.
             */
            $stmt = $db->prepare("
                UPDATE raffle_payments
                SET status = 'rejected'
                WHERE id = ?
                AND status = 'pending'
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo rechazar el pago.'
                );
            }

            /*
             * 5. Liberar TODOS los boletos reservados
             * de la reserva.
             */
            $stmt = $db->prepare("
                UPDATE raffle_tickets rt
                INNER JOIN raffle_reservation_tickets rrt
                    ON rrt.raffle_ticket_id = rt.id
                SET
                    rt.payment_status = 'available',
                    rt.customer_name = NULL,
                    rt.phone = NULL,
                    rt.payment_reference = NULL,
                    rt.paid_at = NULL,
                    rt.reserved_at = NULL
                WHERE rrt.reservation_id = ?
                AND rt.payment_status = 'reserved'
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

            /*
             * 6. Marcar la reserva como rechazada.
             */
            $stmt = $db->prepare("
                UPDATE raffle_reservations
                SET status = 'rejected'
                WHERE id = ?
            ");

            $stmt->execute([
                $payment->reservation_id
            ]);

        } else {

            /*
             * =================================================
             * PAGO INDIVIDUAL / SISTEMA ANTERIOR
             * =================================================
             */

            /*
             * 2. Bloquear el boleto.
             */
            $stmt = $db->prepare("
                SELECT
                    id,
                    ticket_number,
                    payment_status
                FROM raffle_tickets
                WHERE id = ?
                LIMIT 1
                FOR UPDATE
            ");

            $stmt->execute([
                $payment->raffle_ticket_id
            ]);

            $ticket = $stmt->fetch(PDO::FETCH_OBJ);

            if (!$ticket) {
                throw new \Exception(
                    'El boleto no existe.'
                );
            }

            /*
             * Un boleto ganador no puede rechazarse.
             */
            if ($ticket->payment_status === 'winner') {
                throw new \Exception(
                    'El boleto ya está marcado como ganador.'
                );
            }

            /*
             * 3. Rechazar pago.
             */
            $stmt = $db->prepare("
                UPDATE raffle_payments
                SET status = 'rejected'
                WHERE id = ?
                AND status = 'pending'
            ");

            $stmt->execute([$id]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo rechazar el pago.'
                );
            }

            /*
             * 4. Liberar boleto.
             */
            $stmt = $db->prepare("
                UPDATE raffle_tickets
                SET
                    payment_status = 'available',
                    customer_name = NULL,
                    phone = NULL,
                    payment_reference = NULL,
                    paid_at = NULL,
                    reserved_at = NULL
                WHERE id = ?
                AND payment_status <> 'winner'
            ");

            $stmt->execute([
                $payment->raffle_ticket_id
            ]);

            if ($stmt->rowCount() !== 1) {
                throw new \Exception(
                    'No se pudo liberar el boleto.'
                );
            }
        }

        /*
         * 7. Confirmar toda la operación.
         */
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
