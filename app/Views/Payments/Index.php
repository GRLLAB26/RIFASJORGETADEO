<div class="container mt-4">

<h1>💰 Pagos</h1>

<table class="table table-bordered">

<tr>
<th>Rifa</th>
<th>Boleto</th>
<th>Cliente</th>
<th>Teléfono</th>
<th>Monto</th>
<th>Método</th>
<th>Referencia</th>
<th>Estado</th>
<th>Fecha</th>
<th>Acciones</th>
<th>Estado Boleto</th>

</tr>

<?php foreach($payments as $p): ?>

<tr>

<td><?= $p->title ?></td>

<td>
<?= str_pad($p->ticket_number,3,'0',STR_PAD_LEFT) ?>
</td>

<td><?= $p->customer_name ?></td>

<td><?= $p->phone ?></td>

<td>
$<?= number_format($p->amount,2) ?>
</td>

<td>
<?= $p->payment_method ?>
</td>

<td>
<?= $p->reference ?>
</td>

<?php if($p->status == 'approved'): ?>

<span class="badge bg-success">
    Aprobado
</span>

<?php elseif($p->status == 'rejected'): ?>

<span class="badge bg-danger">
    Rechazado
</span>

<?php else: ?>

<span class="badge bg-warning">
    Pendiente
</span>

<?php endif; ?>

</td>

<td>

<?php if($p->status == 'pending'): ?>

<form method="POST" action="/payments/approve" style="display:inline;">
    <input type="hidden" name="id" value="<?= $p->id ?>">
    <button class="btn btn-success btn-sm">
        ✅ Aprobar
    </button>
</form>

<form method="POST" action="/payments/reject" style="display:inline;">
    <input type="hidden" name="id" value="<?= $p->id ?>">
    <button class="btn btn-danger btn-sm">
        ❌ Rechazar
    </button>
</form>

<?php else: ?>

<span class="badge bg-secondary">
    Sin acciones
</span>

<?php endif; ?>

</td>

</td>

</tr>

<?php endforeach; ?>

</table>

</div>
