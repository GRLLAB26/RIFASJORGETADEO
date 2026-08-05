<?php

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Models/RaffleTicket.php';

use App\Models\RaffleTicket;

$hours = 24;

try {

    $released = RaffleTicket::expireReservations($hours);

    echo date('Y-m-d H:i:s') .
        " - Reservas liberadas: " .
        $released .
        PHP_EOL;

} catch (Exception $e) {

    echo "Error: " . $e->getMessage() . PHP_EOL;

}
