<?php

$tarjetasResumen = [
    [
        'titulo' => 'Total actividades',
        'valor'  => $resumen['total'],
        'texto'  => 'Registradas en la comunidad',
        'icono'  => 'bi-calendar-event',
    ],
    [
        'titulo' => 'Reuniones',
        'valor'  => $resumen['reuniones'],
        'texto'  => 'Comités y junta directiva',
        'icono'  => 'bi-people',
    ],
    [
        'titulo' => 'Eventos y talleres',
        'valor'  => $resumen['eventos'],
        'texto'  => 'Capacitaciones y charlas',
        'icono'  => 'bi-easel',
    ],
    [
        'titulo' => 'Jornadas comunales',
        'valor'  => $resumen['jornadas'],
        'texto'  => 'Trabajo voluntario activo',
        'icono'  => 'bi-tools',
    ],
];

?>

<main class="main-content">
    <section class="dashboard-section py-5">
        <div class="container">

            <?php if ($error = Auth::flash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <?= e($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <?php if ($exito = Auth::flash('exito')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <?= e($exito) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="row align-items-center mb-4">

                <div class="col-12 col-lg-8">

                    <span class="badge text-bg-success mb-3">
                        Gestión de actividades
                    </span>

                    <h1 class="dashboard-title">
                        Actividades y Eventos
                    </h1>

                    <p class="dashboard-text">
                        Programación de reuniones, eventos, jornadas comunitarias
                        y actividades vinculadas a los proyectos de la asociación.
                    </p>

                </div>

                <?php if (
                    Auth::tieneRol('Administrador', 'Coordinador')
                ): ?>

                    <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <button
                            class="btn btn-success btn-lg"
                            data-bs-toggle="modal"
                            data-bs-target="#modalNuevaActividad"
                        >
                            <i class="bi bi-calendar-plus"></i>
                            Nueva actividad
                        </button>
                    </div>

                <?php endif; ?>

            </div>

            <div class="row g-4 mb-5">

                <?php foreach ($tarjetasResumen as $tarjeta): ?>

                    <div class="col-12 col-md-6 col-xl-3">
                        <div class="dashboard-resumen-card h-100">

                            <div class="dashboard-resumen-icono">
                                <i class="bi <?= e($tarjeta['icono']) ?>"></i>
                            </div>

                            <div>
                                <h3><?= e((string) $tarjeta['valor']) ?></h3>
                                <h6><?= e($tarjeta['titulo']) ?></h6>
                                <p><?= e($tarjeta['texto']) ?></p>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

            <div class="row g-4">

                <div class="col-12 col-xl-8">
                    <div class="dashboard-card h-100">

                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <div>
                                <h4>Actividades registradas</h4>
                                <p class="text-muted mb-0">
                                    Reuniones, eventos y jornadas planificadas.
                                </p>
                            </div>

                            <form
                                action="<?= e(url('index.php')) ?>"
                                method="get"
                                class="d-flex align-items-center gap-2"
                            >
                                <input type="hidden" name="controller" value="actividades">
                                <input type="hidden" name="action" value="index">
                                <select
                                    name="tipo"
                                    class="form-select form-select-sm w-auto"
                                    onchange="this.form.submit()"
                                    aria-label="Filtrar por tipo"
                                >
                                    <option value="" <?= $filtroTipo === null || $filtroTipo === '' ? 'selected' : '' ?>>
                                        Todas las categorías
                                    </option>
                                    <option value="Reunion"  <?= $filtroTipo === 'Reunion'  ? 'selected' : '' ?>>Reuniones</option>
                                    <option value="Evento"   <?= $filtroTipo === 'Evento'   ? 'selected' : '' ?>>Eventos</option>
                                    <option value="Jornada"  <?= $filtroTipo === 'Jornada'  ? 'selected' : '' ?>>Jornadas</option>
                                    <option value="Proyecto" <?= $filtroTipo === 'Proyecto' ? 'selected' : '' ?>>Proyectos</option>
                                </select>
                            </form>

                        </div>

                        <div class="dashboard-lista">

                            <?php if ($listado === []): ?>

                                <p class="text-muted mb-0">
                                    No hay actividades registradas.
                                </p>

                            <?php endif; ?>

                            <?php foreach ($listado as $actividad): ?>

                                <?php

                                $iconoTipo = match ($actividad['tipo']) {
                                    'Reunion'  => 'bi-people',
                                    'Evento'   => 'bi-easel',
                                    'Jornada'  => 'bi-tools',
                                    default    => 'bi-folder-check',
                                };

                                $claseBadge = match ($actividad['tipo']) {
                                    'Reunion'  => 'text-bg-primary',
                                    'Evento'   => 'text-bg-warning',
                                    'Jornada'  => 'text-bg-success',
                                    default    => 'text-bg-secondary',
                                };

                                $claseEstado = match ($actividad['estado']) {
                                    'Realizada'  => 'text-bg-success',
                                    'Cancelada'  => 'text-bg-danger',
                                    default      => 'text-bg-secondary',
                                };

                                ?>

                                <div class="dashboard-lista-item align-items-start">

                                    <div class="dashboard-lista-icono">
                                        <i class="bi <?= e($iconoTipo) ?>"></i>
                                    </div>

                                    <div class="flex-grow-1">

                                        <div class="d-flex justify-content-between align-items-start gap-2">

                                            <div>
                                                <span class="badge <?= e($claseBadge) ?> mb-1">
                                                    <?= e($actividad['tipo']) ?>
                                                </span>
                                                <h6><?= e($actividad['titulo']) ?></h6>
                                            </div>

                                            <span class="badge <?= e($claseEstado) ?> flex-shrink-0">
                                                <?= e($actividad['estado']) ?>
                                            </span>

                                        </div>

                                        <p class="text-muted small mb-1">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            <?= e(fechaCorta($actividad['fecha'])) ?>
                                            &nbsp;·&nbsp;
                                            <i class="bi bi-clock me-1"></i>
                                            <?= e(horaCorta($actividad['hora'])) ?>
                                            &nbsp;·&nbsp;
                                            <i class="bi bi-geo-alt me-1"></i>
                                            <?= e($actividad['lugar']) ?>
                                        </p>

                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-person me-1"></i>
                                            <?= e($actividad['responsable']) ?>
                                            <?php if (!empty($actividad['proyecto'])): ?>
                                                &nbsp;·&nbsp;
                                                <i class="bi bi-folder-check me-1"></i>
                                                <?= e($actividad['proyecto']) ?>
                                            <?php endif; ?>
                                        </p>

                                    </div>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="d-flex flex-column gap-4">

                        <div class="dashboard-card">

                            <h4>Próximas actividades</h4>

                            <p class="text-muted">
                                Eventos agendados desde hoy.
                            </p>

                            <div class="dashboard-lista">

                                <?php if ($proximas === []): ?>
                                    <p class="text-muted mb-0">
                                        No hay actividades próximas.
                                    </p>
                                <?php endif; ?>

                                <?php foreach ($proximas as $proxima): ?>

                                    <div class="dashboard-lista-item">
                                        <div class="dashboard-lista-icono">
                                            <i class="bi bi-calendar-event"></i>
                                        </div>
                                        <div>
                                            <h6><?= e($proxima['titulo']) ?></h6>
                                            <p>
                                                <?= e(fechaCorta($proxima['fecha'])) ?>,
                                                <?= e(horaCorta($proxima['hora'])) ?>
                                            </p>
                                            <small class="text-muted">
                                                <?= e($proxima['lugar']) ?>
                                            </small>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>
    </section>
</main>

<?php if (Auth::tieneRol('Administrador', 'Coordinador')): ?>

<!-- Modal nueva actividad -->
<div class="modal fade" id="modalNuevaActividad" tabindex="-1"
     aria-labelledby="modalNuevaActividadLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalNuevaActividadLabel">
                    <i class="bi bi-calendar-plus me-1"></i>
                    Nueva actividad
                </h5>
                <button type="button" class="btn-close btn-close-white"
                        data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4">
                <form
                    action="<?= e(url('index.php?controller=actividades&action=guardar')) ?>"
                    method="post"
                >
                    <input type="hidden" name="csrf_token"
                           value="<?= e(Auth::csrfToken()) ?>">

                    <div class="row g-3">

                        <div class="col-12">
                            <label for="titulo" class="form-label fw-semibold">
                                Nombre de la actividad
                            </label>
                            <input
                                type="text" class="form-control"
                                id="titulo" name="titulo"
                                placeholder="Ej. Reunión mensual de coordinación"
                                required
                            >
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="tipo" class="form-label fw-semibold">
                                Tipo
                            </label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="" disabled selected>Seleccione el tipo</option>
                                <option value="Reunion">Reunión</option>
                                <option value="Evento">Evento / Taller</option>
                                <option value="Jornada">Jornada Comunitaria</option>
                                <option value="Proyecto">Actividad de Proyecto</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="id_proyecto" class="form-label fw-semibold">
                                Proyecto asociado (Opcional)
                            </label>
                            <select class="form-select" id="id_proyecto" name="id_proyecto">
                                <option value="">Ninguno / General</option>
                                <?php foreach ($proyectos as $proyecto): ?>
                                    <option value="<?= e((string) $proyecto['id_proyecto']) ?>">
                                        <?= e($proyecto['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="fecha" class="form-label fw-semibold">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="hora" class="form-label fw-semibold">Hora</label>
                            <input type="time" class="form-control" id="hora" name="hora" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label for="lugar" class="form-label fw-semibold">Lugar</label>
                            <input
                                type="text" class="form-control"
                                id="lugar" name="lugar"
                                placeholder="Ej. Salón Comunal"
                                required
                            >
                        </div>

                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-semibold">
                                Descripción (Opcional)
                            </label>
                            <textarea
                                class="form-control" id="descripcion" name="descripcion"
                                rows="3"
                                placeholder="Objetivos o detalles de la actividad..."
                            ></textarea>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-save me-1"></i>
                            Guardar actividad
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>

<?php endif; ?>
