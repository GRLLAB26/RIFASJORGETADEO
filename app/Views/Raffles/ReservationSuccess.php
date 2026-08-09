cat > app/Views/Raffles/ReservationSuccess.php <<'PHP'
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Reserva Confirmada</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 30px;
            text-align: center;
        }

        .success-box {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,.10);
        }

        h1 {
            color: #198754;
        }

        .tickets {
            margin: 20px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .ticket {
            background: #ffc107;
            color: #000;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
        }

        .whatsapp {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 20px;
            background: #25D366;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }

        .whatsapp:hover {
            opacity: .9;
        }
    </style>
</head>

<body>

<div class="success-box">

    <h1>🎉 Reserva Confirmada</h1>

    <h2>
        <?= htmlspecialchars($raffle->title) ?>
    </h2>

    <p>
        👤 Cliente:
        <strong><?= htmlspecialchars($customer) ?></strong>
    </p>

    <p>
        📱 WhatsApp:
        <strong><?= htmlspecialchars($phone) ?></strong>
    </p>

    <p>
        📅 Fecha:
        <strong><?= date('d/m/Y H:i') ?></strong>
    </p>

    <h3>🎟️ Tus boletos:</h3>

    <div class="tickets">

        <?php foreach ($tickets as $ticket): ?>

            <div class="ticket">
                <?= str_pad(
                    $ticket->ticket_number,
                    3,
                    '0',
                    STR_PAD_LEFT
                ) ?>
            </div>

        <?php endforeach; ?>

    </div>

    <p>
        🟡 Estado:
        <strong>Reservado</strong>
    </p>

    <p>
        ¡Gracias por participar!
    </p>

    <?php

    $ticketNumbers = implode(', ', array_map(
        function ($ticket) {
            return str_pad(
                $ticket->ticket_number,
                3,
                '0',
                STR_PAD_LEFT
            );
        },
        $tickets
    ));

    $whatsappMessage =
        "Nueva reserva de rifa\n\n" .
        "Premio: " . $raffle->title . "\n\n" .
        "Cliente: " . $customer . "\n\n" .
        "WhatsApp: " . $phone . "\n\n" .
        "Boletos:\n" . $ticketNumbers . "\n\n" .
        "Estado: Reservado\n\n" .
        "Gracias por participar";

    $whatsappUrl =
        "https://wa.me/52" .
        preg_replace('/\D/', '', $phone) .
        "?text=" .
        urlencode($whatsappMessage);

    ?>

    <a
        href="<?= htmlspecialchars($whatsappUrl) ?>"
        class="whatsapp"
        target="_blank"
        rel="noopener"
    >
        📲 Enviar comprobante por WhatsApp
    </a>

</div>

</body>
</html>
PHP
