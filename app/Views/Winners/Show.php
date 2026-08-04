<h1>🏆 Ganador de la Rifa</h1>

<?php if($winner): ?>

<div class="card mt-4">

    <div class="card-body">

        <h3 class="text-success">
            🎉 <?= $winner->title ?>
        </h3>

        <hr>

        <p>
            <strong>🎟️ Número ganador:</strong>
            <?= $winner->ticket_number ?>
        </p>

        <p>
            <strong>👤 Nombre:</strong>
            <?= $winner->winner_name ?>
        </p>

        <p>
            <strong>📱 Teléfono:</strong>
            <?= $winner->winner_phone ?>
        </p>

        <p>
            <strong>📅 Fecha del sorteo:</strong>
            <?= $winner->created_at ?>
        </p>

        <a href="/winners" class="btn btn-primary">
            ← Volver
        </a>

    </div>

</div>

<?php else: ?>

<div class="alert alert-warning">
    No hay ganador registrado todavía.
</div>

<?php endif; ?>
