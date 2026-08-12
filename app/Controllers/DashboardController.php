<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Raffle;

class DashboardController
{
    public function index(): void
    {
        $raffles = Raffle::all();

        view('Dashboard/Index', [
            'title' => 'Dashboard',
            'raffles' => $raffles,
            'totalRaffles' => count($raffles),
        ]);
    }
}
