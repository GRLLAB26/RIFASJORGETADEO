<div class="container mt-4">

<h1>📊 Reportes GRL RIFAS</h1>

<hr>


<div class="row g-4">


<div class="col-md-3">
<div class="card shadow p-3">
<h5>🎟️ Rifas</h5>
<h2><?= $stats['raffles'] ?></h2>
</div>
</div>


<div class="col-md-3">
<div class="card shadow p-3">
<h5>🎫 Boletos</h5>
<h2><?= $stats['tickets'] ?></h2>
</div>
</div>


<div class="col-md-3">
<div class="card shadow p-3">
<h5>✅ Pagados</h5>
<h2><?= $stats['paid'] ?></h2>
</div>
</div>


<div class="col-md-3">
<div class="card shadow p-3">
<h5>💰 Ingresos</h5>
<h2>
$<?= number_format($stats['income'],2) ?>
</h2>
</div>
</div>


</div>


<hr class="mt-5">


<h2>📋 Ventas por Rifa</h2>


<table class="table table-bordered table-striped mt-3">

<tr>
<th>Rifa</th>
<th>Total Boletos</th>
<th>Vendidos</th>
<th>Disponibles</th>
<th>Precio</th>
<th>Total</th>
</tr>


<?php foreach($raffleReports as $r): ?>


<tr>

<td>
<?= $r->title ?>
</td>


<td>
<?= $r->total_numbers ?>
</td>


<td>
<?= $r->sold ?>
</td>


<td>
<?= $r->total_numbers - $r->sold ?>
</td>


<td>
$<?= number_format($r->ticket_price,2) ?>
</td>


<td>
$<?= number_format($r->total,2) ?>
</td>


</tr>


<?php endforeach; ?>


</table>


</div>
