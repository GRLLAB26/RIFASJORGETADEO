<?php

namespace App\Controllers;

use App\Models\Raffle;

class DashboardController
{
    public function index()
    {
        $raffles = Raffle::all();

        $totalRaffles = count($raffles);

        view('Dashboard/Index', [
            'title' => 'Dashboard',
            'raffles' => $raffles,
            'totalRaffles' => $totalRaffles,
        ]);
    }
}