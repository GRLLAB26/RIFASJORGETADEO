<?php

use App\Controllers\DashboardController;
use App\Controllers\RaffleController;

return [

    'GET' => [

        '/'                 => [DashboardController::class, 'index'],

        '/dashboard'        => [DashboardController::class, 'index'],

        '/raffles'          => [RaffleController::class, 'index'],
        '/raffles/create'   => [RaffleController::class, 'create'],
        '/raffles/show'     => [RaffleController::class, 'show'],
        '/tickets/edit'     => [RaffleController::class, 'editTicket'],

    ],

    'POST' => [

        '/raffles/store'    => [RaffleController::class, 'store'],
        '/tickets/update'   => [RaffleController::class, 'updateTicket'],

    ],

];