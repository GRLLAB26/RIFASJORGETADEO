<?php

namespace App\Controllers;

use App\Core\Database;
use PDO;

class AdminController
{

    public function index()
    {
        view('Admin/Index');
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
