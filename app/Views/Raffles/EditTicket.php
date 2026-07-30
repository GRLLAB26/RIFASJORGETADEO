<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar boleto</title>

<style>

body{
    font-family:Arial;
    padding:40px;
    max-width:600px;
    margin:auto;
}

input,
select{

    width:100%;
    padding:10px;
    margin-top:5px;
    margin-bottom:20px;
}

button{

    background:#28a745;
    color:white;
    border:none;
    padding:12px 20px;
    border-radius:8px;
    cursor:pointer;
}

</style>

</head>

<body>

<h1>Boleto #<?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?></h1>

<form method="POST" action="/tickets/update">

<input type="hidden" name="id" value="<?= $ticket->id ?>">

<label>Nombre</label>

<input
type="text"
name="customer_name"
value="<?= htmlspecialchars($ticket->customer_name ?? '') ?>"
>

<label>Teléfono</label>

<input
type="text"
name="phone"
value="<?= htmlspecialchars($ticket->phone ?? '') ?>"
>

<label>Referencia de pago</label>

<input
type="text"
name="payment_reference"
value="<?= htmlspecialchars($ticket->payment_reference ?? '') ?>"
>

<label>Estado</label>

<select name="payment_status">

<option value="available">Disponible</option>

<option value="reserved">Reservado</option>

<option value="paid">Pagado</option>

<option value="winner">Ganador</option>

</select>

<button>

Guardar

</button>

</form>

</body>
</html>