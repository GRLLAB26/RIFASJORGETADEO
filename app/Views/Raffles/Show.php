<?php

$available = 0;
$reserved  = 0;
$paid      = 0;
$winner    = 0;
$revenue   = 0;

foreach ($tickets as $ticket) {
    switch ($ticket->payment_status) {
        case 'available':
            $available++;
            break;

        case 'reserved':
            $reserved++;
            break;

        case 'paid':
            $paid++;
            $revenue += $raffle->ticket_price;
            break;

        case 'winner':
            $winner++;
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($raffle->title) ?></title>

<style>

body{
    font-family:Arial,sans-serif;
    background:#f5f5f5;
    padding:30px;
}

.dashboard{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
    margin:25px 0;
}

.card{
    background:white;
    border-radius:10px;
    padding:18px;
    box-shadow:0 2px 8px rgba(0,0,0,.08);
}

.card h3{
    margin:0;
    color:#666;
    font-size:15px;
}

.card p{
    margin-top:10px;
    font-size:28px;
    font-weight:bold;
}

.legend{
    display:flex;
    gap:20px;
    margin:20px 0;
    flex-wrap:wrap;
}

.dot{
    width:16px;
    height:16px;
    border-radius:50%;
    display:inline-block;
    margin-right:5px;
}

.grid{
    display:grid;
    grid-template-columns:repeat(10,70px);
    gap:10px;
    margin-top:30px;
}

.ticket{
    height:60px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    font-weight:bold;
    color:white;
}

.available{ background:#28a745; }
.reserved{ background:#ffc107; color:black; }
.paid{ background:#007bff; }
.winner{ background:#dc3545; }

</style>

</head>
<?php if (!empty($raffle->logo)): ?>

    <img src="/uploads/<?= htmlspecialchars($raffle->logo) ?>"
     style="max-width:180px;border-radius:10px;margin-bottom:20px;">

<?php endif; ?>


<?php if (!empty($raffle->image)): ?>

    <img src="/uploads/<?= htmlspecialchars($raffle->image) ?>" 
     style="max-width:350px;border-radius:10px;">

<?php endif; ?>


<h1><?= htmlspecialchars($raffle->title) ?></h1>

<p>
Precio:
<strong>$<?= number_format($raffle->ticket_price,2) ?></strong>
</p>
