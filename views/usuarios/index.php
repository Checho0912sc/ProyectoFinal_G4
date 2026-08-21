<?php

$usuarioSesion = Auth::usuario();

?>

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
                data-id-usuario-actual="<?= e(
                    $usuarioSesion[
                        'id_usuario'
                    ] ?? 0
                ) ?>"
            ></div>

            <div id="mensajeUsuarios"></div>

            <div class="row align-items-center mb-4">

                <div class="col-12">

                    <span
                        class="badge text-bg-success mb-3"
                    >
                        Gestión de usuarios
                    </span>

                    <h1 class="modulo-title">
                        Usuarios y roles
                    </h1>

                    <p class="modulo-text">
                        Consulta los miembros y administra
                        su rol y estado dentro de la comunidad.
                    </p>

                </div>

            </div>

            <div class="row g-4">

                <div class="col-12 col-xl-4">

                    <div class="modulo-card h-100">

                        <h4 id="tituloFormulario">
                            Datos del miembro
                        </h4>

                        <p
                            id="textoFormulario"
                            class="text-muted"
                        >
                            Selecciona un usuario de la tabla
                            para consultar su información.
                        </p>

                        <form
                            id="formUsuario"
                            novalidate
                        >
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
                                        readonly
                                    >

                                </div>

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
                                        readonly
                                    >

                                </div>

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
                                        readonly
                                    >

                                </div>

                            </div>

                            <div
                                class="alert alert-light
                                border small"
                            >
                                Los datos personales solamente
                                pueden ser modificados por el
                                propietario desde su perfil.
                            </div>

                            <div class="mb-3">

                                <label
                                    for="idRol"
                                    class="form-label"
                                >
                                    Rol en la comunidad
                                </label>

                                <select
                                    id="idRol"
                                    class="form-select"
                                    disabled
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
                                    Estado de la membresía
                                </label>

                                <select
                                    id="estado"
                                    class="form-select"
                                    disabled
                                >
                                    <option value="">
                                        Seleccione un estado
                                    </option>

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
                                disabled
                            >
                                <i class="bi bi-save"></i>
                                Guardar membresía
                            </button>

                            <button
                                type="button"
                                id="btnCancelarEdicion"
                                class="btn
                                btn-outline-secondary
                                w-100 mt-2 d-none"
                            >
                                Limpiar selección
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
                            align-items-md-center
                            mb-4 gap-3"
                        >
                            <div>

                                <h4>
                                    Miembros de la comunidad
                                </h4>

                                <p class="text-muted mb-0">
                                    Personas asociadas con la
                                    comunidad actual.
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
                                        placeholder="Buscar miembro..."
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
                                            Cargando miembros...
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