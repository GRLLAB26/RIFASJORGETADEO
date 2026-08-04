<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class ReportController
{
    public function index()
    {
        $db = Database::connect();


        $stats = [];


        // Total de rifas
        $stats['raffles'] = $db->query("
            SELECT COUNT(*) FROM raffles
        ")->fetchColumn();


        // Total boletos
        $stats['tickets'] = $db->query("
            SELECT COUNT(*) FROM raffle_tickets
        ")->fetchColumn();


        // Boletos pagados
        $stats['paid'] = $db->query("
            SELECT COUNT(*)
            FROM raffle_tickets
            WHERE payment_status='paid'
        ")->fetchColumn();


        // Ingresos
        $stats['income'] = $db->query("
            SELECT 
            COALESCE(SUM(r.ticket_price),0)
            FROM raffle_tickets rt
            INNER JOIN raffles r
            ON rt.raffle_id = r.id
            WHERE rt.payment_status IN ('paid','winner')
        ")->fetchColumn();


        // Reporte por rifa
        $stmt = $db->query("
            SELECT
                r.title,
                r.total_numbers,
                COUNT(rt.id) AS sold,
                r.ticket_price,
                (COUNT(rt.id) * r.ticket_price) AS total
            FROM raffles r
            LEFT JOIN raffle_tickets rt
            ON rt.raffle_id = r.id
            AND rt.payment_status IN ('paid','winner')
            GROUP BY r.id
            ORDER BY r.id DESC
        ");


        $raffleReports = $stmt->fetchAll(PDO::FETCH_OBJ);


        view('Reports/Index', [
            'stats' => $stats,
            'raffleReports' => $raffleReports
        ]);
    }
}
