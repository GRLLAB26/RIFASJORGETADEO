<?php

use App\Controllers\DashboardController;
use App\Controllers\RaffleController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\CustomerController;

return [

    'GET' => [

    '/'                => [DashboardController::class, 'index'],

    '/dashboard'       => [DashboardController::class, 'index'],

    '/raffles'         => [RaffleController::class, 'index'],
    '/raffles/create'  => [RaffleController::class, 'create'],
    '/raffles/show'    => [RaffleController::class, 'show'],
    '/rifa'            => [RaffleController::class, 'show'],
    '/tickets/edit'    => [RaffleController::class, 'editTicket'],
    '/login'           => [AuthController::class, 'login'],
    '/logout'          => [AuthController::class, 'logout'],
    '/admin'           => [AdminController::class, 'index'],
    '/admin/customers' => [AdminController::class, 'customers'],


],

    'POST' => [

        '/raffles/store'    => [RaffleController::class, 'store'],
        '/tickets/update'   => [RaffleController::class, 'updateTicket'],
        '/login' => [AuthController::class, 'authenticate'],
    ],

];
