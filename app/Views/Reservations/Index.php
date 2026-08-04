<h1>🟡 Reservas</h1>

<div style="display:flex;gap:20px;margin:20px 0;">

    <div style="background:#ffc107;padding:15px;border-radius:10px;">
        🟡 Reservados:
        <strong><?= $stats['reserved'] ?? 0 ?></strong>
    </div>

    <div style="background:#28a745;color:white;padding:15px;border-radius:10px;">
        ✅ Pagados:
        <strong><?= $stats['paid'] ?? 0 ?></strong>
    </div>
<div style="background:#777;color:white;padding:15px;border-radius:10px;">
    🎟️ Disponibles:
    <strong><?= $stats['available'] ?? 0 ?></strong>
</div>

<div style="background:#17a2b8;color:white;padding:15px;border-radius:10px;">
    💰 Ingresos:
    <strong>$<?= $stats['income'] ?? 0 ?></strong>
</div>

</div>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>Boleto</th>
        <th>Cliente</th>
        <th>WhatsApp</th>
        <th>Estado</th>
        <th>Acciones</th>

</tr>

    <?php foreach ($tickets as $ticket): ?>

<tr>
    <td><?= str_pad($ticket->ticket_number, 3, '0', STR_PAD_LEFT) ?></td>
    <td><?= htmlspecialchars($ticket->customer_name) ?></td>

    <td>
        <?= htmlspecialchars($ticket->phone) ?>

        <br>

        <?php if(!empty($ticket->phone)): ?>
            <a href="https://wa.me/52<?= $ticket->phone ?>" target="_blank">
                📱 WhatsApp
            </a>
        <?php endif; ?>
    </td>

<td>

<?php if($ticket->payment_status == 'paid'): ?>

<span style="background:#28a745;color:white;padding:5px 10px;border-radius:15px;">
    ✅ Pagado
</span>

<?php elseif($ticket->payment_status == 'reserved'): ?>

<span style="background:#ffc107;color:#000;padding:5px 10px;border-radius:15px;">
    🟡 Reservado
</span>

<?php else: ?>

<span style="background:#777;color:white;padding:5px 10px;border-radius:15px;">
    ⚪ Disponible
</span>

<?php endif; ?>

</td>

    <td>
        ...
    </td>
</tr>

        <a href="/reservations/confirm?id=<?= $ticket->id ?>"
           onclick="return confirm('¿Confirmar pago?')">
           ✅ Confirmar
        </a>

        <br>

        <a href="/reservations/release?id=<?= $ticket->id ?>"
           onclick="return confirm('¿Liberar boleto?')">
           ❌ Liberar
        </a>

    </td>
</tr>
<?php endforeach; ?>

</table>
