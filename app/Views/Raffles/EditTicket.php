<h2 class="mb-4">
    🎟️ Editar boleto #<?= str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) ?>
</h2>

<form method="POST" action="/tickets/update">

    <input type="hidden" name="id" value="<?= $ticket->id ?>">

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input
            type="text"
            name="customer_name"
            class="form-control"
            value="<?= htmlspecialchars($ticket->customer_name ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input
            type="text"
            name="phone"
            class="form-control"
            value="<?= htmlspecialchars($ticket->phone ?? '') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Referencia de pago</label>
        <input
            type="text"
            name="payment_reference"
            class="form-control"
            value="<?= htmlspecialchars($ticket->payment_reference ?? '') ?>">
    </div>

    <div class="mb-4">
        <label class="form-label">Estado</label>

        <select name="payment_status" class="form-select">

            <option value="available" <?= $ticket->payment_status == 'available' ? 'selected' : '' ?>>
                Disponible
            </option>

            <option value="reserved" <?= $ticket->payment_status == 'reserved' ? 'selected' : '' ?>>
                Reservado
            </option>

            <option value="paid" <?= $ticket->payment_status == 'paid' ? 'selected' : '' ?>>
                Pagado
            </option>

            <option value="winner" <?= $ticket->payment_status == 'winner' ? 'selected' : '' ?>>
                Ganador
            </option>

        </select>
    </div>

    <button type="submit" class="btn btn-success">
        <i class="bi bi-check-circle"></i> Guardar cambios
    </button>

    <a href="/raffles/show?id=<?= $ticket->raffle_id ?>" class="btn btn-secondary">
        Cancelar
    </a>

</form>