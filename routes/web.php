<?php

use App\Controllers\RaffleController;
use App\Controllers\AuthController;
use App\Controllers\AdminController;
use App\Controllers\PaymentController;
use App\Controllers\WinnerController;
use App\Controllers\ReservationController;


return [

'GET' => [

    '/raffles/create' => [RaffleController::class, 'create'],
    '/raffles/show'   => [RaffleController::class, 'show'],
    '/rifa'           => [RaffleController::class, 'participate'],
    '/participar'     => [RaffleController::class, 'participate'],
    '/tickets/edit'   => [RaffleController::class, 'editTicket'],
    '/raffles'        => [RaffleController::class, 'index'],

    '/reservations'         => [ReservationController::class, 'index'],
    '/reservations/confirm' => [ReservationController::class, 'confirm'],
    '/reservations/release' => [ReservationController::class, 'release'],


    '/login'          => [AuthController::class, 'login'],
    '/logout'         => [AuthController::class, 'logout'],

    '/admin'           => [AdminController::class, 'index'],
    '/admin/customers' => [AdminController::class, 'customers'],

],

'POST' => [

    '/raffles/store'      => [RaffleController::class, 'store'],
    '/tickets/update'     => [RaffleController::class, 'updateTicket'],
    '/payments/approve'   => [PaymentController::class, 'approve'],
    '/payments/reject'    => [PaymentController::class, 'reject'],
    '/winners/draw'       => [WinnerController::class, 'draw'],

    '/login'              => [AuthController::class, 'authenticate'],

'/participar/reservar'=> [RaffleController::class, 'reserve'],

],

];
