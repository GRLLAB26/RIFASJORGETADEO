<?php

use App\Controllers\RaffleController;

return [

    'GET' => [
        '/'                 => [RaffleController::class, 'index'],
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