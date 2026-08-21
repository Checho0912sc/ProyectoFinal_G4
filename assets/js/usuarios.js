$(function () {

    const $config = $("#configUsuarios");

    const apiUrl =
        $config.data("api-url");

    const csrfToken =
        $config.data("csrf-token");

    const idUsuarioActual = Number(
        $config.data("id-usuario-actual")
    );

    cargarRoles();
    cargarUsuarios();

    $("#formUsuario").on("submit", function (evento) {
        evento.preventDefault();
        guardarMembresia();
    });

    $("#btnCancelarEdicion").on("click", function () {
        limpiarSeleccion();
    });

    $("#buscarUsuario").on("input", function () {
        filtrarUsuarios();
    });

    $("#tablaUsuarios").on(
        "click",
        ".btn-editar",
        function () {
            const idUsuario = Number(
                $(this).data("id")
            );

            cargarUsuario(idUsuario);
        }
    );

    $("#tablaUsuarios").on(
        "click",
        ".btn-desactivar",
        function () {
            const idUsuario = Number(
                $(this).data("id")
            );

            desactivarUsuario(idUsuario);
        }
    );


    // ------------------ CARGAR ROLES (Llena el select con los roles disponibles) ------------------

    function cargarRoles() {

        $.ajax({
            url: apiUrl,
            method: "GET",

            data: {
                recurso: "roles"
            },

            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {
                    mostrarMensaje(
                        "danger",
                        respuesta.mensaje
                    );

                    return;
                }

                const $select = $("#idRol");

                $select
                    .find("option:not(:first)")
                    .remove();

                respuesta.roles.forEach(function (rol) {

                    $("<option>")
                        .val(rol.id_rol)
                        .text(rol.nombre)
                        .appendTo($select);

                });
            },

            error: function (solicitud) {

                mostrarMensaje(
                    "danger",
                    obtenerMensajeError(solicitud)
                );

            }
        });
    }


    // ------------------ CARGAR USUARIOS (Trae los miembros de la comunidad actual) ------------------

    function cargarUsuarios() {

        $.ajax({
            url: apiUrl,
            method: "GET",
            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {
                    mostrarMensaje(
                        "danger",
                        respuesta.mensaje
                    );

                    return;
                }

                mostrarUsuarios(
                    respuesta.usuarios || []
                );
            },

            error: function (solicitud) {

                mostrarMensaje(
                    "danger",
                    obtenerMensajeError(solicitud)
                );

            }
        });
    }


    // ------------------ MOSTRAR USUARIOS (Arma las filas y los botones de la tabla) ------------------

    function mostrarUsuarios(usuarios) {

        const $tabla = $("#tablaUsuarios");

        $tabla.empty();

        if (usuarios.length === 0) {

            $("<tr>")
                .append(
                    $("<td>")
                        .attr("colspan", 5)
                        .addClass(
                            "text-center text-muted py-4"
                        )
                        .text(
                            "No hay miembros registrados."
                        )
                )
                .appendTo($tabla);

            return;
        }

        usuarios.forEach(function (usuario) {

            const esUsuarioActual =
                Number(usuario.id_usuario)
                === idUsuarioActual;

            const usuarioActivo =
                usuario.estado === "Activo";

            const textoBusqueda = [
                usuario.nombre,
                usuario.correo,
                usuario.rol
            ]
                .join(" ")
                .toLowerCase();

            const $fila = $("<tr>")
                .addClass("fila-usuario")
                .attr(
                    "data-busqueda",
                    textoBusqueda
                );

            $("<td>")
                .text(usuario.nombre)
                .appendTo($fila);

            $("<td>")
                .text(usuario.correo)
                .appendTo($fila);

            $("<td>")
                .text(usuario.rol)
                .appendTo($fila);

            const claseEstado = usuarioActivo
                ? "text-bg-success"
                : "text-bg-secondary";

            const $badge = $("<span>")
                .addClass(
                    "badge " + claseEstado
                )
                .text(usuario.estado);

            $("<td>")
                .append($badge)
                .appendTo($fila);

            const $acciones = $("<td>");

            $("<button>")
                .attr("type", "button")
                .addClass(
                    "btn btn-sm btn-outline-primary me-2 btn-editar"
                )
                .data(
                    "id",
                    usuario.id_usuario
                )
                .attr(
                    "title",
                    esUsuarioActual
                        ? "No puedes modificar tu propia membresía"
                        : "Ver y editar membresía"
                )
                .prop(
                    "disabled",
                    esUsuarioActual
                )
                .html(
                    '<i class="bi bi-pencil"></i>'
                )
                .appendTo($acciones);

            $("<button>")
                .attr("type", "button")
                .addClass(
                    "btn btn-sm btn-outline-danger btn-desactivar"
                )
                .data(
                    "id",
                    usuario.id_usuario
                )
                .attr(
                    "title",
                    esUsuarioActual
                        ? "No puedes desactivar tu propia membresía"
                        : usuarioActivo
                            ? "Desactivar membresía"
                            : "Membresía inactiva"
                )
                .prop(
                    "disabled",
                    esUsuarioActual || !usuarioActivo
                )
                .html(
                    '<i class="bi bi-person-dash"></i>'
                )
                .appendTo($acciones);

            $acciones.appendTo($fila);
            $fila.appendTo($tabla);

        });
    }


    // ------------------ CARGAR UN USUARIO (Muestra sus datos en la tarjeta izquierda) ------------------

    function cargarUsuario(idUsuario) {

        if (idUsuario <= 0) {
            return;
        }

        $.ajax({
            url: apiUrl,
            method: "GET",

            data: {
                id: idUsuario
            },

            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {
                    mostrarMensaje(
                        "danger",
                        respuesta.mensaje
                    );

                    return;
                }

                const usuario = respuesta.usuario;

                $("#idUsuario")
                    .val(usuario.id_usuario);

                $("#nombre")
                    .val(usuario.nombre);

                $("#correo")
                    .val(usuario.correo);

                $("#telefono")
                    .val(usuario.telefono || "");

                $("#idRol")
                    .val(usuario.id_rol)
                    .prop("disabled", false);

                $("#estado")
                    .val(usuario.estado)
                    .prop("disabled", false);

                $("#btnGuardarUsuario")
                    .prop("disabled", false);

                $("#btnCancelarEdicion")
                    .removeClass("d-none");

                $("#tituloFormulario")
                    .text("Editar membresía");

                $("#textoFormulario")
                    .text(
                        "Puedes cambiar el rol y el estado dentro de esta comunidad."
                    );

                $("#errorRol").text("");
            },

            error: function (solicitud) {

                mostrarMensaje(
                    "danger",
                    obtenerMensajeError(solicitud)
                );

            }
        });
    }


    // ------------------ GUARDAR MEMBRESÍA (Actualiza solamente el rol y el estado) ------------------

    function guardarMembresia() {

        const idUsuario = Number(
            $("#idUsuario").val()
        );

        const idRol = Number(
            $("#idRol").val()
        );

        const estado =
            $("#estado").val();

        $("#errorRol").text("");

        if (idUsuario <= 0) {

            mostrarMensaje(
                "danger",
                "Selecciona un miembro de la tabla."
            );

            return;
        }

        if (idRol <= 0) {

            $("#errorRol").text(
                "Selecciona un rol."
            );

            return;
        }

        if (
            estado !== "Activo"
            && estado !== "Inactivo"
        ) {

            mostrarMensaje(
                "danger",
                "Selecciona un estado válido."
            );

            return;
        }

        $.ajax({
            url:
                apiUrl
                + "?id="
                + encodeURIComponent(idUsuario),

            method: "PUT",
            contentType: "application/json",
            dataType: "json",

            headers: {
                "X-CSRF-Token": csrfToken
            },

            data: JSON.stringify({
                id_rol: idRol,
                estado: estado
            }),

            success: function (respuesta) {

                mostrarMensaje(
                    "success",
                    respuesta.mensaje
                );

                limpiarSeleccion();
                cargarUsuarios();
            },

            error: function (solicitud) {

                mostrarMensaje(
                    "danger",
                    obtenerMensajeError(solicitud)
                );

            }
        });
    }


    // ------------------ DESACTIVAR USUARIO (Quita la membresía sin borrar la cuenta) ------------------

    function desactivarUsuario(idUsuario) {

        const confirmar = window.confirm(
            "¿Deseas desactivar esta membresía?"
        );

        if (!confirmar) {
            return;
        }

        $.ajax({
            url:
                apiUrl
                + "?id="
                + encodeURIComponent(idUsuario),

            method: "DELETE",
            dataType: "json",

            headers: {
                "X-CSRF-Token": csrfToken
            },

            success: function (respuesta) {

                mostrarMensaje(
                    "success",
                    respuesta.mensaje
                );

                limpiarSeleccion();
                cargarUsuarios();
            },

            error: function (solicitud) {

                mostrarMensaje(
                    "danger",
                    obtenerMensajeError(solicitud)
                );

            }
        });
    }


    function filtrarUsuarios() {

        const texto = String(
            $("#buscarUsuario").val()
        )
            .toLowerCase()
            .trim();

        let cantidadVisible = 0;

        $("#filaSinResultados").remove();

        const $filas =
            $("#tablaUsuarios .fila-usuario");

        if ($filas.length === 0) {
            return;
        }

        $filas.each(function () {

            const datos = String(
                $(this).attr("data-busqueda") || ""
            );

            const mostrar =
                datos.includes(texto);

            $(this).toggle(mostrar);

            if (mostrar) {
                cantidadVisible++;
            }

        });

        if (cantidadVisible === 0) {

            $("<tr>")
                .attr(
                    "id",
                    "filaSinResultados"
                )
                .append(
                    $("<td>")
                        .attr("colspan", 5)
                        .addClass(
                            "text-center text-muted py-4"
                        )
                        .text(
                            "No se encontraron miembros."
                        )
                )
                .appendTo("#tablaUsuarios");
        }
    }


    function limpiarSeleccion() {

        $("#idUsuario").val("");
        $("#nombre").val("");
        $("#correo").val("");
        $("#telefono").val("");

        $("#idRol")
            .val("")
            .prop("disabled", true);

        $("#estado")
            .val("")
            .prop("disabled", true);

        $("#btnGuardarUsuario")
            .prop("disabled", true);

        $("#btnCancelarEdicion")
            .addClass("d-none");

        $("#tituloFormulario")
            .text("Datos del miembro");

        $("#textoFormulario")
            .text(
                "Selecciona un usuario de la tabla para consultar su información."
            );

        $("#errorRol").text("");
    }


    function mostrarMensaje(tipo, mensaje) {

        const $alerta = $("<div>")
            .addClass(
                "alert alert-" + tipo
            )
            .attr(
                "role",
                "alert"
            )
            .text(
                mensaje || "Ocurrió un error."
            );

        $("#mensajeUsuarios")
            .empty()
            .append($alerta);
    }


    function obtenerMensajeError(solicitud) {

        if (
            solicitud.responseJSON
            && solicitud.responseJSON.mensaje
        ) {
            return solicitud
                .responseJSON
                .mensaje;
        }

        return "Ocurrió un error al procesar la solicitud.";
    }

});