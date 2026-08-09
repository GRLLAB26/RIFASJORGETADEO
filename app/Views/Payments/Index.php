<div class="container mt-4">

    <h1>💰 Pagos</h1>

    <table class="table table-bordered table-striped align-middle">

        <thead>
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
        </thead>

        <tbody>

        <?php foreach ($payments as $p): ?>

            <tr>

                <td>
                    <?= htmlspecialchars($p->title ?? '') ?>
                </td>

                <td>
                    <?= str_pad(
                        (string)($p->ticket_number ?? ''),
                        3,
                        '0',
                        STR_PAD_LEFT
                    ) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p->customer_name ?? '') ?>
                </td>

                <td>
                    <?= htmlspecialchars($p->phone ?? '') ?>
                </td>

                <td>
                    $<?= number_format((float)$p->amount, 2) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p->payment_method ?? '') ?>
                </td>

                <td>
                    <?= htmlspecialchars($p->reference ?? '') ?>
                </td>

                <!-- ESTADO DEL PAGO -->
                <td>

                    <?php if ($p->status === 'approved'): ?>

                        <span class="badge bg-success">
                            ✅ Aprobado
                        </span>

                    <?php elseif ($p->status === 'rejected'): ?>

                        <span class="badge bg-danger">
                            ❌ Rechazado
                        </span>

                    <?php else: ?>

                        <span class="badge bg-warning text-dark">
                            ⏳ Pendiente
                        </span>

                    <?php endif; ?>

                </td>

                <!-- FECHA -->
                <td>
                    <?= htmlspecialchars($p->created_at ?? '') ?>
                </td>

                <!-- ACCIONES -->
                <td>

                    <?php if ($p->status === 'pending'): ?>

                        <form method="POST"
                              action="/payments/approve"
                              style="display:inline;">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$p->id ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-success btn-sm"
                            >
                                ✅ Aprobar
                            </button>

                        </form>

                        <form method="POST"
                              action="/payments/reject"
                              style="display:inline;">

                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int)$p->id ?>"
                            >

                            <button
                                type="submit"
                                class="btn btn-danger btn-sm"
                            >
                                ❌ Rechazar
                            </button>

                        </form>

                    <?php else: ?>

                        <span class="badge bg-secondary">
                            Sin acciones
                        </span>

                    <?php endif; ?>

                </td>

                <!-- ESTADO DEL BOLETO -->
                <td>

                    <?php if (($p->payment_status ?? '') === 'paid'): ?>

                        <span class="badge bg-success">
                            🎟️ Pagado
                        </span>

                    <?php elseif (($p->payment_status ?? '') === 'reserved'): ?>

                        <span class="badge bg-warning text-dark">
                            🔒 Reservado
                        </span>

                    <?php elseif (($p->payment_status ?? '') === 'winner'): ?>

                        <span class="badge bg-primary">
                            🏆 Ganador
                        </span>

                    <?php else: ?>

                        <span class="badge bg-secondary">
                            Disponible
                        </span>

                    <?php endif; ?>

                </td>

            </tr>

        <?php endforeach; ?>

        </tbody>

    </table>

</div>
