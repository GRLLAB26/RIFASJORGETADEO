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

        // Buscar pago
        $stmt = $db->prepare("
            SELECT raffle_ticket_id, reference, status
            FROM raffle_payments
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        $payment = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$payment) {
            throw new \Exception('Pago no encontrado.');
        }

        if ($payment->status !== 'pending') {
            throw new \Exception('El pago ya fue procesado.');
        }


        // 1. Aprobar pago
        $stmt = $db->prepare("
            UPDATE raffle_payments
            SET status = 'approved'
            WHERE id = ?
        ");

        $stmt->execute([$id]);


        // 2. Marcar boleto como pagado
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
    throw new \Exception('No se pudo marcar el boleto como pagado.');
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
