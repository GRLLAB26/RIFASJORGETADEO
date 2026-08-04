<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= $title ?? 'GRL RIFAS' ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

<link rel="stylesheet" href="/css/app.css">

</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            🎟️ GRL RIFAS
        </a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Menú lateral -->
        <div class="col-md-2 bg-white border-end min-vh-100 p-3">

            <h5 class="mb-4">Menú</h5>

            <div class="list-group">

                <a href="/dashboard" class="list-group-item list-group-item-action">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>

                <a href="/raffles" class="list-group-item list-group-item-action">
                    <i class="bi bi-ticket-perforated"></i> Rifas
   </a>
<a href="/raffles" class="list-group-item list-group-item-action">
    <i class="bi bi-grid-3x3-gap"></i> Boletos

</a>

<a href="/admin/customers" class="list-group-item list-group-item-action">
    <i class="bi bi-people"></i> Clientes
</a>

                <a href="/payments" class="list-group-item list-group-item-action">
                    <i class="bi bi-cash-stack"></i> Pagos
                </a>

                <a href="/reports" class="list-group-item list-group-item-action">
                    <i class="bi bi-bar-chart"></i> Reportes
                </a>

                <a href="/settings" class="list-group-item list-group-item-action">
                    <i class="bi bi-gear"></i> Configuración
                </a>

            </div>

        </div>

        <!-- Contenido -->
        <div class="col-md-10 p-4">

            <?= $content ?>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
