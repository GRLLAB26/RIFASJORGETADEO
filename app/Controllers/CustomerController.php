<?php

namespace App\Controllers;

use App\Models\User;

class CustomerController
{
    public function index()
    {
        $customers = User::customers();

        view('Admin/Customers', [
            'customers' => $customers
        ]);
    }
}
