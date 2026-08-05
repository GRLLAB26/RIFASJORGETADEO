<h1>
    🎟️ Panel Administrador GRL RIFAS
</h1>

<hr>

<!-- 📊 KPIs DEL SISTEMA -->
<div class="row g-4 mb-4">

    <div class="col-md-3">
        <div class="card shadow-sm p-3">
            <h5>🎟️ Rifas Activas</h5>
            <h2><?= $kpis['active_raffles'] ?? 0 ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm p-3">
            <h5>💰 Ingresos</h5>
            <h2>
                $<?= number_format($kpis['revenue'] ?? 0, 2) ?>
            </h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm p-3">
            <h5>✅ Boletos Vendidos</h5>
            <h2><?= $kpis['sold_tickets'] ?? 0 ?></h2>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm p-3">
            <h5>⏳ Reservados</h5>
            <h2><?= $kpis['reserved_tickets'] ?? 0 ?></h2>
        </div>
    </div>

</div>

<div class="row g-4">
