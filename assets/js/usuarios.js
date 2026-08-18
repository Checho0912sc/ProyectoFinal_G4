$(function () {

    const $config = $("#configUsuarios");

    const apiUrl =
        $config.data("api-url");

    const csrfToken =
        $config.data("csrf-token");

    cargarRoles();
    cargarUsuarios();

    $("#formUsuario").on(
        "submit",
        function (evento) {

            evento.preventDefault();

            guardarUsuario();

        }
    );

    $("#btnNuevoUsuario").on(
        "click",
        function () {

            limpiarFormulario();

            $("#nombre").trigger("focus");

        }
    );

    $("#btnCancelarEdicion").on(
        "click",
        function () {

            limpiarFormulario();

        }
    );

    $("#buscarUsuario").on(
        "keyup",
        function () {

            const texto =
                $(this)
                    .val()
                    .toLowerCase()
                    .trim();

            $("#tablaUsuarios tr").each(
                function () {

                    const contenido =
                        $(this)
                            .text()
                            .toLowerCase();

                    $(this).toggle(
                        contenido.includes(texto)
                    );

                }
            );

        }
    );

    $("#tablaUsuarios").on(
        "click",
        ".btn-editar",
        function () {

            const idUsuario =
                $(this).data("id");

            cargarUsuario(idUsuario);

        }
    );

    $("#tablaUsuarios").on(
        "click",
        ".btn-eliminar",
        function () {

            const idUsuario =
                $(this).data("id");

            eliminarUsuario(idUsuario);

        }
    );

    function cargarUsuarios() {

        $.ajax({

            url: apiUrl,
            method: "GET",
            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {

                    mostrarMensaje(
                        respuesta.mensaje
                        || "No fue posible cargar los usuarios.",
                        "danger"
                    );

                    return;
                }

                mostrarUsuarios(
                    respuesta.usuarios
                );

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }

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
                    return;
                }

                const $select =
                    $("#idRol");

                $select
                    .find(
                        "option:not(:first)"
                    )
                    .remove();

                respuesta.roles.forEach(
                    function (rol) {

                        $("<option>")
                            .val(rol.id_rol)
                            .text(rol.nombre)
                            .appendTo($select);

                    }
                );

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }

    function mostrarUsuarios(
        usuarios
    ) {

        const $tabla =
            $("#tablaUsuarios");

        $tabla.empty();

        if (usuarios.length === 0) {

            $("<tr>")
                .append(
                    $("<td>")
                        .attr(
                            "colspan",
                            5
                        )
                        .addClass(
                            "text-center text-muted py-4"
                        )
                        .text(
                            "No hay usuarios registrados."
                        )
                )
                .appendTo($tabla);

            return;
        }

        usuarios.forEach(
            function (usuario) {

                const $fila =
                    $("<tr>");

                $("<td>")
                    .text(usuario.nombre)
                    .appendTo($fila);

                $("<td>")
                    .text(usuario.correo)
                    .appendTo($fila);

                $("<td>")
                    .text(usuario.rol)
                    .appendTo($fila);

                const claseEstado =
                    usuario.estado ===
                    "Activo"
                        ? "text-bg-success"
                        : "text-bg-secondary";

                const $badge =
                    $("<span>")
                        .addClass(
                            "badge "
                            + claseEstado
                        )
                        .text(
                            usuario.estado
                        );

                $("<td>")
                    .append($badge)
                    .appendTo($fila);

                const $acciones =
                    $("<td>");

                $("<button>")
                    .attr(
                        "type",
                        "button"
                    )
                    .addClass(
                        "btn btn-sm btn-outline-primary me-2 btn-editar"
                    )
                    .data(
                        "id",
                        usuario.id_usuario
                    )
                    .attr(
                        "title",
                        "Editar"
                    )
                    .html(
                        '<i class="bi bi-pencil"></i>'
                    )
                    .appendTo(
                        $acciones
                    );

                const usuarioActivo =
                    usuario.estado === "Activo";

                $("<button>")
                    .attr(
                        "type",
                        "button"
                    )
                    .addClass(
                        "btn btn-sm btn-outline-danger btn-eliminar"
                    )
                    .data(
                        "id",
                        usuario.id_usuario
                    )
                    .attr(
                        "title",
                        usuarioActivo
                            ? "Desactivar usuario"
                            : "Usuario inactivo"
                    )
                    .attr(
                        "aria-label",
                        usuarioActivo
                            ? "Desactivar usuario"
                            : "Usuario inactivo"
                    )
                    .prop(
                        "disabled",
                        !usuarioActivo
                    )
                    .html(
                        '<i class="bi bi-person-dash"></i>'
                    )
                    .appendTo(
                        $acciones
                    );

                $acciones.appendTo(
                    $fila
                );

                $fila.appendTo(
                    $tabla
                );

            }
        );

    }

    function cargarUsuario(
        idUsuario
    ) {

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
                        respuesta.mensaje,
                        "danger"
                    );

                    return;
                }

                const usuario =
                    respuesta.usuario;

                $("#idUsuario")
                    .val(
                        usuario.id_usuario
                    );

                $("#nombre")
                    .val(
                        usuario.nombre
                    );

                $("#correo")
                    .val(
                        usuario.correo
                    );

                $("#telefono")
                    .val(
                        usuario.telefono
                        || ""
                    );

                $("#idRol")
                    .val(
                        usuario.id_rol
                    );

                $("#estado")
                    .val(
                        usuario.estado
                    );

                $("#contrasena")
                    .val("");

                $(
                    "#nombre, #correo, #telefono, #contrasena"
                ).prop(
                    "disabled",
                    true
                );

                $("#tituloFormulario")
                    .text(
                        "Editar membresía"
                    );

                $("#textoFormulario")
                    .text(
                        "Modifique el rol y el estado del usuario en esta comunidad."
                    );

                $("#ayudaContrasena")
                    .text(
                        "Los datos de la cuenta no se modifican desde este módulo."
                    );

                $("#btnGuardarUsuario")
                    .html(
                        '<i class="bi bi-save"></i> Actualizar membresía'
                    );

                $("#btnCancelarEdicion")
                    .removeClass(
                        "d-none"
                    );

                limpiarErrores();

                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }

    function guardarUsuario() {

        limpiarErrores();

        if (!validarFormulario()) {
            return;
        }

        const idUsuario =
            $("#idUsuario").val();

        const esEdicion =
            idUsuario !== "";

        const datos = esEdicion
            ? {
                id_rol:
                    parseInt(
                        $("#idRol").val(),
                        10
                    ),

                estado:
                    $("#estado").val()
            }
            : {
                nombre:
                    $("#nombre")
                        .val()
                        .trim(),

                correo:
                    $("#correo")
                        .val()
                        .trim(),

                telefono:
                    $("#telefono")
                        .val()
                        .trim(),

                contrasena:
                    $("#contrasena")
                        .val(),

                id_rol:
                    parseInt(
                        $("#idRol").val(),
                        10
                    ),

                estado:
                    $("#estado").val()
            };

        const url =
            esEdicion
                ? apiUrl
                    + "?id="
                    + encodeURIComponent(
                        idUsuario
                    )
                : apiUrl;

        const metodo =
            esEdicion
                ? "PUT"
                : "POST";

        $("#btnGuardarUsuario")
            .prop(
                "disabled",
                true
            );

        $.ajax({

            url: url,

            method: metodo,

            contentType:
                "application/json; charset=utf-8",

            dataType: "json",

            headers: {
                "X-CSRF-Token":
                    csrfToken
            },

            data:
                JSON.stringify(
                    datos
                ),

            success: function (
                respuesta
            ) {

                mostrarMensaje(
                    respuesta.mensaje,
                    "success"
                );

                limpiarFormulario();

                cargarUsuarios();

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            },

            complete: function () {

                $("#btnGuardarUsuario")
                    .prop(
                        "disabled",
                        false
                    );

            }

        });

    }

    function eliminarUsuario(
        idUsuario
    ) {

        const confirmar =
            window.confirm(
                "¿Desea desactivar este usuario de la comunidad?"
            );

        if (!confirmar) {
            return;
        }

        $.ajax({

            url:
                apiUrl
                + "?id="
                + encodeURIComponent(
                    idUsuario
                ),

            method: "DELETE",

            dataType: "json",

            headers: {
                "X-CSRF-Token":
                    csrfToken
            },

            success: function (
                respuesta
            ) {

                mostrarMensaje(
                    respuesta.mensaje,
                    "success"
                );

                cargarUsuarios();

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }

    function validarFormulario() {

        let valido = true;

        const nombre =
            $("#nombre")
                .val()
                .trim();

        const correo =
            $("#correo")
                .val()
                .trim();

        const contrasena =
            $("#contrasena")
                .val();

        const idRol =
            $("#idRol").val();

        const idUsuario =
            $("#idUsuario").val();

        if (nombre.length < 3) {

            $("#errorNombre")
                .text(
                    "Ingrese un nombre de al menos 3 caracteres."
                );

            $("#nombre")
                .addClass(
                    "is-invalid"
                );

            valido = false;

        }

        if (
            correo === ""
            || !correoValido(
                correo
            )
        ) {

            $("#errorCorreo")
                .text(
                    "Ingrese un correo electrónico válido."
                );

            $("#correo")
                .addClass(
                    "is-invalid"
                );

            valido = false;

        }

        if (
            idUsuario === ""
            && contrasena.length < 6
        ) {

            $("#errorContrasena")
                .text(
                    "La contraseña debe tener al menos 6 caracteres."
                );

            $("#contrasena")
                .addClass(
                    "is-invalid"
                );

            valido = false;

        }

        if (
            idUsuario !== ""
            && contrasena !== ""
            && contrasena.length < 6
        ) {

            $("#errorContrasena")
                .text(
                    "La nueva contraseña debe tener al menos 6 caracteres."
                );

            $("#contrasena")
                .addClass(
                    "is-invalid"
                );

            valido = false;

        }

        if (!idRol) {

            $("#errorRol")
                .text(
                    "Seleccione un rol."
                );

            $("#idRol")
                .addClass(
                    "is-invalid"
                );

            valido = false;

        }

        return valido;

    }

    function correoValido(
        correo
    ) {

        const expresion =
            /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        return expresion.test(
            correo
        );

    }

    function limpiarFormulario() {

        $("#formUsuario")[0]
            .reset();

        $("#idUsuario")
            .val("");

        $(
            "#nombre, #correo, #telefono, #contrasena"
        ).prop(
            "disabled",
            false
        );

        $("#estado")
            .val("Activo");

        $("#tituloFormulario")
            .text(
                "Registrar usuario"
            );

        $("#textoFormulario")
            .text(
                "Complete los datos del nuevo miembro."
            );

        $("#ayudaContrasena")
            .text(
                "Es obligatoria al crear un usuario."
            );

        $("#btnGuardarUsuario")
            .html(
                '<i class="bi bi-save"></i> Guardar usuario'
            );

        $("#btnCancelarEdicion")
            .addClass(
                "d-none"
            );

        limpiarErrores();

    }

    function limpiarErrores() {

        $(
            "#errorNombre, "
            + "#errorCorreo, "
            + "#errorContrasena, "
            + "#errorRol"
        ).text("");

        $(
            "#nombre, "
            + "#correo, "
            + "#contrasena, "
            + "#idRol"
        ).removeClass(
            "is-invalid"
        );

    }

    function mostrarMensaje(
        mensaje,
        tipo
    ) {

        const $alerta =
            $("<div>")
                .addClass(
                    "alert alert-"
                    + tipo
                    + " alert-dismissible fade show"
                )
                .attr(
                    "role",
                    "alert"
                )
                .text(mensaje);

        $("<button>")
            .attr({
                type: "button",
                "data-bs-dismiss":
                    "alert",
                "aria-label":
                    "Cerrar"
            })
            .addClass(
                "btn-close"
            )
            .appendTo(
                $alerta
            );

        $("#mensajeUsuarios")
            .empty()
            .append(
                $alerta
            );

    }

    function mostrarErrorAjax(
        xhr
    ) {

        let mensaje =
            "Ocurrió un error al procesar la solicitud.";

        if (
            xhr.responseJSON
            && xhr.responseJSON.mensaje
        ) {

            mensaje =
                xhr.responseJSON.mensaje;

        }

        mostrarMensaje(
            mensaje,
            "danger"
        );

    }

});