$(function () {

    const $buscar =
        $("#buscarComunidad");

    const $comunidades =
        $(".comunidad-item");

    const $sinResultados =
        $("#sinResultados");

    $buscar.on(
        "input",
        function () {

            const texto = String(
                $(this).val()
            )
                .toLowerCase()
                .trim();

            let cantidadVisible = 0;

            $comunidades.each(
                function () {

                    const datos = String(
                        $(this).data(
                            "busqueda"
                        )
                    ).toLowerCase();

                    const mostrar =
                        datos.includes(texto);

                    $(this).toggle(mostrar);

                    if (mostrar) {
                        cantidadVisible++;
                    }

                }
            );

            $sinResultados.toggleClass(
                "d-none",
                cantidadVisible > 0
            );

        }
    );

});