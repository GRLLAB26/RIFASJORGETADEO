<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<title><?= $raffle->title ?></title>

<style>

body{
    font-family:Arial;
    background:#f5f5f5;
    padding:20px;
}

.ticket-grid{
    display:grid;
    grid-template-columns:repeat(10,60px);
    gap:10px;
}

.ticket-label{
    cursor:pointer;
}

.ticket-label input{
    display:none;
}

.ticket{
    padding:15px;
    background:#28a745;
    color:white;
    text-align:center;
    border-radius:8px;
    cursor:pointer;
    transition:.2s;
}

.ticket-label input:checked + .ticket{
    background:#ffc107;
    color:#000;
    border:3px solid #000;
    transform:scale(1.08);
}

.ticket.used{
    background:#777;
    cursor:not-allowed;
}


.form-box{
    margin-top:30px;
    background:white;
    padding:20px;
    border-radius:10px;
    max-width:400px;
}


input[type=text]{
    width:100%;
    padding:10px;
    border-radius:5px;
    border:1px solid #ccc;
}


button{
    margin-top:20px;
    padding:12px 25px;
    background:#28a745;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:18px;
}

button:hover{
    background:#218838;
}


</style>

</head>


<body>


<h1>🎟️ <?= $raffle->title ?></h1>


<p>
💰 Precio boleto:
<strong>$<?= $raffle->ticket_price ?></strong>
</p>


<?php if (!empty($raffle->image)): ?>

<img src="/uploads/<?= $raffle->image ?>"
style="max-width:350px;border-radius:10px;">

<?php endif; ?>


<h2>Selecciona tus boletos</h2>


<form method="POST" action="/participar/reservar">


<input type="hidden" 
name="raffle_id" 
value="<?= $raffle->id ?>">



<div class="ticket-grid">


<?php foreach($tickets as $ticket): ?>


<?php if($ticket->payment_status == 'available'): ?>


<label class="ticket-label">


<input 
type="checkbox"
name="tickets[]"
value="<?= $ticket->id ?>">


<div class="ticket">

<?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?>

</div>


</label>


<?php else: ?>


<div class="ticket used">

<?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?>

</div>


<?php endif; ?>


<?php endforeach; ?>


</div>




<div class="form-box">


<h2>📝 Datos del comprador</h2>


<label>
Nombre:
</label>


<input 
type="text"
name="customer_name"
required>



<br><br>


<label>
WhatsApp:
</label>


<input 
type="text"
name="phone"
required>



<button type="submit">

🎟️ Reservar boletos

</button>


</div>


</form>


</body>

</html>
