$(function () {

    const $config = $("#configProyectos");

    const apiUrl = $config.data("api-url");
    const csrfToken = $config.data("csrf-token");

    cargarGrupos();
    cargarResponsables();
    cargarProyectos();


    // ==========================================
    // EVENTOS
    // ==========================================

    $("#formProyecto").on("submit", function (evento) {

        evento.preventDefault();

        guardarProyecto();

    });


    $("#btnNuevoProyecto").on("click", function () {

        limpiarFormulario();

        $("#nombreProyecto").trigger("focus");

    });


    $("#btnCancelarEdicionProyecto").on("click", function () {

        limpiarFormulario();

    });


    $("#buscarProyecto").on("keyup", function () {

        const texto = $(this)
            .val()
            .toLowerCase()
            .trim();

        $("#tablaProyectos tr").each(function () {

            const contenido = $(this)
                .text()
                .toLowerCase();

            $(this).toggle(
                contenido.includes(texto)
            );

        });

    });


    $("#tablaProyectos").on(
        "click",
        ".btn-editar-proyecto",
        function () {

            const idProyecto = $(this).data("id");

            cargarProyecto(idProyecto);

        }
    );


    $("#tablaProyectos").on(
        "click",
        ".btn-eliminar-proyecto",
        function () {

            const idProyecto = $(this).data("id");

            eliminarProyecto(idProyecto);

        }
    );


    // ==========================================
    // CARGAR PROYECTOS
    // ==========================================

    function cargarProyectos() {

        $.ajax({

            url: apiUrl,
            method: "GET",
            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {

                    mostrarMensaje(
                        respuesta.mensaje ||
                        "No fue posible cargar los proyectos.",
                        "danger"
                    );

                    return;

                }

                mostrarProyectos(
                    respuesta.proyectos
                );

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }


    // ==========================================
    // MOSTRAR PROYECTOS
    // ==========================================

    function mostrarProyectos(proyectos) {

        const $tabla = $("#tablaProyectos");

        $tabla.empty();

        if (proyectos.length === 0) {

            $("<tr>")
                .append(
                    $("<td>")
                        .attr("colspan", 7)
                        .addClass(
                            "text-center text-muted py-4"
                        )
                        .text(
                            "No hay proyectos registrados."
                        )
                )
                .appendTo($tabla);

            return;

        }


        proyectos.forEach(function (proyecto) {

            const $fila = $("<tr>");


            $("<td>")
                .text(proyecto.nombre)
                .appendTo($fila);


            $("<td>")
                .text(proyecto.grupo)
                .appendTo($fila);


            $("<td>")
                .text(proyecto.responsable)
                .appendTo($fila);


            $("<td>")
                .text(proyecto.fecha_inicio)
                .appendTo($fila);


            $("<td>")
                .text(
                    formatearMoneda(
                        proyecto.presupuesto
                    )
                )
                .appendTo($fila);


            const $badge = $("<span>")
                .addClass(
                    "badge " +
                    claseEstado(proyecto.estado)
                )
                .text(proyecto.estado);


            $("<td>")
                .append($badge)
                .appendTo($fila);


            const $acciones = $("<td>");


            $("<button>")
                .attr("type", "button")
                .addClass(
                    "btn btn-sm btn-outline-primary me-2 btn-editar-proyecto"
                )
                .data(
                    "id",
                    proyecto.id_proyecto
                )
                .attr(
                    "title",
                    "Editar"
                )
                .html(
                    '<i class="bi bi-pencil"></i>'
                )
                .appendTo($acciones);


            if (proyecto.estado !== "Cancelado") {

                $("<button>")
                    .attr("type", "button")
                    .addClass(
                        "btn btn-sm btn-outline-danger btn-eliminar-proyecto"
                    )
                    .data(
                        "id",
                        proyecto.id_proyecto
                    )
                    .attr(
                        "title",
                        "Cancelar proyecto"
                    )
                    .html(
                        '<i class="bi bi-x-circle"></i>'
                    )
                    .appendTo($acciones);

            }


            $acciones.appendTo($fila);

            $fila.appendTo($tabla);

        });

    }


    // ==========================================
    // CARGAR UN PROYECTO PARA EDITAR
    // ==========================================

    function cargarProyecto(idProyecto) {

        $.ajax({

            url: apiUrl,

            method: "GET",

            data: {
                id: idProyecto
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


                const proyecto = respuesta.proyecto;


                $("#idProyecto").val(
                    proyecto.id_proyecto
                );


                $("#nombreProyecto").val(
                    proyecto.nombre
                );


                $("#idGrupo").val(
                    proyecto.id_grupo
                );


                $("#idResponsable").val(
                    proyecto.id_responsable
                );


                $("#descripcionProyecto").val(
                    proyecto.descripcion || ""
                );


                $("#fechaInicio").val(
                    proyecto.fecha_inicio
                );


                $("#fechaFin").val(
                    proyecto.fecha_fin || ""
                );


                $("#estadoProyecto").val(
                    proyecto.estado
                );


                $("#presupuesto").val(
                    proyecto.presupuesto
                );


                $("#tituloFormularioProyecto").text(
                    "Editar proyecto"
                );


                $("#textoFormularioProyecto").text(
                    "Modifique la información del proyecto seleccionado."
                );


                $("#btnGuardarProyecto").html(
                    '<i class="bi bi-save"></i> Actualizar proyecto'
                );


                $("#btnCancelarEdicionProyecto")
                    .removeClass("d-none");


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


    // ==========================================
    // CARGAR GRUPOS
    // ==========================================

    function cargarGrupos() {

        $.ajax({

            url: apiUrl,

            method: "GET",

            data: {
                recurso: "grupos"
            },

            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {
                    return;
                }


                const $select = $("#idGrupo");


                $select
                    .find("option:not(:first)")
                    .remove();


                respuesta.grupos.forEach(
                    function (grupo) {

                        $("<option>")
                            .val(grupo.id_grupo)
                            .text(grupo.nombre)
                            .appendTo($select);

                    }
                );

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }


    // ==========================================
    // CARGAR RESPONSABLES
    // ==========================================

    function cargarResponsables() {

        $.ajax({

            url: apiUrl,

            method: "GET",

            data: {
                recurso: "responsables"
            },

            dataType: "json",

            success: function (respuesta) {

                if (!respuesta.exito) {
                    return;
                }


                const $select =
                    $("#idResponsable");


                $select
                    .find("option:not(:first)")
                    .remove();


                respuesta.responsables.forEach(
                    function (usuario) {

                        $("<option>")
                            .val(
                                usuario.id_usuario
                            )
                            .text(
                                usuario.nombre
                            )
                            .appendTo($select);

                    }
                );

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }


    // ==========================================
    // GUARDAR / ACTUALIZAR
    // ==========================================

    function guardarProyecto() {

        limpiarErrores();


        if (!validarFormulario()) {
            return;
        }


        const idProyecto =
            $("#idProyecto").val();


        const datos = {

            id_grupo:
                parseInt(
                    $("#idGrupo").val(),
                    10
                ),

            id_responsable:
                parseInt(
                    $("#idResponsable").val(),
                    10
                ),

            nombre:
                $("#nombreProyecto")
                    .val()
                    .trim(),

            descripcion:
                $("#descripcionProyecto")
                    .val()
                    .trim(),

            fecha_inicio:
                $("#fechaInicio").val(),

            fecha_fin:
                $("#fechaFin").val(),

            estado:
                $("#estadoProyecto").val(),

            presupuesto:
                parseFloat(
                    $("#presupuesto").val()
                )

        };


        const esEdicion =
            idProyecto !== "";


        const url = esEdicion
            ? apiUrl +
                "?id=" +
                encodeURIComponent(idProyecto)
            : apiUrl;


        const metodo = esEdicion
            ? "PUT"
            : "POST";


        $("#btnGuardarProyecto")
            .prop("disabled", true);


        $.ajax({

            url: url,

            method: metodo,

            contentType:
                "application/json; charset=utf-8",

            dataType: "json",

            headers: {
                "X-CSRF-Token": csrfToken
            },

            data: JSON.stringify(datos),

            success: function (respuesta) {

                mostrarMensaje(
                    respuesta.mensaje,
                    "success"
                );


                limpiarFormulario();

                cargarProyectos();

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            },

            complete: function () {

                $("#btnGuardarProyecto")
                    .prop(
                        "disabled",
                        false
                    );

            }

        });

    }


    // ==========================================
    // CANCELAR PROYECTO
    // ==========================================

    function eliminarProyecto(idProyecto) {

        const confirmar = window.confirm(
            "¿Desea cancelar este proyecto?"
        );


        if (!confirmar) {
            return;
        }


        $.ajax({

            url:
                apiUrl +
                "?id=" +
                encodeURIComponent(idProyecto),

            method: "DELETE",

            dataType: "json",

            headers: {
                "X-CSRF-Token": csrfToken
            },

            success: function (respuesta) {

                mostrarMensaje(
                    respuesta.mensaje,
                    "success"
                );

                cargarProyectos();

            },

            error: function (xhr) {

                mostrarErrorAjax(xhr);

            }

        });

    }


    // ==========================================
    // VALIDACIONES FRONTEND
    // ==========================================

    function validarFormulario() {

        let valido = true;


        const nombre =
            $("#nombreProyecto")
                .val()
                .trim();


        const idGrupo =
            $("#idGrupo").val();


        const idResponsable =
            $("#idResponsable").val();


        const fechaInicio =
            $("#fechaInicio").val();


        const fechaFin =
            $("#fechaFin").val();


        const presupuesto =
            parseFloat(
                $("#presupuesto").val()
            );


        if (nombre.length < 3) {

            $("#errorNombreProyecto").text(
                "Ingrese un nombre de al menos 3 caracteres."
            );

            $("#nombreProyecto")
                .addClass("is-invalid");

            valido = false;

        }


        if (!idGrupo) {

            $("#errorGrupo").text(
                "Seleccione un grupo."
            );

            $("#idGrupo")
                .addClass("is-invalid");

            valido = false;

        }


        if (!idResponsable) {

            $("#errorResponsable").text(
                "Seleccione un responsable."
            );

            $("#idResponsable")
                .addClass("is-invalid");

            valido = false;

        }


        if (!fechaInicio) {

            $("#errorFechaInicio").text(
                "Seleccione la fecha de inicio."
            );

            $("#fechaInicio")
                .addClass("is-invalid");

            valido = false;

        }


        if (
            fechaInicio &&
            fechaFin &&
            fechaFin < fechaInicio
        ) {

            $("#errorFechaFin").text(
                "La fecha final no puede ser anterior a la fecha de inicio."
            );

            $("#fechaFin")
                .addClass("is-invalid");

            valido = false;

        }


        if (
            Number.isNaN(presupuesto) ||
            presupuesto < 0
        ) {

            $("#errorPresupuesto").text(
                "El presupuesto debe ser igual o mayor a cero."
            );

            $("#presupuesto")
                .addClass("is-invalid");

            valido = false;

        }


        return valido;

    }


    // ==========================================
    // LIMPIAR FORMULARIO
    // ==========================================

    function limpiarFormulario() {

        $("#formProyecto")[0].reset();


        $("#idProyecto").val("");


        $("#estadoProyecto").val(
            "Planificado"
        );


        $("#presupuesto").val(0);


        $("#tituloFormularioProyecto").text(
            "Registrar proyecto"
        );


        $("#textoFormularioProyecto").text(
            "Complete la información del proyecto."
        );


        $("#btnGuardarProyecto").html(
            '<i class="bi bi-save"></i> Guardar proyecto'
        );


        $("#btnCancelarEdicionProyecto")
            .addClass("d-none");


        limpiarErrores();

    }


    // ==========================================
    // LIMPIAR ERRORES
    // ==========================================

    function limpiarErrores() {

        $(
            "#errorNombreProyecto, " +
            "#errorGrupo, " +
            "#errorResponsable, " +
            "#errorFechaInicio, " +
            "#errorFechaFin, " +
            "#errorPresupuesto"
        ).text("");


        $(
            "#nombreProyecto, " +
            "#idGrupo, " +
            "#idResponsable, " +
            "#fechaInicio, " +
            "#fechaFin, " +
            "#presupuesto"
        ).removeClass("is-invalid");

    }


    // ==========================================
    // BADGE SEGÚN ESTADO
    // ==========================================

    function claseEstado(estado) {

        switch (estado) {

            case "Planificado":
                return "text-bg-primary";

            case "En proceso":
                return "text-bg-warning";

            case "Pausado":
                return "text-bg-secondary";

            case "Finalizado":
                return "text-bg-success";

            case "Cancelado":
                return "text-bg-danger";

            default:
                return "text-bg-secondary";

        }

    }


    // ==========================================
    // FORMATO MONEDA
    // ==========================================

    function formatearMoneda(valor) {

        const numero =
            Number(valor);


        if (Number.isNaN(numero)) {
            return "₡0";
        }


        return new Intl.NumberFormat(
            "es-CR",
            {
                style: "currency",
                currency: "CRC"
            }
        ).format(numero);

    }


    // ==========================================
    // MENSAJES
    // ==========================================

    function mostrarMensaje(
        mensaje,
        tipo
    ) {

        const $alerta = $("<div>")
            .addClass(
                "alert alert-" +
                tipo +
                " alert-dismissible fade show"
            )
            .attr(
                "role",
                "alert"
            )
            .text(mensaje);


        $("<button>")
            .attr({
                type: "button",
                "data-bs-dismiss": "alert",
                "aria-label": "Cerrar"
            })
            .addClass("btn-close")
            .appendTo($alerta);


        $("#mensajeProyectos")
            .empty()
            .append($alerta);

    }


    function mostrarErrorAjax(xhr) {

        let mensaje =
            "Ocurrió un error al procesar la solicitud.";


        if (
            xhr.responseJSON &&
            xhr.responseJSON.mensaje
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