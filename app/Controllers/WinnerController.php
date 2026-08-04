<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class WinnerController
{

    public function index()
    {
        $db = Database::connect();

        $stmt = $db->query("
            SELECT 
                r.id,
                r.title,
                r.status,
                COUNT(rt.id) AS paid_tickets
            FROM raffles r

            LEFT JOIN raffle_tickets rt
            ON rt.raffle_id = r.id
            AND rt.payment_status = 'paid'

            GROUP BY r.id
            ORDER BY r.id DESC
        ");

        $raffles = $stmt->fetchAll(PDO::FETCH_OBJ);

        view('Winners/Index', [
            'raffles' => $raffles
        ]);
    }


    public function draw()
    {
        $raffle_id = (int)($_POST['raffle_id'] ?? 0);

        $db = Database::connect();


        // Buscar boleto pagado al azar
        $stmt = $db->prepare("
            SELECT *
            FROM raffle_tickets
            WHERE raffle_id = ?
            AND payment_status = 'paid'
            ORDER BY RAND()
            LIMIT 1
        ");

        $stmt->execute([$raffle_id]);

        $ticket = $stmt->fetch(PDO::FETCH_OBJ);


        if(!$ticket){
            exit('No hay boletos pagados para esta rifa');
        }


        // Guardar ganador
        $stmt = $db->prepare("
            INSERT INTO raffle_winners
            (
                raffle_id,
                raffle_ticket_id,
                winner_name,
                winner_phone
            )
            VALUES (?,?,?,?)
        ");


        $stmt->execute([
            $raffle_id,
            $ticket->id,
            $ticket->customer_name,
            $ticket->phone
        ]);


        // Marcar boleto ganador
        $stmt = $db->prepare("
            UPDATE raffle_tickets
            SET payment_status = 'winner'
            WHERE id = ?
        ");

        $stmt->execute([
            $ticket->id
        ]);


        // Finalizar rifa
        $stmt = $db->prepare("
            UPDATE raffles
            SET status = 'finished'
            WHERE id = ?
        ");

        $stmt->execute([
            $raffle_id
        ]);


        header('Location: /winners');
        exit;
    }

public function show()
{
    $db = Database::connect();

    $stmt = $db->query("
        SELECT 
            rw.*,
            r.title,
            rt.ticket_number
        FROM raffle_winners rw
        INNER JOIN raffles r
        ON rw.raffle_id = r.id
        INNER JOIN raffle_tickets rt
        ON rw.raffle_ticket_id = rt.id
        ORDER BY rw.id DESC
        LIMIT 1
    ");

    $winner = $stmt->fetch(PDO::FETCH_OBJ);

    view('Winners/Show', [
        'winner' => $winner
    ]);
}
}
