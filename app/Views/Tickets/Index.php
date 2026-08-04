
<div class="container mt-4">

    <h1>🎫 Boletos GRL RIFAS</h1>

    <hr>

    <div class="card shadow">

        <div class="card-body">

            <table class="table table-striped">

                <thead>
                    <tr>
<th>ID</th>
<th>Rifa</th>
<th>Número</th>
<th>Cliente</th>
<th>Teléfono</th>
<th>Estado</th>
<th>Acciones</th>

                    </tr>
                </thead>

                <tbody>

                <?php foreach($tickets as $ticket): ?>

                    <tr>

                        <td><?= $ticket->id ?></td>

                        <td><?= $ticket->raffle_id ?></td>

                        <td>
                            <?= str_pad($ticket->ticket_number,3,'0',STR_PAD_LEFT) ?>
                        </td>

                        <td>
                            <?= $ticket->customer_name ?? '' ?>
                        </td>

                        <td>
                            <?= $ticket->phone ?? '' ?>
                        </td>

                        <td>

<td>
    <?php if($ticket->payment_status == 'paid'): ?>

        <span class="badge bg-success">
            Pagado
        </span>

    <?php elseif($ticket->payment_status == 'reserved'): ?>

        <span class="badge bg-warning">
            Reservado
        </span>

    <?php else: ?>

        <span class="badge bg-secondary">
            Disponible
        </span>

    <?php endif; ?>
</td>

<td>
    <a href="/tickets/edit?id=<?= $ticket->id ?>" class="btn btn-sm btn-primary">
        ✏️ Editar
    </a>
</td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
