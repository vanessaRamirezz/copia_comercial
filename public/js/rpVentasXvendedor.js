let tabla; // variable global

$(document).ready(function () {
    tabla = $('#tblVentasXvendedor').DataTable({
        responsive: true,
        paging: true,
        ordering: true,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
                extend: 'excelHtml5',
                title: 'Reporte de Ventas por Vendedor',
                messageTop: function () {
                    const vendedorSelect = document.getElementById('usuario');
                    const vendedorNombre = vendedorSelect.options[vendedorSelect.selectedIndex]?.text || 'Todos';

                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Vendedor: ${vendedorNombre} | Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            },
            {
                extend: 'pdfHtml5',
                title: 'Reporte de Ventas por Vendedor',
                messageTop: function () {
                    const vendedorSelect = document.getElementById('usuario');
                    const vendedorNombre = vendedorSelect.options[vendedorSelect.selectedIndex]?.text || 'Todos';

                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Vendedor: ${vendedorNombre} | Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            },
            {
                extend: 'print',
                title: 'Reporte de Ventas por Vendedor',
                messageTop: function () {
                    const vendedorSelect = document.getElementById('usuario');
                    const vendedorNombre = vendedorSelect.options[vendedorSelect.selectedIndex]?.text || 'Todos';

                    const fechaInicio = document.getElementById('fechaInicio').value;
                    const fechaFin = document.getElementById('fechaFin').value;

                    return `Filtros: Vendedor: ${vendedorNombre} | Desde: ${fechaInicio} - Hasta: ${fechaFin}`;
                }
            }
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        columns: [
            { data: 'tipo' },
            { data: 'sucursal' },
            {
                data: 'fecha',
                render: function (data, type, row) {
                    if (!data) return '';
                    const f = new Date(data);
                    if (isNaN(f.getTime())) return '';
                    return f.toLocaleDateString('es-SV', { day: '2-digit', month: '2-digit', year: 'numeric' });
                }
            },
            { data: 'documento' },
            { data: 'cliente' },
            { data: 'monto', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2) }
        ]
    });
});


function buscarReporte() {
    const vendedor = document.getElementById('usuario').value.trim();
    const fechaInicio = document.getElementById('fechaInicio').value.trim();
    const fechaFin = document.getElementById('fechaFin').value.trim();

    if (!vendedor || !fechaInicio || !fechaFin) {
        toastr.error('Por favor, complete todos los campos antes de filtrar el reporte.', 'Error');
        return;
    }

    console.log('Sucursal:', vendedor);
    console.log('Fecha de inicio:', fechaInicio);
    console.log('Fecha fin:', fechaFin);

    $.ajax({
        type: "POST",
        url: baseURL + 'getVentasXvendedor',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            vendedor: vendedor
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
    let totalCredito = 0;
    let totalContado = 0;

    const creditos = datos.filter(item => item.tipo === 'CRÉDITO');
    const contados = datos.filter(item => item.tipo === 'CONTADO');

    totalCredito = creditos.reduce((acc, cur) => acc + parseFloat(cur.monto), 0);
    totalContado = contados.reduce((acc, cur) => acc + parseFloat(cur.monto), 0);

    // Construir array con datos para DataTables (incluye filas de totales)
    let datosParaTabla = [];

    datosParaTabla = datosParaTabla.concat(creditos);

    // Fila total crédito como objeto especial
    if (creditos.length > 0) {
    datosParaTabla.push({
        tipo: 'TOTAL CRÉDITO',
        sucursal: '',
        fecha: '',
        cliente: '',
        documento: '',
        monto: totalCredito.toFixed(2)
    });
}

    datosParaTabla = datosParaTabla.concat(contados);

    // Fila total contado como objeto especial
    if (contados.length > 0) {
    datosParaTabla.push({
        tipo: 'TOTAL CONTADO',
        sucursal: '',
        fecha: '',
        cliente: '',
        documento: '',
        monto: totalContado.toFixed(2)
    });
}

    // Actualizar total general en footer
    const totalGeneral = totalCredito + totalContado;
    document.getElementById('totalDisponible').textContent = totalGeneral.toFixed(2);

    // Actualizar DataTables con los nuevos datos
    tabla.clear();
    tabla.rows.add(datosParaTabla);
    tabla.draw();
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
