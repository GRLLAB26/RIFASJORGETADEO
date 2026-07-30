<h2>Rifas</h2>

<p><a href="/raffles/create">➕ Nueva Rifa</a></p>

<?php if (empty($raffles)): ?>

    <p>No hay rifas registradas.</p>

<?php else: ?>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Precio</th>
        <th>Boletos</th>
        <th>Estado</th>
        <th>Acciones</th>
    </tr>

    <?php foreach ($raffles as $raffle): ?>
    <tr>
        <td><?= $raffle->id ?></td>
        <td><?= htmlspecialchars($raffle->title) ?></td>
        <td>$<?= number_format($raffle->ticket_price, 2) ?></td>
        <td><?= $raffle->total_numbers ?></td>
        <td><?= $raffle->status ?></td>
        <td>
            <a href="/raffles/show?id=<?= $raffle->id ?>">Ver boletos</a>
        </td>
    </tr>
    <?php endforeach; ?>

</table>

<?php endif; ?>