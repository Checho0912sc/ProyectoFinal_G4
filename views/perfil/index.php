<main class="main-content">

    <section class="modulo-section py-5">

        <div class="container">

            <?php if (!empty($mensaje)): ?>

                <div
                    class="alert alert-success"
                    role="alert"
                >
                    <i class="bi bi-check-circle me-2"></i>
                    <?= e($mensaje) ?>
                </div>

            <?php endif; ?>

            <?php if (!empty($error)): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= e($error) ?>
                </div>

            <?php endif; ?>


            <div class="row justify-content-center">

                <div class="col-12 col-lg-8">

                    <span
                        class="badge text-bg-success mb-3"
                    >
                        Mi cuenta
                    </span>

                    <h1 class="modulo-title">
                        Mi perfil
                    </h1>

                    <p class="modulo-text mb-4">
                        Actualiza los datos personales de tu cuenta.
                    </p>


                    <div class="modulo-card">

                        <form
                            action="<?= e(
                                url(
                                    'index.php?controller=perfil'
                                    . '&action=guardar'
                                )
                            ) ?>"
                            method="post"
                        >

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(
                                    Auth::csrfToken()
                                ) ?>"
                            >


                            <div class="mb-3">

                                <label
                                    for="nombre"
                                    class="form-label"
                                >
                                    Nombre completo
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-person"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="nombre"
                                        name="nombre"
                                        class="form-control"
                                        value="<?= e(
                                            (string) (
                                                $perfil['nombre']
                                                ?? ''
                                            )
                                        ) ?>"
                                        maxlength="120"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="mb-3">

                                <label
                                    for="correo"
                                    class="form-label"
                                >
                                    Correo electrónico
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-envelope"></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="correo"
                                        name="correo"
                                        class="form-control"
                                        value="<?= e(
                                            (string) (
                                                $perfil['correo']
                                                ?? ''
                                            )
                                        ) ?>"
                                        maxlength="191"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="mb-4">

                                <label
                                    for="telefono"
                                    class="form-label"
                                >
                                    Teléfono
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-telephone"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="telefono"
                                        name="telefono"
                                        class="form-control"
                                        value="<?= e(
                                            (string) (
                                                $perfil['telefono']
                                                ?? ''
                                            )
                                        ) ?>"
                                        maxlength="20"
                                    >

                                </div>

                            </div>


                            <hr class="my-4">


                            <h4>
                                Cambiar contraseña
                            </h4>

                            <p class="text-muted">
                                Deja estos espacios vacíos si no deseas cambiarla.
                            </p>


                            <div class="row g-3 mb-4">

                                <div class="col-12 col-md-6">

                                    <label
                                        for="contrasena"
                                        class="form-label"
                                    >
                                        Nueva contraseña
                                    </label>

                                    <input
                                        type="password"
                                        id="contrasena"
                                        name="contrasena"
                                        class="form-control"
                                        minlength="6"
                                        maxlength="255"
                                    >

                                </div>


                                <div class="col-12 col-md-6">

                                    <label
                                        for="confirmarContrasena"
                                        class="form-label"
                                    >
                                        Confirmar contraseña
                                    </label>

                                    <input
                                        type="password"
                                        id="confirmarContrasena"
                                        name="confirmar_contrasena"
                                        class="form-control"
                                        minlength="6"
                                        maxlength="255"
                                    >

                                </div>

                            </div>


                            <button
                                type="submit"
                                class="btn btn-success"
                            >
                                <i class="bi bi-save me-1"></i>
                                Guardar cambios
                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>