<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1>
            👥 Clientes GRL RIFAS
        </h1>

        <a href="/admin" class="btn btn-secondary">
            Volver
        </a>

    </div>


    <div class="card shadow">

        <div class="card-body">

            <table class="table table-striped">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                    </tr>
                </thead>


                <tbody>

                <?php if(empty($users)): ?>
                    <tr>
                        <td colspan="4" class="text-center">
                            No hay clientes registrados.
                        </td>
                    </tr>

                <?php else: ?>

                 <?php foreach($users as $customer): ?>
                    <tr>

                        <td>
                            <?= $customer->id ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($customer->name) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($customer->email) ?>
                        </td>

                        <td>
                            <?= $customer->role ?>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>
