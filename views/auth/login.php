<main class="main-content">
    <section class="login-seccion">
        <div class="container">

            <div
                class="row justify-content-center
                align-items-center min-vh-100 py-5"
            >
                <div class="col-12 col-lg-10">

                    <div class="login-tarjeta shadow">
                        <div class="row g-0">

                            <div
                                class="col-12 col-lg-6
                                login-info"
                            >
                                <div>
                                    <i
                                        class="bi bi-diagram-3-fill
                                        login-icono"
                                    ></i>

                                    <h1>
                                        Bienvenido a ComuniGest
                                    </h1>

                                    <p>
                                        Accede para administrar
                                        proyectos, actividades y
                                        recursos de tu comunidad.
                                    </p>

                                    <div class="login-beneficio">
                                        <i
                                            class="bi
                                            bi-check-circle"
                                        ></i>
                                        <span>
                                            Control de proyectos
                                            activos
                                        </span>
                                    </div>

                                    <div class="login-beneficio">
                                        <i
                                            class="bi
                                            bi-check-circle"
                                        ></i>
                                        <span>
                                            Seguimiento de
                                            actividades
                                        </span>
                                    </div>

                                    <div class="login-beneficio">
                                        <i
                                            class="bi
                                            bi-check-circle"
                                        ></i>
                                        <span>
                                            Acceso según comunidad
                                            y rol
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="col-12 col-lg-6
                                login-formulario"
                            >
                                <div
                                    class="login-formulario-contenido"
                                >
                                    <a
                                        href="<?= e(url(
                                            'index.html'
                                        )) ?>"
                                        class="login-link
                                        d-inline-flex
                                        align-items-center
                                        gap-2 mb-4"
                                    >
                                        <i
                                            class="bi
                                            bi-arrow-left"
                                        ></i>
                                        Volver al inicio
                                    </a>

                                    <h2>Iniciar sesión</h2>

                                    <p class="text-muted mb-4">
                                        Ingresa los datos de tu
                                        cuenta.
                                    </p>

                                    <?php if (
                                        $mensaje !== null
                                    ): ?>

                                        <div
                                            class="alert
                                            alert-success"
                                            role="status"
                                        >
                                            <?= e($mensaje) ?>
                                        </div>

                                    <?php endif; ?>

                                    <?php if (
                                        isset(
                                            $errores['general']
                                        )
                                    ): ?>

                                        <div
                                            class="alert
                                            alert-danger"
                                            role="alert"
                                        >
                                            <?= e(
                                                $errores['general']
                                            ) ?>
                                        </div>

                                    <?php endif; ?>

                                    <form
                                        action="<?= e(url(
                                            'index.php'
                                            . '?controller=auth'
                                            . '&action=autenticar'
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
                                                for="correo"
                                                class="form-label"
                                            >
                                                Correo electrónico
                                            </label>

                                            <div
                                                class="input-group"
                                            >
                                                <span
                                                    class="input-group-text"
                                                >
                                                    <i
                                                        class="bi
                                                        bi-envelope"
                                                    ></i>
                                                </span>

                                                <input
                                                    type="email"
                                                    id="correo"
                                                    name="correo"
                                                    class="form-control<?= isset(
                                                        $errores['correo']
                                                    )
                                                        ? ' is-invalid'
                                                        : '' ?>"
                                                    value="<?= e(
                                                        $correo
                                                    ) ?>"
                                                    placeholder="correo@ejemplo.com"
                                                    maxlength="190"
                                                    autocomplete="email"
                                                    required
                                                    autofocus
                                                >
                                            </div>

                                            <?php if (
                                                isset(
                                                    $errores['correo']
                                                )
                                            ): ?>

                                                <div
                                                    class="invalid-feedback
                                                    d-block"
                                                >
                                                    <?= e(
                                                        $errores['correo']
                                                    ) ?>
                                                </div>

                                            <?php endif; ?>

                                        </div>

                                        <div class="mb-4">

                                            <label
                                                for="contrasena"
                                                class="form-label"
                                            >
                                                Contraseña
                                            </label>

                                            <div
                                                class="input-group"
                                            >
                                                <span
                                                    class="input-group-text"
                                                >
                                                    <i
                                                        class="bi
                                                        bi-lock"
                                                    ></i>
                                                </span>

                                                <input
                                                    type="password"
                                                    id="contrasena"
                                                    name="contrasena"
                                                    class="form-control<?= isset(
                                                        $errores[
                                                            'contrasena'
                                                        ]
                                                    )
                                                        ? ' is-invalid'
                                                        : '' ?>"
                                                    placeholder="Ingresa tu contraseña"
                                                    maxlength="255"
                                                    autocomplete="current-password"
                                                    required
                                                >
                                            </div>

                                            <?php if (
                                                isset(
                                                    $errores[
                                                        'contrasena'
                                                    ]
                                                )
                                            ): ?>

                                                <div
                                                    class="invalid-feedback
                                                    d-block"
                                                >
                                                    <?= e(
                                                        $errores[
                                                            'contrasena'
                                                        ]
                                                    ) ?>
                                                </div>

                                            <?php endif; ?>

                                        </div>

                                        <button
                                            type="submit"
                                            class="btn btn-success
                                            w-100 btn-lg"
                                        >
                                            <i
                                                class="bi
                                                bi-box-arrow-in-right"
                                            ></i>
                                            Ingresar
                                        </button>
                                        <div class="text-center mt-3">
                                            <span class="text-muted">
                                                ¿No tienes una cuenta?
                                            </span>

                                            <a
                                                href="<?= e(url(
                                                    'index.php?controller=auth'
                                                    . '&action=registro'
                                                )) ?>"
                                                class="login-link"
                                            >
                                                Crear cuenta
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>
</main>