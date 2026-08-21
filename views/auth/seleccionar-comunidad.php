<?php

$membresiasPorComunidad = [];

foreach ($membresias as $membresia) {
    $id = (int) $membresia['id_comunidad'];

    $membresiasPorComunidad[$id] =
        $membresia;
}

?>

<main class="main-content">
    <section class="py-5">
        <div class="container">

            <div class="text-center mb-4">
                <i
                    class="bi bi-buildings
                    login-icono"
                ></i>

                <h1 class="modulo-title">
                    Comunidades
                </h1>

                <p class="text-muted">
                    Hola,
                    <strong>
                        <?= e(
                            $usuarioPendiente['nombre']
                        ) ?>
                    </strong>.
                    Selecciona una comunidad o únete
                    a una nueva.
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

            <div class="text-center mb-4">
                <a
                    href="<?= e(url(
                        'index.php?controller=auth'
                        . '&action=crearComunidad'
                    )) ?>"
                    class="btn btn-success"
                >
                    <i class="bi bi-building-add"></i>
                    Crear comunidad
                </a>
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-12 col-md-8 col-lg-6">

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>

                        <input
                            type="search"
                            id="buscarComunidad"
                            class="form-control"
                            placeholder="Buscar comunidad..."
                        >
                    </div>

                </div>
            </div>

            <?php if ($comunidades === []): ?>

                <div class="alert alert-info text-center">
                    No hay comunidades disponibles.
                </div>

            <?php else: ?>

                <div
                    id="listaComunidades"
                    class="row g-4"
                >
                    <?php foreach (
                        $comunidades as $comunidad
                    ): ?>

                        <?php

                        $idComunidad =
                            (int) $comunidad[
                                'id_comunidad'
                            ];

                        $descripcion = trim(
                            (string) (
                                $comunidad['descripcion']
                                ?? ''
                            )
                        );

                        $cantidadMiembros =
                            (int) $comunidad[
                                'cantidad_miembros'
                            ];

                        $membresiaActual =
                            $membresiasPorComunidad[
                                $idComunidad
                            ] ?? null;

                        ?>

                        <div
                            class="col-12 col-md-6
                            col-lg-4 comunidad-item"
                            data-busqueda="<?= e(
                                $comunidad['nombre']
                                . ' '
                                . $descripcion
                            ) ?>"
                        >
                            <div
                                class="card h-100
                                shadow-sm"
                            >
                                <div class="card-body d-flex flex-column">

                                    <div class="mb-3">
                                        <i
                                            class="bi bi-people-fill
                                            fs-2 text-success"
                                        ></i>
                                    </div>

                                    <h2 class="h5">
                                        <?= e(
                                            $comunidad['nombre']
                                        ) ?>
                                    </h2>

                                    <p
                                        class="text-muted
                                        flex-grow-1"
                                    >
                                        <?= $descripcion !== ''
                                            ? e($descripcion)
                                            : 'Sin descripción.' ?>
                                    </p>

                                    <p class="small text-muted">
                                        <i
                                            class="bi bi-person"
                                        ></i>

                                        <?= e(
                                            $cantidadMiembros
                                        ) ?>

                                        <?= $cantidadMiembros === 1
                                            ? 'miembro'
                                            : 'miembros' ?>
                                    </p>

                                    <?php if (
                                        $membresiaActual !== null
                                    ): ?>

                                        <span
                                            class="badge
                                            text-bg-primary
                                            align-self-start mb-3"
                                        >
                                            <?= e(
                                                $membresiaActual['rol']
                                            ) ?>
                                        </span>

                                        <form
                                            action="<?= e(url(
                                                'index.php'
                                                . '?controller=auth'
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

                                            <input
                                                type="hidden"
                                                name="id_comunidad"
                                                value="<?= e(
                                                    $idComunidad
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn
                                                btn-primary w-100"
                                            >
                                                Entrar
                                            </button>
                                        </form>

                                    <?php else: ?>

                                        <form
                                            action="<?= e(url(
                                                'index.php'
                                                . '?controller=auth'
                                                . '&action=unirseComunidad'
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

                                            <input
                                                type="hidden"
                                                name="id_comunidad"
                                                value="<?= e(
                                                    $idComunidad
                                                ) ?>"
                                            >

                                            <button
                                                type="submit"
                                                class="btn
                                                btn-success w-100"
                                            >
                                                <i
                                                    class="bi
                                                    bi-person-plus"
                                                ></i>
                                                Unirse
                                            </button>
                                        </form>

                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>

                <div
                    id="sinResultados"
                    class="alert alert-warning
                    text-center mt-4 d-none"
                >
                    No se encontraron comunidades.
                </div>

            <?php endif; ?>

            <div class="text-center mt-4">
                <a
                    href="<?= e(url(
                        'index.php?controller=auth'
                        . '&action=login'
                    )) ?>"
                    class="btn btn-link login-link"
                >
                    <i class="bi bi-arrow-left"></i>
                    Usar otra cuenta
                </a>
            </div>

        </div>
    </section>
</main>