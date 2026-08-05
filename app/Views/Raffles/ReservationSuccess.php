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

.ticket-box{
    background:white;
    max-width:500px;
    margin:auto;
    padding:25px;
    border-radius:15px;
    text-align:center;
    box-shadow:0 0 10px #ccc;
}

.logo{
    max-width:180px;
    margin-bottom:15px;
}

.premio{
    max-width:300px;
    border-radius:10px;
}

h1{
    color:#28a745;
}

.numero{
    display:inline-block;
    background:#ffc107;
    padding:12px 18px;
    margin:5px;
    border-radius:8px;
    font-weight:bold;
}

.info{
    text-align:left;
    margin-top:20px;
}
</style>

</head>

<body>

<div class="ticket-box">

<?php if (!empty($raffle->logo)): ?>

<img class="logo"
src="/uploads/<?= htmlspecialchars($raffle->logo) ?>">

<?php endif; ?>


<h1>🎉 Reserva Confirmada</h1>


<?php if (!empty($raffle->image)): ?>

<img class="premio"
src="/uploads/<?= htmlspecialchars($raffle->image) ?>">

<?php endif; ?>

<h2>
<?= htmlspecialchars($raffle->title) ?>
</h2>


<div class="info">

<p>
👤 Cliente:
<strong><?= htmlspecialchars($customer) ?></strong>
</p>

<p>
📱 WhatsApp:
<strong><?= htmlspecialchars($phone) ?></strong>
</p>

<p>
📅 Fecha:
<strong><?= date('d/m/Y H:i') ?></strong>
</p>

</div>


<h3>🎟️ Tus boletos:</h3>


<?php foreach($tickets as $ticket): ?>

<span class="numero">
<?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?>
</span>

<?php endforeach; ?>


<p>
🟡 Estado:
<strong>Reservado</strong>
</p>


<p>
¡Gracias por participar! 
</p>

<a href="https://wa.me/52<?= preg_replace('/[^0-9]/','',$raffle->whatsapp) ?>?text=<?= urlencode(
"Nueva reserva de rifa

Premio: ".$raffle->title."

Cliente: ".$customer."

WhatsApp: ".$phone."

Boletos:
".implode(', ', array_map(function($ticket){
    return str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT);
}, $tickets))."

Estado: Reservado

Gracias por participar"
) ?>"

style="
display:inline-block;
margin-top:20px;
padding:12px 20px;
background:#25D366;
color:white;
text-decoration:none;
border-radius:8px;
font-weight:bold;
">
📲 Enviar comprobante por WhatsApp
</a>

</div>

</body>
</html>
