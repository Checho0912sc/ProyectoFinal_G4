<?php

$tarjetasResumen = [
    [
        'titulo' => 'Comités activos',
        'valor' => $resumen['comites'],
        'texto' => 'Equipos de trabajo activos',
        'icono' => 'bi-person-workspace',
    ],
    [
        'titulo' => 'Miembros asignados',
        'valor' => $resumen['miembros'],
        'texto' => 'Vecinos voluntarios activos',
        'icono' => 'bi-people',
    ],
    [
        'titulo' => 'Tareas en curso',
        'valor' => $resumen['tareas'],
        'texto' => 'En ejecución por comités',
        'icono' => 'bi-list-task',
    ],
    [
        'titulo' => 'Proyectos apoyados',
        'valor' => $resumen['proyectos'],
        'texto' => 'Vinculados a comités activos',
        'icono' => 'bi-folder-check',
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
                        Gestión de grupos
                    </span>

                    <h1 class="dashboard-title">
                        Comités y Grupos de Trabajo
                    </h1>

                    <p class="dashboard-text">
                        Organización de comités y equipos responsables de apoyar
                        proyectos, actividades y tareas de la asociación.
                    </p>

                </div>

                <?php if (
                    Auth::tieneRol('Administrador', 'Coordinador')
                ): ?>

                    <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#modalNuevoGrupo">
                            <i class="bi bi-plus-circle"></i>
                            Nuevo comité
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
                                <h4>Directorio de comités</h4>
                                <p class="text-muted mb-0">
                                    Comités registrados en la asociación.
                                </p>
                            </div>

                            <?php if (
                                Auth::tieneRol('Administrador', 'Coordinador')
                            ): ?>
                                <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#modalAsociarMiembro">
                                    <i class="bi bi-person-plus me-1"></i>
                                    Asociar miembro
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle dashboard-table">
                                <thead>
                                    <tr>
                                        <th>Comité</th>
                                        <th>Área</th>
                                        <th>Responsable</th>
                                        <th>Miembros</th>
                                        <th>Proyecto activo</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php if ($grupos === []): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                No hay comités registrados.
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <?php foreach ($grupos as $grupo): ?>
                                        <tr>
                                            <td>
                                                <strong><?= e($grupo['nombre']) ?></strong>
                                            </td>
                                            <td>
                                                <span class="badge text-bg-success">
                                                    <?= e($grupo['area']) ?>
                                                </span>
                                            </td>
                                            <td><?= e($grupo['responsable']) ?></td>
                                            <td><?= e((string) $grupo['total_miembros']) ?></td>
                                            <td class="text-muted small">
                                                <?= e($grupo['proyecto_activo'] ?? 'Sin proyecto activo') ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>

                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="d-flex flex-column gap-4">

                        <div class="dashboard-card">

                            <h4>Coordinadores</h4>

                            <p class="text-muted">
                                Líderes de comités activos.
                            </p>

                            <div class="dashboard-lista">

                                <?php if ($coordinadores === []): ?>
                                    <p class="text-muted mb-0">
                                        No hay coordinadores asignados.
                                    </p>
                                <?php endif; ?>

                                <?php foreach ($coordinadores as $coordinador): ?>

                                    <div class="dashboard-lista-item">
                                        <div class="dashboard-lista-icono">
                                            <i class="bi bi-person-badge"></i>
                                        </div>
                                        <div>
                                            <h6><?= e($coordinador['nombre']) ?></h6>
                                            <p>
                                                <?= e($coordinador['grupo_nombre']) ?>
                                                · <?= e($coordinador['area']) ?>
                                            </p>
                                        </div>
                                    </div>

                                <?php endforeach; ?>

                            </div>

                        </div>

                        <div class="dashboard-card">

                            <h4>Tareas recientes</h4>

                            <p class="text-muted">
                                Estado de responsabilidades asignadas.
                            </p>

                            <div class="dashboard-lista">

                                <?php if ($tareas === []): ?>
                                    <p class="text-muted mb-0">
                                        No hay tareas registradas.
                                    </p>
                                <?php endif; ?>

                                <?php foreach ($tareas as $tarea): ?>

                                    <?php

                                    $claseEstado = match ($tarea['estado']) {
                                        'Finalizada' => 'text-bg-success',
                                        'En proceso' => 'text-bg-warning',
                                        'Cancelada' => 'text-bg-danger',
                                        default => 'text-bg-secondary',
                                    };

                                    ?>

                                    <div class="dashboard-movimiento">
                                        <div>
                                            <h6><?= e($tarea['titulo']) ?></h6>
                                            <p><?= e($tarea['grupo_nombre']) ?></p>
                                        </div>
                                        <span class="badge <?= e($claseEstado) ?>">
                                            <?= e($tarea['estado']) ?>
                                        </span>
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

    <!-- Modal nuevo comité -->
    <div class="modal fade" id="modalNuevoGrupo" tabindex="-1" aria-labelledby="modalNuevoGrupoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalNuevoGrupoLabel">
                        <i class="bi bi-plus-circle me-1"></i>
                        Nuevo comité
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="<?= e(url('index.php?controller=grupos&action=guardar')) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(Auth::csrfToken()) ?>">

                        <div class="row g-3">

                            <div class="col-12">
                                <label for="nombreGrupo" class="form-label fw-semibold">
                                    Nombre del comité
                                </label>
                                <input type="text" class="form-control" id="nombreGrupo" name="nombre"
                                    placeholder="Ej. Comité de Seguridad Vecinal" required>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="areaGrupo" class="form-label fw-semibold">Área</label>
                                <select class="form-select" id="areaGrupo" name="area" required>
                                    <option value="" disabled selected>Seleccione el área</option>
                                    <option value="Ecología y Ambiente">Ecología y Ambiente</option>
                                    <option value="Infraestructura y Obras">Infraestructura y Obras</option>
                                    <option value="Social y Educación">Social y Educación</option>
                                    <option value="Administración y Tesorería">Administración y Tesorería</option>
                                    <option value="Seguridad Comunitaria">Seguridad Comunitaria</option>
                                    <option value="Deportes y Recreación">Deportes y Recreación</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="responsableGrupo" class="form-label fw-semibold">
                                    Responsable
                                </label>
                                <select class="form-select" id="responsableGrupo" name="id_responsable" required>
                                    <option value="" disabled selected>Seleccione un responsable</option>
                                    <?php foreach ($miembros as $miembro): ?>
                                        <option value="<?= e((string) $miembro['id_usuario']) ?>">
                                            <?= e($miembro['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="descripcionGrupo" class="form-label fw-semibold">
                                    Objetivos (Opcional)
                                </label>
                                <textarea class="form-control" id="descripcionGrupo" name="descripcion" rows="3"
                                    placeholder="Propósito y tareas del comité..."></textarea>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save me-1"></i>
                                Guardar comité
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Modal asociar miembro -->
    <div class="modal fade" id="modalAsociarMiembro" tabindex="-1" aria-labelledby="modalAsociarMiembroLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalAsociarMiembroLabel">
                        <i class="bi bi-person-plus me-1"></i>
                        Asociar miembro al comité
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>

                <div class="modal-body p-4">
                    <form action="<?= e(url('index.php?controller=grupos&action=asociar')) ?>" method="post">
                        <input type="hidden" name="csrf_token" value="<?= e(Auth::csrfToken()) ?>">

                        <div class="row g-3">

                            <div class="col-12">
                                <label for="selectGrupo" class="form-label fw-semibold">Comité</label>
                                <select class="form-select" id="selectGrupo" name="id_grupo" required>
                                    <option value="" disabled selected>Seleccione un comité</option>
                                    <?php foreach ($grupos as $g): ?>
                                        <option value="<?= e((string) $g['id_grupo']) ?>">
                                            <?= e($g['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="selectMiembro" class="form-label fw-semibold">Miembro</label>
                                <select class="form-select" id="selectMiembro" name="id_usuario" required>
                                    <option value="" disabled selected>Seleccione un vecino</option>
                                    <?php foreach ($miembros as $m): ?>
                                        <option value="<?= e((string) $m['id_usuario']) ?>">
                                            <?= e($m['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="rolGrupo" class="form-label fw-semibold">
                                    Rol en el comité
                                </label>
                                <select class="form-select" id="rolGrupo" name="rol_grupo" required>
                                    <option value="Colaborador" selected>Colaborador / Voluntario</option>
                                    <option value="Secretario">Secretario</option>
                                    <option value="Vocal">Vocal</option>
                                    <option value="Sub-tesorero">Sub-tesorero</option>
                                </select>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-outline-secondary"
                                data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Asociar miembro
                            </button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

<?php endif; ?>