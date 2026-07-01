document.addEventListener("DOMContentLoaded", function () {
    cargarComponente("navbar", "layout/navbar.html");
    cargarComponente("footer", "layout/footer.html");
});

function cargarComponente(idElemento, rutaArchivo) {
    fetch(rutaArchivo)
        .then(function (respuesta) {
            if (!respuesta.ok) {
                throw new Error("No se pudo cargar " + rutaArchivo);
            }

            return respuesta.text();
        })
        .then(function (contenido) {
            document.getElementById(idElemento).innerHTML = contenido;

            // Marca la pagina actual en el menu
            marcarPaginaActiva();
        })
        .catch(function (error) {
            console.log("Error cargando el layout:", error);
        });
}

function marcarPaginaActiva() {
    const paginaActual = window.location.pathname.split("/").pop() || "index.html";
    const enlacesMenu = document.querySelectorAll("#navbar .nav-link");

    enlacesMenu.forEach(function (enlace) {
        const rutaEnlace = enlace.getAttribute("href");

        if (rutaEnlace === paginaActual) {
            enlace.classList.add("active");
        }
    });
}