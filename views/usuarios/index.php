<main class="main-content">

    <section class="modulo-section py-5">

        <div class="container">

            <div
                id="configUsuarios"
                data-api-url="<?= e(
                    url('api/usuarios.php')
                ) ?>"
                data-csrf-token="<?= e(
                    Auth::csrfToken()
                ) ?>"
            ></div>

            <div id="mensajeUsuarios"></div>

            <div class="row align-items-center mb-4">

                <div class="col-12 col-lg-8">

                    <span
                        class="badge text-bg-success mb-3"
                    >
                        Gestión de usuarios
                    </span>

                    <h1 class="modulo-title">
                        Usuarios y roles
                    </h1>

                    <p class="modulo-text">
                        Administración de miembros de la
                        asociación, roles asignados y estado
                        dentro de la comunidad.
                    </p>

                </div>

                <div
                    class="col-12 col-lg-4
                    text-lg-end mt-3 mt-lg-0"
                >

                    <button
                        type="button"
                        id="btnNuevoUsuario"
                        class="btn btn-success btn-lg"
                    >
                        <i class="bi bi-person-plus"></i>
                        Nuevo usuario
                    </button>

                </div>

            </div>

            <div class="row g-4">

                <div class="col-12 col-xl-4">

                    <div class="modulo-card h-100">

                        <h4 id="tituloFormulario">
                            Registrar usuario
                        </h4>

                        <p
                            id="textoFormulario"
                            class="text-muted"
                        >
                            Complete los datos del nuevo
                            miembro.
                        </p>

                        <form id="formUsuario" novalidate>

                            <input
                                type="hidden"
                                id="idUsuario"
                                value=""
                            >

                            <div class="mb-3">

                                <label
                                    for="nombre"
                                    class="form-label"
                                >
                                    Nombre completo
                                </label>

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >
                                        <i
                                            class="bi bi-person"
                                        ></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="nombre"
                                        class="form-control"
                                        maxlength="120"
                                        placeholder="Ej: Ana Rodríguez"
                                    >

                                </div>

                                <div
                                    id="errorNombre"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <div class="mb-3">

                                <label
                                    for="correo"
                                    class="form-label"
                                >
                                    Correo electrónico
                                </label>

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >
                                        <i
                                            class="bi bi-envelope"
                                        ></i>
                                    </span>

                                    <input
                                        type="email"
                                        id="correo"
                                        class="form-control"
                                        maxlength="191"
                                        placeholder="correo@ejemplo.com"
                                    >

                                </div>

                                <div
                                    id="errorCorreo"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <div class="mb-3">

                                <label
                                    for="telefono"
                                    class="form-label"
                                >
                                    Teléfono
                                </label>

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >
                                        <i
                                            class="bi bi-telephone"
                                        ></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="telefono"
                                        class="form-control"
                                        maxlength="20"
                                        placeholder="8888-8888"
                                    >

                                </div>

                            </div>

                            <div class="mb-3">

                                <label
                                    for="contrasena"
                                    class="form-label"
                                >
                                    Contraseña
                                </label>

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >
                                        <i
                                            class="bi bi-lock"
                                        ></i>
                                    </span>

                                    <input
                                        type="password"
                                        id="contrasena"
                                        class="form-control"
                                        maxlength="255"
                                        placeholder="Mínimo 6 caracteres"
                                    >

                                </div>

                                <div
                                    id="ayudaContrasena"
                                    class="form-text"
                                >
                                    Es obligatoria al crear
                                    un usuario.
                                </div>

                                <div
                                    id="errorContrasena"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <div class="mb-3">

                                <label
                                    for="idRol"
                                    class="form-label"
                                >
                                    Rol
                                </label>

                                <select
                                    id="idRol"
                                    class="form-select"
                                >
                                    <option value="">
                                        Seleccione un rol
                                    </option>
                                </select>

                                <div
                                    id="errorRol"
                                    class="text-danger small mt-1"
                                ></div>

                            </div>

                            <div class="mb-4">

                                <label
                                    for="estado"
                                    class="form-label"
                                >
                                    Estado
                                </label>

                                <select
                                    id="estado"
                                    class="form-select"
                                >
                                    <option value="Activo">
                                        Activo
                                    </option>

                                    <option value="Inactivo">
                                        Inactivo
                                    </option>
                                </select>

                            </div>

                            <button
                                type="submit"
                                id="btnGuardarUsuario"
                                class="btn btn-success w-100"
                            >
                                <i class="bi bi-save"></i>
                                Guardar usuario
                            </button>

                            <button
                                type="button"
                                id="btnCancelarEdicion"
                                class="btn btn-outline-secondary
                                w-100 mt-2 d-none"
                            >
                                Cancelar edición
                            </button>

                        </form>

                    </div>

                </div>

                <div class="col-12 col-xl-8">

                    <div class="modulo-card">

                        <div
                            class="d-flex flex-column
                            flex-md-row
                            justify-content-between
                            align-items-md-center mb-4 gap-3"
                        >

                            <div>

                                <h4>
                                    Usuarios registrados
                                </h4>

                                <p class="text-muted mb-0">
                                    Miembros registrados en
                                    la comunidad actual.
                                </p>

                            </div>

                            <div style="max-width: 280px;">

                                <div class="input-group">

                                    <span
                                        class="input-group-text"
                                    >
                                        <i
                                            class="bi bi-search"
                                        ></i>
                                    </span>

                                    <input
                                        type="text"
                                        id="buscarUsuario"
                                        class="form-control"
                                        placeholder="Buscar usuario..."
                                    >

                                </div>

                            </div>

                        </div>

                        <div class="table-responsive">

                            <table
                                class="table align-middle
                                modulo-table"
                            >

                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Rol</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>

                                <tbody id="tablaUsuarios">

                                    <tr>
                                        <td
                                            colspan="5"
                                            class="text-center
                                            text-muted py-4"
                                        >
                                            Cargando usuarios...
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