<?php

$puedeModificar = Auth::tieneRol(
    'Administrador',
    'Coordinador'
);

?>

<main class="main-content">

    <section class="dashboard-section py-5">

        <div class="container">

            <div class="row align-items-center mb-4">

                <div class="col-12 col-lg-8">

                    <span class="badge text-bg-success mb-3">
                        Finanzas
                    </span>

                    <h1 class="dashboard-title">
                        Finanzas
                    </h1>

                    <p class="dashboard-text">
                        Control de ingresos y egresos de
                        <?= e($usuarioActual['comunidad']) ?>.
                    </p>

                </div>

                <?php if ($puedeModificar): ?>

                    <div class="col-12 col-lg-4 text-lg-end">

                        <button
                            type="button"
                            class="btn btn-success btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#modalMovimiento"
                        >
                            <i class="bi bi-plus-circle"></i>
                            Registrar movimiento
                        </button>

                    </div>

                <?php endif; ?>

            </div>

            <?php if ($mensajeExito !== null): ?>

                <div
                    class="alert alert-success alert-dismissible fade show"
                    role="alert"
                >
                    <?= e($mensajeExito) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>

            <?php endif; ?>

            <?php if ($mensajeError !== null): ?>

                <div
                    class="alert alert-danger alert-dismissible fade show"
                    role="alert"
                >
                    <?= e($mensajeError) ?>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                    ></button>
                </div>

            <?php endif; ?>

            <!-- RESUMEN -->

            <div class="row g-4 mb-5">

                <div class="col-12 col-md-4">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>

                        <div>
                            <h3>
                                <?= e(
                                    colones(
                                        $resumen['ingresos']
                                    )
                                ) ?>
                            </h3>

                            <h6>Ingresos</h6>

                            <p>
                                Total de ingresos registrados
                            </p>
                        </div>

                    </div>

                </div>

                <div class="col-12 col-md-4">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-arrow-up-circle"></i>
                        </div>

                        <div>
                            <h3>
                                <?= e(
                                    colones(
                                        $resumen['egresos']
                                    )
                                ) ?>
                            </h3>

                            <h6>Egresos</h6>

                            <p>
                                Total de egresos registrados
                            </p>
                        </div>

                    </div>

                </div>

                <div class="col-12 col-md-4">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <div>
                            <h3>
                                <?= e(
                                    colones(
                                        $resumen['saldo']
                                    )
                                ) ?>
                            </h3>

                            <h6>Saldo</h6>

                            <p>
                                Ingresos menos egresos
                            </p>
                        </div>

                    </div>

                </div>

            </div>

            <!-- MOVIMIENTOS -->

            <div class="dashboard-card">

                <div
                    class="d-flex justify-content-between
                    align-items-center mb-4"
                >

                    <div>

                        <h4>
                            Movimientos financieros
                        </h4>

                        <p class="text-muted mb-0">
                            Ingresos y egresos de la comunidad.
                        </p>

                    </div>

                </div>

                <div class="table-responsive">

                    <table
                        class="table align-middle
                        dashboard-table"
                    >

                        <thead>

                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Proyecto</th>
                                <th>Registrado por</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Estado</th>

                                <?php if ($puedeModificar): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($movimientos === []): ?>

                            <tr>

                                <td
                                    colspan="<?= $puedeModificar ? 8 : 7 ?>"
                                    class="text-center text-muted py-4"
                                >
                                    No hay movimientos registrados.
                                </td>

                            </tr>

                        <?php endif; ?>

                        <?php foreach ($movimientos as $movimiento): ?>

                            <tr>

                                <td>
                                    <?= e(
                                        fechaCorta(
                                            $movimiento['fecha']
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $movimiento['descripcion']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $movimiento['proyecto']
                                        ?? 'General'
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $movimiento['usuario']
                                    ) ?>
                                </td>

                                <td>

                                    <span
                                        class="badge
                                        <?= $movimiento['tipo'] === 'Ingreso'
                                            ? 'text-bg-success'
                                            : 'text-bg-danger' ?>"
                                    >
                                        <?= e(
                                            $movimiento['tipo']
                                        ) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= e(
                                        colones(
                                            $movimiento['monto']
                                        )
                                    ) ?>
                                </td>

                                <td>

                                    <span
                                        class="badge
                                        <?= $movimiento['estado'] === 'Registrado'
                                            ? 'text-bg-success'
                                            : 'text-bg-secondary' ?>"
                                    >
                                        <?= e(
                                            $movimiento['estado']
                                        ) ?>
                                    </span>

                                </td>

                                <?php if ($puedeModificar): ?>

                                    <td>

                                        <?php if (
                                            $movimiento['estado']
                                            === 'Registrado'
                                        ): ?>

                                            <form
                                                method="POST"
                                                action="<?= e(
                                                    url(
                                                        'index.php?controller=finanzas&action=anular'
                                                    )
                                                ) ?>"
                                                onsubmit="return confirm(
                                                    '¿Está seguro de anular este movimiento?'
                                                );"
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
                                                    name="id_movimiento"
                                                    value="<?= e(
                                                        $movimiento[
                                                            'id_movimiento'
                                                        ]
                                                    ) ?>"
                                                >

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                >
                                                    <i class="bi bi-x-circle"></i>
                                                    Anular
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                —
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                <?php endif; ?>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</main>


<?php if ($puedeModificar): ?>

<!-- MODAL PARA REGISTRAR -->

<div
    class="modal fade"
    id="modalMovimiento"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <form
                method="POST"
                action="<?= e(
                    url(
                        'index.php?controller=finanzas&action=guardar'
                    )
                ) ?>"
            >

                <div class="modal-header">

                    <h5 class="modal-title">
                        Registrar movimiento financiero
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                    ></button>

                </div>

                <div class="modal-body">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(Auth::csrfToken()) ?>"
                    >

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label
                                for="fecha"
                                class="form-label"
                            >
                                Fecha
                            </label>

                            <input
                                type="date"
                                class="form-control"
                                id="fecha"
                                name="fecha"
                                value="<?= e(
                                    date('Y-m-d')
                                ) ?>"
                                required
                            >

                        </div>

                        <div class="col-md-6">

                            <label
                                for="tipo"
                                class="form-label"
                            >
                                Tipo
                            </label>

                            <select
                                class="form-select"
                                id="tipo"
                                name="tipo"
                                required
                            >

                                <option value="Ingreso">
                                    Ingreso
                                </option>

                                <option value="Egreso">
                                    Egreso
                                </option>

                            </select>

                        </div>

                        <div class="col-12">

                            <label
                                for="descripcion"
                                class="form-label"
                            >
                                Descripción
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="descripcion"
                                name="descripcion"
                                maxlength="255"
                                required
                            >

                        </div>

                        <div class="col-md-6">

                            <label
                                for="monto"
                                class="form-label"
                            >
                                Monto
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    ₡
                                </span>

                                <input
                                    type="number"
                                    class="form-control"
                                    id="monto"
                                    name="monto"
                                    min="0.01"
                                    step="0.01"
                                    required
                                >

                            </div>

                        </div>

                        <div class="col-md-6">

                            <label
                                for="id_proyecto"
                                class="form-label"
                            >
                                Proyecto
                            </label>

                            <select
                                class="form-select"
                                id="id_proyecto"
                                name="id_proyecto"
                            >

                                <option value="">
                                    Movimiento general
                                </option>

                                <?php foreach (
                                    $proyectos
                                    as $proyecto
                                ): ?>

                                    <option
                                        value="<?= e(
                                            $proyecto['id_proyecto']
                                        ) ?>"
                                    >
                                        <?= e(
                                            $proyecto['nombre']
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-success"
                    >
                        <i class="bi bi-save"></i>
                        Guardar movimiento
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<?php endif; ?>