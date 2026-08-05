<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class AdminController
{

public function index()
{
    $db = Database::connect();

    $kpis = [

        'active_raffles' => (int)$db->query("
            SELECT COUNT(*)
            FROM raffles
            WHERE status = 'active'
        ")->fetchColumn(),

        'sold_tickets' => (int)$db->query("
            SELECT COUNT(*)
            FROM raffle_tickets
            WHERE payment_status IN ('paid','winner')
        ")->fetchColumn(),

        'reserved_tickets' => (int)$db->query("
            SELECT COUNT(*)
            FROM raffle_tickets
            WHERE payment_status = 'reserved'
        ")->fetchColumn(),

        'revenue' => (float)$db->query("
            SELECT COALESCE(SUM(amount),0)
            FROM raffle_payments
            WHERE status = 'approved'
        ")->fetchColumn(),

    ];

    view('Admin/Index', [
        'kpis' => $kpis
    ]);
}

    public function customers()
    {
        $db = Database::connect();

        $stmt = $db->query("
            SELECT id, name, email, role
            FROM users
            ORDER BY id DESC
        ");

        $users = $stmt->fetchAll(PDO::FETCH_OBJ);

        view('Admin/Customers', [
            'users' => $users
        ]);
    }
}
