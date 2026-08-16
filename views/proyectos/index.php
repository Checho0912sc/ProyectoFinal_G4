<main class="main-content">

    <section class="modulo-section py-5">

        <div class="container">

            <!-- Configuración utilizada por proyectos.js -->
            <div
                id="configProyectos"
                data-api-url="<?= e(url('api/proyectos.php')) ?>"
                data-csrf-token="<?= e(Auth::csrfToken()) ?>"
            ></div>

            <!-- Mensajes de éxito o error -->
            <div id="mensajeProyectos"></div>

            <!-- Encabezado -->
            <div class="row align-items-center mb-4">

                <div class="col-12 col-lg-8">

                    <span class="badge text-bg-success mb-3">
                        Gestión de proyectos
                    </span>

                    <h1 class="modulo-title">
                        Proyectos comunitarios
                    </h1>

                    <p class="modulo-text">
                        Administre los proyectos, responsables,
                        fechas, presupuesto y estado.
                    </p>

                </div>

                <div class="col-12 col-lg-4 text-lg-end mt-3 mt-lg-0">

                    <button
                        type="button"
                        id="btnNuevoProyecto"
                        class="btn btn-success btn-lg"
                    >
                        <i class="bi bi-folder-plus"></i>
                        Nuevo proyecto
                    </button>

                </div>

            </div>

            <div class="row g-4">

                <!-- FORMULARIO -->
                <div class="col-12 col-xl-4">

                    <div class="modulo-card h-100">

                        <h4 id="tituloFormularioProyecto">
                            Registrar proyecto
                        </h4>

                        <p
                            id="textoFormularioProyecto"
                            class="text-muted"
                        >
                            Complete la información del proyecto.
                        </p>

                        <form id="formProyecto" novalidate>

                            <input
                                type="hidden"
                                id="idProyecto"
                                value=""
                            >

                            <!-- Nombre -->
                            <div class="mb-3">

                                <label
                                    for="nombreProyecto"
                                    class="form-label"
                                >
                                    Nombre
                                </label>

                                <input
                                    type="text"
                                    id="nombreProyecto"
                                    class="form-control"
                                    maxlength="150"
                                    placeholder="Ej: Parque comunitario"
                                >

                                <div
                                    id="errorNombreProyecto"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Grupo -->
                            <div class="mb-3">

                                <label
                                    for="idGrupo"
                                    class="form-label"
                                >
                                    Grupo
                                </label>

                                <select
                                    id="idGrupo"
                                    class="form-select"
                                >
                                    <option value="">
                                        Seleccione un grupo
                                    </option>
                                </select>

                                <div
                                    id="errorGrupo"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Responsable -->
                            <div class="mb-3">

                                <label
                                    for="idResponsable"
                                    class="form-label"
                                >
                                    Responsable
                                </label>

                                <select
                                    id="idResponsable"
                                    class="form-select"
                                >
                                    <option value="">
                                        Seleccione un responsable
                                    </option>
                                </select>

                                <div
                                    id="errorResponsable"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Descripción -->
                            <div class="mb-3">

                                <label
                                    for="descripcionProyecto"
                                    class="form-label"
                                >
                                    Descripción
                                </label>

                                <textarea
                                    id="descripcionProyecto"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Descripción del proyecto"
                                ></textarea>

                            </div>

                            <!-- Fecha inicio -->
                            <div class="mb-3">

                                <label
                                    for="fechaInicio"
                                    class="form-label"
                                >
                                    Fecha de inicio
                                </label>

                                <input
                                    type="date"
                                    id="fechaInicio"
                                    class="form-control"
                                >

                                <div
                                    id="errorFechaInicio"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Fecha fin -->
                            <div class="mb-3">

                                <label
                                    for="fechaFin"
                                    class="form-label"
                                >
                                    Fecha de finalización
                                </label>

                                <input
                                    type="date"
                                    id="fechaFin"
                                    class="form-control"
                                >

                                <div
                                    id="errorFechaFin"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Estado -->
                            <div class="mb-3">

                                <label
                                    for="estadoProyecto"
                                    class="form-label"
                                >
                                    Estado
                                </label>

                                <select
                                    id="estadoProyecto"
                                    class="form-select"
                                >
                                    <option value="Planificado">
                                        Planificado
                                    </option>

                                    <option value="En proceso">
                                        En proceso
                                    </option>

                                    <option value="Pausado">
                                        Pausado
                                    </option>

                                    <option value="Finalizado">
                                        Finalizado
                                    </option>

                                    <option value="Cancelado">
                                        Cancelado
                                    </option>
                                </select>

                            </div>

                            <!-- Presupuesto -->
                            <div class="mb-4">

                                <label
                                    for="presupuesto"
                                    class="form-label"
                                >
                                    Presupuesto
                                </label>

                                <div class="input-group">

                                    <span class="input-group-text">
                                        ₡
                                    </span>

                                    <input
                                        type="number"
                                        id="presupuesto"
                                        class="form-control"
                                        min="0"
                                        step="0.01"
                                        value="0"
                                    >

                                </div>

                                <div
                                    id="errorPresupuesto"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <!-- Guardar -->
                            <button
                                type="submit"
                                id="btnGuardarProyecto"
                                class="btn btn-success w-100"
                            >
                                <i class="bi bi-save"></i>
                                Guardar proyecto
                            </button>

                            <!-- Cancelar edición -->
                            <button
                                type="button"
                                id="btnCancelarEdicionProyecto"
                                class="btn btn-outline-secondary w-100 mt-2 d-none"
                            >
                                Cancelar edición
                            </button>

                        </form>

                    </div>

                </div>

                <!-- TABLA -->
                <div class="col-12 col-xl-8">

                    <div class="modulo-card">

                        <div
                            class="d-flex flex-column flex-md-row
                            justify-content-between
                            align-items-md-center mb-4 gap-3"
                        >

                            <div>

                                <h4>
                                    Proyectos registrados
                                </h4>

                                <p class="text-muted mb-0">
                                    Proyectos pertenecientes a
                                    la comunidad actual.
                                </p>

                            </div>

                            <div style="max-width: 280px;">

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="buscarProyecto"
                                        class="form-control"
                                        placeholder="Buscar proyecto..."
                                    >

                                </div>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table align-middle modulo-table">

                                <thead>

                                    <tr>
                                        <th>Proyecto</th>
                                        <th>Grupo</th>
                                        <th>Responsable</th>
                                        <th>Inicio</th>
                                        <th>Presupuesto</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>

                                </thead>

                                <tbody id="tablaProyectos">

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center text-muted py-4"
                                        >
                                            Cargando proyectos...
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

</main>