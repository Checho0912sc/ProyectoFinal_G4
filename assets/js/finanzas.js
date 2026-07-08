document.addEventListener("DOMContentLoaded", function () {

    cargarResumen();

    cargarMovimientos();

});

const resumen = [

    {
        titulo: "Ingresos",
        valor: "₡205.000",
        icono: "bi-arrow-down-circle"
    },

    {
        titulo: "Egresos",
        valor: "₡75.000",
        icono: "bi-arrow-up-circle"
    },

    {
        titulo: "Saldo",
        valor: "₡130.000",
        icono: "bi-wallet2"
    }

];

const movimientos = [

    {
        fecha: "01/07/2026",
        descripcion: "Donación vecinos",
        tipo: "Ingreso",
        monto: "₡120.000"
    },

    {
        fecha: "03/07/2026",
        descripcion: "Compra de materiales",
        tipo: "Egreso",
        monto: "₡45.000"
    },

    {
        fecha: "05/07/2026",
        descripcion: "Actividad comunitaria",
        tipo: "Ingreso",
        monto: "₡85.000"
    },

    {
        fecha: "07/07/2026",
        descripcion: "Mantenimiento parque",
        tipo: "Egreso",
        monto: "₡30.000"
    }

];

function cargarResumen() {

    const contenedor = document.getElementById("resumenFinanzas");

    resumen.forEach(function(item){

        contenedor.innerHTML += `

        <div class="col-md-4">

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

function cargarMovimientos(){

        const tabla = document.getElementById("tablaMovimientos");

    tabla.innerHTML = "";

    movimientos.forEach(function(item){

        tabla.innerHTML += `

        <tr>
            <td>${item.fecha}</td>
            <td>${item.descripcion}</td>
            <td>
                <span class="badge ${item.tipo=="Ingreso" ? "text-bg-success" : "text-bg-danger"}">
                    ${item.tipo}
                </span>
            </td>
            <td>${item.monto}</td>
        </tr>

        `;

    });

}

function registrarMovimiento(){

    const fecha = document.getElementById("fechaMovimiento").value;

    const descripcion = document.getElementById("descripcionMovimiento").value;

    const tipo = document.getElementById("tipoMovimiento").value;

    const monto = document.getElementById("montoMovimiento").value;

    if(
        fecha === "" ||
        descripcion === "" ||
        monto === ""
    ){
        alert("Complete todos los campos.");
        return;
    }

    movimientos.push({

        fecha: fecha,

        descripcion: descripcion,

        tipo: tipo,

        monto: "₡" + Number(monto).toLocaleString("es-CR")

    });

    actualizarResumen();

    cargarMovimientos();

    limpiarFormulario();

    bootstrap.Modal
        .getInstance(document.getElementById("modalMovimiento"))
        .hide();

}

function limpiarFormulario(){

    document.getElementById("fechaMovimiento").value = "";

    document.getElementById("descripcionMovimiento").value = "";

    document.getElementById("tipoMovimiento").value = "Ingreso";

    document.getElementById("montoMovimiento").value = "";

}

function actualizarResumen(){

    let ingresos = 0;

    let egresos = 0;

    movimientos.forEach(function(item){

        let monto = Number(
            item.monto.replace("₡","").replace(/\./g,"").replace(/,/g,"")
        );

        if(item.tipo=="Ingreso"){

            ingresos += monto;

        }else{

            egresos += monto;

        }

    });

    resumen[0].valor = "₡" + ingresos.toLocaleString("es-CR");

    resumen[1].valor = "₡" + egresos.toLocaleString("es-CR");

    resumen[2].valor = "₡" + (ingresos-egresos).toLocaleString("es-CR");

    document.getElementById("resumenFinanzas").innerHTML = "";

    cargarResumen();

}