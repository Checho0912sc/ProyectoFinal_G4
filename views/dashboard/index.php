<?php

$tarjetasResumen = [
    [
        'titulo' => 'Proyectos activos',
        'valor' => $resumen['proyectos_activos'],
        'texto' => 'En planificación o desarrollo',
        'icono' => 'bi-folder-check',
    ],
    [
        'titulo' => 'Actividades próximas',
        'valor' => $resumen['actividades_proximas'],
        'texto' => 'Programadas desde hoy',
        'icono' => 'bi-calendar-event',
    ],
    [
        'titulo' => 'Miembros activos',
        'valor' => $resumen['miembros_activos'],
        'texto' => 'Asignados a la comunidad',
        'icono' => 'bi-people',
    ],
    [
        'titulo' => 'Saldo registrado',
        'valor' => colones($resumen['saldo']),
        'texto' => 'Ingresos menos egresos',
        'icono' => 'bi-cash-coin',
    ],
];

/*
|--------------------------------------------------------------------------
| ACCESOS RÁPIDOS
|--------------------------------------------------------------------------
| Proyectos está disponible para todos los usuarios autenticados.
| Usuarios solamente se agrega si el usuario es Administrador.
*/

$accesos = [
    [
        'nombre' => 'Proyectos',
        'ruta' => 'index.php?controller=proyecto&action=index',
        'icono' => 'bi-folder-check',
    ],
    [
        'nombre' => 'Actividades',
        'ruta' => 'index.php?controller=actividades&action=index',
        'icono' => 'bi-calendar-check',
    ],
    [
        'nombre' => 'Grupos',
        'ruta' => 'index.php?controller=grupos&action=index',
        'icono' => 'bi-person-workspace',
    ],
    [
        'nombre' => 'Finanzas',
        'ruta' => 'index.php?controller=finanzas&action=index',
        'icono' => 'bi-cash-coin',
    ],
    [
        'nombre' => 'Reportes',
        'ruta' => 'index.php?controller=reportes&action=index',
        'icono' => 'bi-bar-chart',
    ],
];

/*
|--------------------------------------------------------------------------
| USUARIOS SOLO PARA ADMINISTRADOR
|--------------------------------------------------------------------------
*/

if (Auth::tieneRol('Administrador')) {

    array_unshift(
        $accesos,
        [
            'nombre' => 'Usuarios',
            'ruta' => 'index.php?controller=usuario&action=index',
            'icono' => 'bi-people',
        ]
    );
}

?>

<main class="main-content">

    <section class="dashboard-section py-5">

        <div class="container">

            <?php if ($error !== null): ?>

                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <?= e($error) ?>
                </div>

            <?php endif; ?>

            <!-- ==========================================
                 ENCABEZADO
            =========================================== -->

            <div class="row align-items-center mb-4">

                <div class="col-12 col-lg-8">

                    <span class="badge text-bg-success mb-3">
                        <?= e($usuarioActual['rol']) ?>
                    </span>

                    <h1 class="dashboard-title">

                        Hola,
                        <?= e($usuarioActual['nombre']) ?>

                    </h1>

                    <p class="dashboard-text">

                        Resumen de
                        <?= e($usuarioActual['comunidad']) ?>.

                    </p>

                </div>


                <!--
                    Administrador y Coordinador
                    pueden crear proyectos.
                -->

                <?php if (
                    Auth::tieneRol(
                        'Administrador',
                        'Coordinador'
                    )
                ): ?>

                    <div
                        class="col-12 col-lg-4
                        text-lg-end mt-3 mt-lg-0"
                    >

                        <a
                            href="<?= e(url(
                                'index.php?controller=proyecto&action=index'
                            )) ?>"
                            class="btn btn-success btn-lg"
                        >

                            <i class="bi bi-plus-circle"></i>

                            Nuevo proyecto

                        </a>

                    </div>

                <?php endif; ?>

            </div>


            <!-- ==========================================
                 TARJETAS DE RESUMEN
            =========================================== -->

            <div class="row g-4 mb-5">

                <?php foreach (
                    $tarjetasResumen as $tarjeta
                ): ?>

                    <div class="col-12 col-md-6 col-xl-3">

                        <div
                            class="dashboard-resumen-card h-100"
                        >

                            <div
                                class="dashboard-resumen-icono"
                            >

                                <i
                                    class="bi <?= e(
                                        $tarjeta['icono']
                                    ) ?>"
                                ></i>

                            </div>


                            <div>

                                <h3>
                                    <?= e($tarjeta['valor']) ?>
                                </h3>

                                <h6>
                                    <?= e($tarjeta['titulo']) ?>
                                </h6>

                                <p>
                                    <?= e($tarjeta['texto']) ?>
                                </p>

                            </div>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


            <div class="row g-4">


                <!-- ======================================
                     PROYECTOS RECIENTES
                ======================================= -->

                <div class="col-12 col-xl-8">

                    <div class="dashboard-card h-100">

                        <div
                            class="d-flex
                            justify-content-between
                            align-items-center mb-4"
                        >

                            <div>

                                <h4>
                                    Proyectos recientes
                                </h4>

                                <p class="text-muted mb-0">

                                    Últimos proyectos de esta
                                    comunidad.

                                </p>

                            </div>


                            <!--
                                Ahora redirige al módulo MVC
                                completo de proyectos.
                            -->

                            <a
                                href="<?= e(url(
                                    'index.php?controller=proyecto&action=index'
                                )) ?>"
                                class="btn btn-outline-success"
                            >

                                Ver todos

                            </a>

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
                                        <th>Avance</th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <?php if (
                                        $proyectos === []
                                    ): ?>

                                        <tr>

                                            <td
                                                colspan="4"
                                                class="text-center
                                                text-muted py-4"
                                            >

                                                No hay proyectos
                                                registrados.

                                            </td>

                                        </tr>

                                    <?php endif; ?>


                                    <?php foreach (
                                        $proyectos as $proyecto
                                    ): ?>

                                        <?php

                                        $avance = max(
                                            0,
                                            min(
                                                100,
                                                (int) $proyecto[
                                                    'avance'
                                                ]
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
                                                    $proyecto[
                                                        'nombre'
                                                    ]
                                                ) ?>

                                            </td>


                                            <td>

                                                <?= e(
                                                    $proyecto[
                                                        'responsable'
                                                    ]
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
                                                        $proyecto[
                                                            'estado'
                                                        ]
                                                    ) ?>

                                                </span>

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

                </div>


                <!-- ======================================
                     ACTIVIDADES PRÓXIMAS
                ======================================= -->

                <div class="col-12 col-xl-4">

                    <div class="dashboard-card h-100">

                        <h4>
                            Actividades próximas
                        </h4>

                        <p class="text-muted">
                            Agenda de los siguientes días.
                        </p>


                        <div class="dashboard-lista">

                            <?php if (
                                $actividades === []
                            ): ?>

                                <p class="text-muted mb-0">

                                    No hay actividades
                                    programadas.

                                </p>

                            <?php endif; ?>


                            <?php foreach (
                                $actividades as $actividad
                            ): ?>

                                <div
                                    class="dashboard-lista-item"
                                >

                                    <div
                                        class="dashboard-lista-icono"
                                    >

                                        <i
                                            class="bi
                                            bi-calendar-check"
                                        ></i>

                                    </div>


                                    <div>

                                        <h6>

                                            <?= e(
                                                $actividad[
                                                    'titulo'
                                                ]
                                            ) ?>

                                        </h6>


                                        <p>

                                            <?= e(fechaCorta(
                                                $actividad[
                                                    'fecha'
                                                ]
                                            )) ?>,

                                            <?= e(horaCorta(
                                                $actividad[
                                                    'hora'
                                                ]
                                            )) ?>

                                        </p>


                                        <small
                                            class="text-muted"
                                        >

                                            <?= e(
                                                $actividad[
                                                    'lugar'
                                                ]
                                            ) ?>

                                        </small>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- ======================================
                     MOVIMIENTOS FINANCIEROS
                ======================================= -->

                <div class="col-12 col-xl-6">

                    <div class="dashboard-card h-100">

                        <h4>
                            Movimientos financieros
                        </h4>

                        <p class="text-muted">

                            Ingresos y egresos recientes.

                        </p>


                        <div class="dashboard-lista">

                            <?php if (
                                $movimientos === []
                            ): ?>

                                <p class="text-muted mb-0">

                                    No hay movimientos registrados.

                                </p>

                            <?php endif; ?>


                            <?php foreach (
                                $movimientos as $movimiento
                            ): ?>

                                <div
                                    class="dashboard-movimiento"
                                >

                                    <div>

                                        <h6>

                                            <?= e(
                                                $movimiento[
                                                    'descripcion'
                                                ]
                                            ) ?>

                                        </h6>


                                        <p>

                                            <?= e(
                                                $movimiento[
                                                    'tipo'
                                                ]
                                            ) ?>

                                            ·

                                            <?= e(fechaCorta(
                                                $movimiento[
                                                    'fecha'
                                                ]
                                            )) ?>

                                        </p>

                                    </div>


                                    <span
                                        class="<?= $movimiento[
                                            'tipo'
                                        ] === 'Ingreso'
                                            ? 'movimiento-ingreso'
                                            : 'movimiento-egreso' ?>"
                                    >

                                        <?= e(colones(
                                            $movimiento[
                                                'monto'
                                            ]
                                        )) ?>

                                    </span>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>


                <!-- ======================================
                     ACCESOS RÁPIDOS
                ======================================= -->

                <div class="col-12 col-xl-6">

                    <div class="dashboard-card h-100">

                        <h4>
                            Accesos rápidos
                        </h4>

                        <p class="text-muted">

                            Módulos disponibles en el proyecto.

                        </p>


                        <div class="dashboard-accesos">

                            <?php foreach (
                                $accesos as $acceso
                            ): ?>

                                <a
                                    href="<?= e(url(
                                        $acceso['ruta']
                                    )) ?>"
                                >

                                    <i
                                        class="bi <?= e(
                                            $acceso['icono']
                                        ) ?>"
                                    ></i>

                                    <?= e(
                                        $acceso['nombre']
                                    ) ?>

                                </a>

                            <?php endforeach; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>