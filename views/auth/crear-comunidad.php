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
                                class="bi bi-building-add
                                login-icono"
                            ></i>

                            <h1 class="modulo-title h2">
                                Crear comunidad
                            </h1>

                            <p class="text-muted">
                                Hola,
                                <strong>
                                    <?= e(
                                        $usuarioPendiente[
                                            'nombre'
                                        ]
                                    ) ?>
                                </strong>.
                                Registra tu barrio o asociación.
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
                                . '&action=guardarComunidad'
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
                                    Nombre de la comunidad
                                </label>

                                <input
                                    type="text"
                                    id="nombre"
                                    name="nombre"
                                    class="form-control"
                                    value="<?= e(
                                        $datos['nombre'] ?? ''
                                    ) ?>"
                                    placeholder="Ej: Barrio Los Ángeles"
                                    maxlength="120"
                                    required
                                    autofocus
                                >
                            </div>

                            <div class="mb-4">
                                <label
                                    for="descripcion"
                                    class="form-label"
                                >
                                    Descripción
                                    <span class="text-muted">
                                        (opcional)
                                    </span>
                                </label>

                                <textarea
                                    id="descripcion"
                                    name="descripcion"
                                    class="form-control"
                                    rows="4"
                                    placeholder="Describe brevemente la comunidad"
                                ><?= e(
                                    $datos['descripcion'] ?? ''
                                ) ?></textarea>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success w-100"
                            >
                                <i
                                    class="bi bi-building-add"
                                ></i>
                                Crear comunidad
                            </button>

                            <a
                                href="<?= e(url(
                                    'index.php?controller=auth'
                                    . '&action=seleccionarComunidad'
                                )) ?>"
                                class="btn btn-link
                                login-link w-100 mt-2"
                            >
                                <i class="bi bi-arrow-left"></i>
                                Volver a comunidades
                            </a>
                        </form>

                    </div>
                </div>
            </div>

        </div>
    </section>
</main>