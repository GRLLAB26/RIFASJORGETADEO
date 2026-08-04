<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reserva confirmada</title>

<style>
body{
    font-family:Arial;
    background:#f5f5f5;
    padding:40px;
}

.box{
    background:white;
    max-width:500px;
    margin:auto;
    padding:30px;
    border-radius:12px;
    text-align:center;
}

h1{
    color:#28a745;
}

.ticket{
    display:inline-block;
    background:#ffc107;
    padding:10px 15px;
    margin:5px;
    border-radius:8px;
    font-weight:bold;
}
</style>

</head>

<body>

<div class="box">

<h1>🎉 Reserva realizada</h1>

<p>
Gracias <strong><?= $customer ?></strong>
</p>

<p>
WhatsApp:
<strong><?= $phone ?></strong>
</p>

<h3>🎟️ Tus boletos:</h3>

<?php foreach($tickets as $ticket): ?>

<span class="ticket">
<?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?>
</span>

<?php endforeach; ?>

<br><br>

<a href="/rifa?id=2">
Volver a la rifa
</a>

</div>
