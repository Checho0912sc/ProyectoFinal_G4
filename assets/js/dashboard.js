document.addEventListener("DOMContentLoaded", function () {
    cargarResumen();
    cargarProyectos();
    cargarActividades();
    cargarMovimientos();
});

// Datos quemados para el dashboard
const resumenDashboard = [
    {
        titulo: "Proyectos activos",
        valor: 12,
        icono: "bi-folder-check",
        texto: "Proyectos en desarrollo"
    },
    {
        titulo: "Actividades próximas",
        valor: 8,
        icono: "bi-calendar-event",
        texto: "Actividades programadas"
    },
    {
        titulo: "Usuarios registrados",
        valor: 45,
        icono: "bi-people",
        texto: "Miembros de la comunidad"
    },
    {
        titulo: "Fondos disponibles",
        valor: "₡250K",
        icono: "bi-cash-coin",
        texto: "Saldo comunitario"
    }
];

const proyectos = [
    {
        nombre: "Mejora del parque comunal",
        responsable: "Ana Rodriguez",
        estado: "Activo",
        avance: 75
    },
    {
        nombre: "Campaña de reciclaje",
        responsable: "Carlos Mendez",
        estado: "En proceso",
        avance: 50
    },
    {
        nombre: "Iluminacion de zonas comunes",
        responsable: "Maria Solis",
        estado: "Planificado",
        avance: 25
    },
    {
        nombre: "Taller de tecnologia",
        responsable: "Luis Fernandez",
        estado: "Activo",
        avance: 60
    }
];

const actividades = [
    {
        titulo: "Reunion de coordinadores",
        fecha: "12 julio",
        hora: "6:00 p.m.",
        icono: "bi-people"
    },
    {
        titulo: "Jornada de limpieza",
        fecha: "15 julio",
        hora: "8:00 a.m.",
        icono: "bi-trash3"
    },
    {
        titulo: "Taller comunitario",
        fecha: "18 julio",
        hora: "5:30 p.m.",
        icono: "bi-easel"
    }
];

const movimientos = [
    {
        descripcion: "Donacion vecinal",
        tipo: "Ingreso",
        monto: "₡120.000"
    },
    {
        descripcion: "Compra de materiales",
        tipo: "Egreso",
        monto: "₡45.000"
    },
    {
        descripcion: "Actividad de recaudacion",
        tipo: "Ingreso",
        monto: "₡85.000"
    },
    {
        descripcion: "Mantenimiento del parque",
        tipo: "Egreso",
        monto: "₡30.000"
    }
];

function cargarResumen() {
    const contenedor = document.getElementById("resumenDashboard");

    resumenDashboard.forEach(function (item) {
        contenedor.innerHTML += `
            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-resumen-card">
                    <div class="dashboard-resumen-icono">
                        <i class="bi ${item.icono}"></i>
                    </div>

                    <div>
                        <h3>${item.valor}</h3>
                        <h6>${item.titulo}</h6>
                        <p>${item.texto}</p>
                    </div>
                </div>
            </div>
        `;
    });
}

function cargarProyectos() {
    const tabla = document.getElementById("tablaProyectos");

    proyectos.forEach(function (proyecto) {
        tabla.innerHTML += `
            <tr>
                <td>${proyecto.nombre}</td>
                <td>${proyecto.responsable}</td>
                <td>
                    <span class="badge ${obtenerClaseEstado(proyecto.estado)}">
                        ${proyecto.estado}
                    </span>
                </td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1">
                            <div class="progress-bar bg-success" style="width: ${proyecto.avance}%"></div>
                        </div>
                        <span>${proyecto.avance}%</span>
                    </div>
                </td>
            </tr>
        `;
    });
}

function cargarActividades() {
    const lista = document.getElementById("listaActividades");

    actividades.forEach(function (actividad) {
        lista.innerHTML += `
            <div class="dashboard-lista-item">
                <div class="dashboard-lista-icono">
                    <i class="bi ${actividad.icono}"></i>
                </div>

                <div>
                    <h6>${actividad.titulo}</h6>
                    <p>${actividad.fecha} - ${actividad.hora}</p>
                </div>
            </div>
        `;
    });
}

function cargarMovimientos() {
    const lista = document.getElementById("listaMovimientos");

    movimientos.forEach(function (movimiento) {
        lista.innerHTML += `
            <div class="dashboard-movimiento">
                <div>
                    <h6>${movimiento.descripcion}</h6>
                    <p>${movimiento.tipo}</p>
                </div>

                <span class="${movimiento.tipo === "Ingreso" ? "movimiento-ingreso" : "movimiento-egreso"}">
                    ${movimiento.monto}
                </span>
            </div>
        `;
    });
}

function obtenerClaseEstado(estado) {
    if (estado === "Activo") {
        return "text-bg-success";
    }

    if (estado === "En proceso") {
        return "text-bg-warning";
    }

    return "text-bg-primary";
}