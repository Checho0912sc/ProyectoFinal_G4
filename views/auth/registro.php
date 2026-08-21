<main class="main-content">
    <section class="login-seccion">
        <div class="container">

            <div
                class="row justify-content-center
                align-items-center min-vh-100 py-5"
            >
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="modulo-card shadow-sm">

                        <div class="text-center mb-4">
                            <i
                                class="bi bi-person-plus
                                login-icono"
                            ></i>

                            <h1 class="modulo-title h2">
                                Crear cuenta
                            </h1>

                            <p class="text-muted">
                                Regístrate para crear o unirte
                                a una comunidad.
                            </p>
                        </div>

                        <?php if (
                            isset($errores['general'])
                        ): ?>

                            <div
                                class="alert alert-danger"
                                role="alert"
                            >
                                <?= e(
                                    $errores['general']
                                ) ?>
                            </div>

                        <?php endif; ?>

                        <form
                            action="<?= e(url(
                                'index.php?controller=auth'
                                . '&action=registrar'
                            )) ?>"
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

                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    class="form-control"
                                    value="<?= e(
                                        $datos['nombre'] ?? ''
                                    ) ?>"
                                    maxlength="120"
                                    required
                                >

                                <?php if (
                                    isset($errores['nombre'])
                                ): ?>

                                    <div
                                        class="text-danger
                                        small mt-1"
                                    >
                                        <?= e(
                                            $errores['nombre']
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="correo"
                                    class="form-label"
                                >
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    id="correo"
                                    name="correo"
                                    class="form-control"
                                    value="<?= e(
                                        $datos['correo'] ?? ''
                                    ) ?>"
                                    maxlength="190"
                                    required
                                >

                                <?php if (
                                    isset($errores['correo'])
                                ): ?>

                                    <div
                                        class="text-danger
                                        small mt-1"
                                    >
                                        <?= e(
                                            $errores['correo']
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="telefono"
                                    class="form-label"
                                >
                                    Teléfono
                                    <span class="text-muted">
                                        (opcional)
                                    </span>
                                </label>

                                <input
                                    type="text"
                                    id="telefono"
                                    name="telefono"
                                    class="form-control"
                                    value="<?= e(
                                        $datos['telefono'] ?? ''
                                    ) ?>"
                                    maxlength="20"
                                    placeholder="8888-8888"
                                >

                                <?php if (
                                    isset($errores['telefono'])
                                ): ?>

                                    <div
                                        class="text-danger
                                        small mt-1"
                                    >
                                        <?= e(
                                            $errores['telefono']
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label
                                    for="contrasena"
                                    class="form-label"
                                >
                                    Contraseña
                                </label>

                                <input
                                    type="password"
                                    id="contrasena"
                                    name="contrasena"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                                <?php if (
                                    isset(
                                        $errores['contrasena']
                                    )
                                ): ?>

                                    <div
                                        class="text-danger
                                        small mt-1"
                                    >
                                        <?= e(
                                            $errores['contrasena']
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <div class="mb-4">
                                <label
                                    for="confirmar_contrasena"
                                    class="form-label"
                                >
                                    Confirmar contraseña
                                </label>

                                <input
                                    type="password"
                                    id="confirmar_contrasena"
                                    name="confirmar_contrasena"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                                <?php if (
                                    isset(
                                        $errores[
                                            'confirmar_contrasena'
                                        ]
                                    )
                                ): ?>

                                    <div
                                        class="text-danger
                                        small mt-1"
                                    >
                                        <?= e(
                                            $errores[
                                                'confirmar_contrasena'
                                            ]
                                        ) ?>
                                    </div>

                                <?php endif; ?>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success
                                w-100"
                            >
                                <i class="bi bi-person-plus"></i>
                                Crear cuenta
                            </button>

                            <a
                                href="<?= e(url(
                                    'index.php?controller=auth'
                                    . '&action=login'
                                )) ?>"
                                class="btn btn-link
                                login-link w-100 mt-2"
                            >
                                Ya tengo una cuenta
                            </a>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </section>
</main>