<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card shadow">

                <div class="card-body">

                    <h2 class="text-center mb-4">
                        🎟️ GRL RIFAS
                    </h2>

                    <h5 class="text-center mb-4">
                        Iniciar sesión
                    </h5>


                    <form method="POST" action="/login">

                        <div class="mb-3">
                            <label class="form-label">
                                Correo
                            </label>

                            <input 
                                type="email"
                                name="email"
                                class="form-control"
                                required>
                        </div>


                        <div class="mb-3">

                            <label class="form-label">
                                Contraseña
                            </label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>


                        <button class="btn btn-success w-100">
                            Entrar
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
