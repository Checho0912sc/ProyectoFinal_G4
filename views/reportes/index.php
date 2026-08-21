<main class="main-content">

    <section class="dashboard-section py-5">

        <div class="container">

            <!-- ENCABEZADO -->

            <div class="row align-items-center mb-4">

                <div class="col-12">

                    <span class="badge text-bg-success mb-3">
                        Reportes
                    </span>

                    <h1 class="dashboard-title">
                        Reportes
                    </h1>

                    <p class="dashboard-text">
                        Información general de
                        <?= e(
                            $usuarioActual['comunidad']
                        ) ?>.
                    </p>

                </div>

            </div>


            <!-- INDICADORES -->

            <div class="row g-4 mb-5">

                <div class="col-12 col-md-6 col-xl-3">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-folder-check"></i>
                        </div>

                        <div>

                            <h3>
                                <?= e(
                                    $indicadores['proyectos']
                                ) ?>
                            </h3>

                            <h6>Proyectos</h6>

                        </div>

                    </div>

                </div>


                <div class="col-12 col-md-6 col-xl-3">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-calendar-event"></i>
                        </div>

                        <div>

                            <h3>
                                <?= e(
                                    $indicadores['actividades']
                                ) ?>
                            </h3>

                            <h6>Actividades</h6>

                        </div>

                    </div>

                </div>


                <div class="col-12 col-md-6 col-xl-3">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-cash-coin"></i>
                        </div>

                        <div>

                            <h3>
                                <?= e(
                                    colones(
                                        $indicadores['ingresos']
                                    )
                                ) ?>
                            </h3>

                            <h6>Ingresos</h6>

                        </div>

                    </div>

                </div>


                <div class="col-12 col-md-6 col-xl-3">

                    <div class="dashboard-resumen-card h-100">

                        <div class="dashboard-resumen-icono">
                            <i class="bi bi-people"></i>
                        </div>

                        <div>

                            <h3>
                                <?= e(
                                    $indicadores['usuarios']
                                ) ?>
                            </h3>

                            <h6>Usuarios</h6>

                        </div>

                    </div>

                </div>

            </div>


            <!-- REPORTE DE PROYECTOS -->

            <div class="dashboard-card mb-5">

                <div class="mb-4">

                    <h4>
                        Reporte de proyectos
                    </h4>

                    <p class="text-muted mb-0">
                        Estado y avance de los proyectos
                        comunitarios.
                    </p>

                </div>

                <div class="table-responsive">

                    <table
                        class="table align-middle
                        dashboard-table"
                    >

                        <thead>

                            <tr>

                                <th>Proyecto</th>
                                <th>Responsable</th>
                                <th>Estado</th>
                                <th>Presupuesto</th>
                                <th>Avance</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($proyectos === []): ?>

                            <tr>

                                <td
                                    colspan="5"
                                    class="text-center
                                    text-muted py-4"
                                >
                                    No hay proyectos registrados.
                                </td>

                            </tr>

                        <?php endif; ?>


                        <?php foreach (
                            $proyectos
                            as $proyecto
                        ): ?>

                            <?php

                            $avance = max(
                                0,
                                min(
                                    100,
                                    (int) $proyecto['avance']
                                )
                            );

                            $claseEstado = match (
                                $proyecto['estado']
                            ) {
                                'En proceso' =>
                                    'text-bg-success',

                                'Pausado' =>
                                    'text-bg-warning',

                                'Finalizado' =>
                                    'text-bg-primary',

                                'Cancelado' =>
                                    'text-bg-danger',

                                default =>
                                    'text-bg-secondary',
                            };

                            ?>

                            <tr>

                                <td>
                                    <?= e(
                                        $proyecto['nombre']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $proyecto['responsable']
                                    ) ?>
                                </td>

                                <td>

                                    <span
                                        class="badge
                                        <?= e(
                                            $claseEstado
                                        ) ?>"
                                    >
                                        <?= e(
                                            $proyecto['estado']
                                        ) ?>
                                    </span>

                                </td>

                                <td>
                                    <?= e(
                                        colones(
                                            $proyecto['presupuesto']
                                        )
                                    ) ?>
                                </td>

                                <td>

                                    <div
                                        class="d-flex
                                        align-items-center
                                        gap-2"
                                    >

                                        <div
                                            class="progress
                                            flex-grow-1"
                                            role="progressbar"
                                            aria-valuenow="<?= e(
                                                $avance
                                            ) ?>"
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                        >

                                            <div
                                                class="progress-bar
                                                bg-success"
                                                style="width:
                                                <?= e(
                                                    $avance
                                                ) ?>%"
                                            ></div>

                                        </div>

                                        <span>
                                            <?= e(
                                                $avance
                                            ) ?>%
                                        </span>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- REPORTE FINANCIERO -->

            <div class="dashboard-card mb-5">

                <div class="mb-4">

                    <h4>
                        Reporte financiero
                    </h4>

                    <p class="text-muted mb-0">
                        Ingresos y egresos registrados.
                    </p>

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
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Monto</th>
                                <th>Estado</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($financiero === []): ?>

                            <tr>

                                <td
                                    colspan="7"
                                    class="text-center
                                    text-muted py-4"
                                >
                                    No hay movimientos registrados.
                                </td>

                            </tr>

                        <?php endif; ?>


                        <?php foreach (
                            $financiero
                            as $movimiento
                        ): ?>

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
                                        <?= $movimiento['tipo']
                                            === 'Ingreso'
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
                                        <?= $movimiento['estado']
                                            === 'Registrado'
                                            ? 'text-bg-success'
                                            : 'text-bg-secondary' ?>"
                                    >
                                        <?= e(
                                            $movimiento['estado']
                                        ) ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- REPORTE DE ACTIVIDADES -->

            <div class="dashboard-card">

                <div class="mb-4">

                    <h4>
                        Reporte de actividades
                    </h4>

                    <p class="text-muted mb-0">
                        Actividades programadas y realizadas
                        de la comunidad.
                    </p>

                </div>

                <div class="table-responsive">

                    <table
                        class="table align-middle
                        dashboard-table"
                    >

                        <thead>

                            <tr>

                                <th>Actividad</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Lugar</th>
                                <th>Proyecto</th>
                                <th>Responsable</th>
                                <th>Estado</th>

                            </tr>

                        </thead>

                        <tbody>

                        <?php if ($actividades === []): ?>

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center
                                    text-muted py-4"
                                >
                                    No hay actividades registradas.
                                </td>

                            </tr>

                        <?php endif; ?>


                        <?php foreach (
                            $actividades
                            as $actividad
                        ): ?>

                            <?php

                            $claseEstado = match (
                                $actividad['estado']
                            ) {
                                'Programada' =>
                                    'text-bg-success',

                                'Realizada' =>
                                    'text-bg-primary',

                                'Cancelada' =>
                                    'text-bg-danger',

                                default =>
                                    'text-bg-secondary',
                            };

                            ?>

                            <tr>

                                <td>
                                    <?= e(
                                        $actividad['titulo']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $actividad['tipo']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        fechaCorta(
                                            $actividad['fecha']
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        horaCorta(
                                            $actividad['hora']
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $actividad['lugar']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $actividad['proyecto']
                                        ?? 'General'
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        $actividad['responsable']
                                    ) ?>
                                </td>

                                <td>

                                    <span
                                        class="badge
                                        <?= e(
                                            $claseEstado
                                        ) ?>"
                                    >
                                        <?= e(
                                            $actividad['estado']
                                        ) ?>
                                    </span>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </section>

</main>