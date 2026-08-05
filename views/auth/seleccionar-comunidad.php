<main class="main-content">
    <section class="login-seccion">
        <div class="container">

            <div
                class="row justify-content-center
                align-items-center min-vh-100 py-5"
            >
                <div
                    class="col-12 col-md-9 col-lg-6"
                >
                    <div class="modulo-card shadow-sm">

                        <div class="text-center mb-4">

                            <i
                                class="bi bi-buildings
                                login-icono"
                            ></i>

                            <h1 class="modulo-title h2">
                                Selecciona una comunidad
                            </h1>

                            <p class="text-muted mb-0">
                                Hola,
                                <?= e(
                                    $usuarioPendiente['nombre']
                                ) ?>.

                                Elige la comunidad en la que
                                vas a trabajar.
                            </p>

                        </div>

                        <?php if ($error !== null): ?>

                            <div
                                class="alert alert-danger"
                                role="alert"
                            >
                                <?= e($error) ?>
                            </div>

                        <?php endif; ?>

                        <form
                            action="<?= e(url(
                                'index.php?controller=auth'
                                . '&action=confirmarComunidad'
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

                            <div
                                class="comunidades-opciones
                                mb-4"
                            >
                                <?php foreach (
                                    $membresias
                                    as $indice => $membresia
                                ): ?>

                                    <label
                                        class="comunidad-opcion"
                                    >
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="id_comunidad"
                                            value="<?= e(
                                                $membresia[
                                                    'id_comunidad'
                                                ]
                                            ) ?>"
                                            <?= $indice === 0
                                                ? 'checked'
                                                : '' ?>
                                            required
                                        >

                                        <span>
                                            <strong>
                                                <?= e(
                                                    $membresia[
                                                        'comunidad'
                                                    ]
                                                ) ?>
                                            </strong>

                                            <small>
                                                <?= e(
                                                    $membresia['rol']
                                                ) ?>
                                            </small>
                                        </span>
                                    </label>

                                <?php endforeach; ?>
                            </div>

                            <button
                                type="submit"
                                class="btn btn-success
                                btn-lg w-100"
                            >
                                Continuar
                                <i class="bi bi-arrow-right"></i>
                            </button>

                            <a
                                href="<?= e(url(
                                    'index.php?controller=auth'
                                    . '&action=login'
                                )) ?>"
                                class="btn btn-link
                                login-link w-100 mt-2"
                            >
                                Usar otra cuenta
                            </a>

                        </form>

                    </div>
                </div>
            </div>

        </div>
    </section>
</main>