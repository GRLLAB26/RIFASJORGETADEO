<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="mb-0">🎟️ GRL RIFAS</h1>
        <small class="text-muted">
            Administra todas tus rifas desde un solo lugar.
        </small>
    </div>

    <a href="/raffles/create" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Nueva Rifa
    </a>

</div>

<?php if (empty($raffles)): ?>

    <div class="alert alert-info">
        No hay rifas registradas.
    </div>

<?php else: ?>

<div class="row g-4">

<?php foreach ($raffles as $raffle): ?>

<div class="col-md-6 col-xl-4">

    <div class="card shadow-sm h-100">

        <div class="card-body">

            <h4 class="card-title">
                🎟️ <?= htmlspecialchars($raffle->title) ?>
            </h4>

            <hr>

            <p class="mb-2">
                <strong>💰 Precio:</strong>
                $<?= number_format($raffle->ticket_price,2) ?>
            </p>

            <p class="mb-2">
                <strong>🎫 Boletos:</strong>
                <?= $raffle->total_numbers ?>
            </p>

            <p class="mb-2">
                <strong>📅 Sorteo:</strong><br>
                <?= $raffle->draw_date ?>
            </p>

            <p class="mb-3">
                <strong>Estado:</strong>

                <?php if($raffle->status == 'active'): ?>

                    <span class="badge bg-success">
                        Activa
                    </span>

                <?php else: ?>

                    <span class="badge bg-secondary">
                        <?= ucfirst($raffle->status) ?>
                    </span>

                <?php endif; ?>

            </p>

        </div>

        <div class="card-footer bg-white">

            <div class="d-flex justify-content-between">

                <a href="/raffles/show?id=<?= $raffle->id ?>"
                   class="btn btn-primary btn-sm">
                    👁 Ver
                </a>

                <a href="/raffles/edit?id=<?= $raffle->id ?>"
                   class="btn btn-warning btn-sm">
                    ✏ Editar
                </a>

                <a href="/raffles/delete?id=<?= $raffle->id ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('¿Eliminar esta rifa?');">
                    🗑 Eliminar
                </a>

            </div>

        </div>

    </div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>