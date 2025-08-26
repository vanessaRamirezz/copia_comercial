let tabla; // variable global

$(document).ready(function () {
    tabla = $('#tblIngresosDiarios').DataTable({
        responsive: true,
        paging: true,
        ordering: true,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
                extend: 'excelHtml5',
                title: 'Reporte de Ingresos diarios',
                messageTop: function () {
                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Reporte de Ingresos diarios',
                messageTop: function () {
                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            },
            {
                extend: 'print',
                title: 'Reporte de Ingresos diarios',
                messageTop: function () {
                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        columns: [
            { data: 'sucursal' },
            { data: 'contado', className: 'text-end' },
            { data: 'itemsContado', className: 'text-center' },
            { data: 'primas', className: 'text-end' },
            { data: 'itemsPrimas', className: 'text-center' },
            { data: 'cuotas', className: 'text-end' },
            { data: 'itemsCuotas', className: 'text-center' },
            { data: 'total', className: 'text-end' }
        ]

    });
});


function buscarReporte() {
    const fechaInicio = document.getElementById('fechaInicio').value.trim();
    const fechaFin = document.getElementById('fechaFin').value.trim();

    if (!fechaInicio || !fechaFin) {
        toastr.error('Por favor, complete todos los campos antes de filtrar el reporte.', 'Error');
        return;
    }

    console.log('Fecha de inicio:', fechaInicio);
    console.log('Fecha fin:', fechaFin);

    $.ajax({
        type: "POST",
        url: baseURL + 'getDataIngresosDiarios',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (respuesta) {
            console.log("Datos recibidos:", respuesta);
            pintarTabla(respuesta);
        },
        error: function (xhr) {
            console.error("Error al obtener datos", xhr.responseText);
        }
    });


}

function validarFechas() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        toastr.error("La fecha fin no puede ser menor que la fecha de inicio.", "Error");
        document.getElementById('fechaFin').value = '';
    }
}


function pintarTabla(datos) {
    let totalContado = 0;
    let totalPrimas = 0;
    let totalCuotas = 0;

    let totalItemsContado = 0;
    let totalItemsPrimas = 0;
    let totalItemsCuotas = 0;

    // Construir array para DataTables
    let datosParaTabla = datos.map(item => {
        const contado = parseFloat(item.Contado || 0);
        const primas = parseFloat(item.Primas || 0);
        const cuotas = parseFloat(item.Cuotas || 0);

        const itemsContado = parseInt(item.ItemsContado || 0);
        const itemsPrimas = parseInt(item.ItemsPrimas || 0);
        const itemsCuotas = parseInt(item.ItemsCuotas || 0);

        totalContado += contado;
        totalPrimas += primas;
        totalCuotas += cuotas;

        totalItemsContado += itemsContado;
        totalItemsPrimas += itemsPrimas;
        totalItemsCuotas += itemsCuotas;

        return {
            sucursal: item.sucursal || '',
            contado: formatMoney(contado.toFixed(2)),
            itemsContado: itemsContado,
            primas: formatMoney(primas.toFixed(2)),
            itemsPrimas: itemsPrimas,
            cuotas: formatMoney(cuotas.toFixed(2)),
            itemsCuotas: itemsCuotas,
            total: formatMoney((contado + primas + cuotas).toFixed(2)),
            itemsTotal: (itemsContado + itemsPrimas + itemsCuotas)
        };
    });

    // Fila de total general
    datosParaTabla.push({
    sucursal: 'TOTAL GENERAL',
    contado: formatMoney(totalContado),
    itemsContado: totalItemsContado,
    primas: formatMoney(totalPrimas),
    itemsPrimas: totalItemsPrimas,
    cuotas: formatMoney(totalCuotas),
    itemsCuotas: totalItemsCuotas,
    total: formatMoney(totalContado + totalPrimas + totalCuotas),
    itemsTotal: (totalItemsContado + totalItemsPrimas + totalItemsCuotas)
});

    // Actualizar DataTables
    tabla.clear();
    tabla.rows.add(datosParaTabla);
    tabla.draw();

    // Actualizar total en footer
    document.getElementById('totalDisponible').textContent =
        (totalContado + totalPrimas + totalCuotas).toFixed(2);
}

function formatMoney(valor) {
    return Number(valor).toLocaleString('es-SV', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}




// Función auxiliar para formatear fecha (opcional)
function formatearFecha(fecha) {
    const f = new Date(fecha);
    return f.toLocaleDateString('es-SV', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}
