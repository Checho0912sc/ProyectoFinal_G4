document.addEventListener("DOMContentLoaded", function(){

    cargarTarjetas();

    cargarTabla();

});

const indicadores=[

{
titulo:"Proyectos",
valor:12,
icono:"bi-folder-check"
},

{
titulo:"Actividades",
valor:18,
icono:"bi-calendar-event"
},

{
titulo:"Ingresos",
valor:"₡205.000",
icono:"bi-cash-coin"
},

{
titulo:"Usuarios",
valor:45,
icono:"bi-people"
}

];

const reportes=[

{
nombre:"Reporte de proyectos",
descripcion:"Estado de los proyectos comunitarios",
estado:"Disponible"
},

{
nombre:"Reporte financiero",
descripcion:"Ingresos y egresos registrados",
estado:"Disponible"
},

{
nombre:"Reporte de actividades",
descripcion:"Eventos programados",
estado:"Disponible"
}

];

function cargarTarjetas(){

const contenedor=document.getElementById("tarjetasReportes");

indicadores.forEach(function(item){

contenedor.innerHTML+=`

<div class="col-md-3">

<div class="dashboard-resumen-card">

<div class="dashboard-resumen-icono">

<i class="bi ${item.icono}"></i>

</div>

<div>

<h3>${item.valor}</h3>

<h6>${item.titulo}</h6>

</div>

</div>

</div>

`;

});

}

function cargarTabla(){

const tabla=document.getElementById("tablaReportes");

reportes.forEach(function(item){

tabla.innerHTML+=`

<tr>

<td>${item.nombre}</td>

<td>${item.descripcion}</td>

<td><span class="badge text-bg-success">${item.estado}</span></td>

</tr>

`;

});

}