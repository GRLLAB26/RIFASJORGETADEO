<?php

$availableTickets = [];
$unavailableTickets = [];

foreach ($tickets as $ticket) {
    if ($ticket->payment_status === 'available') {
        $availableTickets[] = $ticket;
    } else {
        $unavailableTickets[] = $ticket;
    }
}

$logo = !empty($raffle->logo)
    ? '/uploads/' . $raffle->logo
    : null;

$image = !empty($raffle->image)
    ? '/uploads/' . $raffle->image
    : null;

?>

<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        <?= htmlspecialchars($raffle->title) ?> - GRL RIFAS
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            color: #222;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: #111827;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            margin-top: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,.08);
        }

        .raffle-image {
            display: block;
            max-width: 350px;
            width: 100%;
            margin: 0 auto 20px;
            border-radius: 12px;
        }

        .logo {
            display: block;
            max-width: 180px;
            max-height: 120px;
            margin: 0 auto 20px;
            object-fit: contain;
        }

        .price {
            font-size: 28px;
            font-weight: bold;
            color: #198754;
            text-align: center;
            margin: 15px 0;
        }

        .info {
            text-align: center;
            margin-bottom: 25px;
        }

        .ticket-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(65px, 1fr));
            gap: 10px;
            margin-top: 20px;
        }

        .ticket-label {
            cursor: pointer;
        }

        .ticket-label input {
            display: none;
        }

        .ticket {
            padding: 14px 5px;
            text-align: center;
            border-radius: 8px;
            background: #198754;
            color: white;
            font-weight: bold;
            transition: .2s;
        }

        .ticket:hover {
            transform: scale(1.05);
        }

        .ticket-label input:checked + .ticket {
            background: #ffc107;
            color: #000;
            outline: 3px solid #000;
        }

        .ticket.used {
            background: #999;
            cursor: not-allowed;
            opacity: .7;
        }

        .form-grid {
            display: grid;
            gap: 15px;
            margin-top: 25px;
        }

        .form-grid label {
            font-weight: bold;
        }

        .form-grid input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        .submit {
            width: 100%;
            margin-top: 20px;
            padding: 15px;
            border: none;
            border-radius: 10px;
            background: #198754;
            color: white;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit:hover {
            background: #157347;
        }

        .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 20px;
            justify-content: center;
        }

        .legend span {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
        }

        .available {
            background: #198754;
            color: white;
        }

        .selected {
            background: #ffc107;
            color: #000;
        }

        .taken {
            background: #999;
            color: white;
        }

        .back {
            display: inline-block;
            margin-top: 15px;
            color: #fff;
            text-decoration: none;
        }

    </style>

</head>

<body>

<header class="header">

    <h1>🎟️ GRL RIFAS</h1>

    <a href="/raffles" class="back">
        ← Volver
    </a>

</header>


<div class="container">

    <div class="card">

        <?php if ($logo): ?>

            <img
                src="<?= htmlspecialchars($logo) ?>"
                class="logo"
                alt="Logo"
            >

        <?php endif; ?>


        <h2 style="text-align:center;">
            <?= htmlspecialchars($raffle->title) ?>
        </h2>


        <?php if (!empty($raffle->description)): ?>

            <p class="info">
                <?= nl2br(htmlspecialchars($raffle->description)) ?>
            </p>

        <?php endif; ?>


        <?php if ($image): ?>

            <img
                src="<?= htmlspecialchars($image) ?>"
                class="raffle-image"
                alt="<?= htmlspecialchars($raffle->title) ?>"
            >

        <?php endif; ?>


        <div class="price">

            💰 $<?= number_format(
                (float) $raffle->ticket_price,
                2
            ) ?>

            <small>por boleto</small>

        </div>


        <div class="info">

            📅 Sorteo:
            <strong>
                <?= htmlspecialchars($raffle->draw_date) ?>
            </strong>

        </div>

    </div>


    <form
        method="POST"
        action="/participar/reservar"
    >

        <input
            type="hidden"
            name="raffle_id"
            value="<?= (int) $raffle->id ?>"
        >


        <div class="card">

            <h2>🎟️ Selecciona tus boletos</h2>


            <div class="legend">

                <span class="available">
                    Disponibles
                </span>

                <span class="selected">
                    Seleccionados
                </span>

                <span class="taken">
                    Ocupados
                </span>

            </div>


            <div class="ticket-grid">

                <?php foreach ($tickets as $ticket): ?>

                    <?php
                    $available =
                        $ticket->payment_status === 'available';
                    ?>

                    <?php if ($available): ?>

                        <label class="ticket-label">

                            <input
                                type="checkbox"
                                name="tickets[]"
                                value="<?= (int) $ticket->id ?>"
                            >

                            <div class="ticket">

                                <?= str_pad(
                                    (string) $ticket->ticket_number,
                                    3,
                                    '0',
                                    STR_PAD_LEFT
                                ) ?>

                            </div>

                        </label>

                    <?php else: ?>

                        <div class="ticket used">

                            <?= str_pad(
                                (string) $ticket->ticket_number,
                                3,
                                '0',
                                STR_PAD_LEFT
                            ) ?>

                        </div>

                    <?php endif; ?>

                <?php endforeach; ?>

            </div>

        </div>


        <div class="card">

            <h2>📝 Datos del comprador</h2>


            <div class="form-grid">

                <div>

                    <label for="customer_name">
                        Nombre
                    </label>

                    <input
                        id="customer_name"
                        type="text"
                        name="customer_name"
                        required
                        maxlength="150"
                    >

                </div>


                <div>

                    <label for="phone">
                        WhatsApp
                    </label>

                    <input
                        id="phone"
                        type="text"
                        name="phone"
                        required
                        maxlength="30"
                    >

                </div>

            </div>


            <button
                type="submit"
                class="submit"
            >
                🎟️ Reservar boletos
            </button>

        </div>

    </form>

</div>

</body>
</html>
