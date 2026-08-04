<h1>🏆 Ganadores</h1>

<table class="table table-bordered">

<tr>
    <th>Rifa</th>
    <th>Boletos Pagados</th>
    <th>Estado</th>
    <th>Acción</th>
</tr>


<?php foreach($raffles as $r): ?>

<tr>

<td>
    <?= $r->title ?>
</td>

<td>
    <?= $r->paid_tickets ?>
</td>

<td>
    <?= $r->status ?>
</td>

<td>

<td>

<?php if($r->status == 'active' && $r->paid_tickets > 0): ?>

<form method="POST" action="/winners/draw">

<input type="hidden" name="raffle_id" value="<?= $r->id ?>">

<button class="btn btn-warning">
🎲 Realizar Sorteo
</button>

</form>

<?php elseif($r->status == 'finished'): ?>

<a href="/winner" class="btn btn-success">
🏆 Ver ganador
</a>

<?php else: ?>

<span class="badge bg-secondary">
No disponible
</span>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>


</table>
