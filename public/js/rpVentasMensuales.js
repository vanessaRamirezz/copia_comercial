let tabla; // variable global
$(document).ready(function () {
    tabla = $('#tablaVentasMensuales').DataTable({
        responsive: true,
        paging: true,
        ordering: false,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
                extend: 'excelHtml5',
                title: 'Rp-Ventas-mensuales',
                messageTop: 'Reporte de Ventas Mensuales'
            },
            'pdf',
            'print'
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        columns: [
            { data: 'duiUsuario' },
            { data: 'tipo' },
            {
                data: 'fecha',
                render: function (data, type, row) {
                    if (!data) return '';  // Si es vacío o null, devuelve vacío
                    const f = new Date(data);
                    if (isNaN(f.getTime())) return ''; // Si no es fecha válida, vacío
                    return f.toLocaleDateString('es-SV', { day: '2-digit', month: '2-digit', year: 'numeric' });
                }
            },
            { data: 'cliente' },
            { data: 'documento' },
            { data: 'monto', className: 'text-end', render: $.fn.dataTable.render.number(',', '.', 2) }
        ],
        createdRow: function(row, data, dataIndex) {
            if (data.claseFila === 'fila-total') {
                $(row).addClass('fila-total');
            }
        }

    });
});




function buscarReporte() {
    const sucursal = document.getElementById('filtroSucursal').value.trim();
    const fechaInicio = document.getElementById('fechaInicio').value.trim();
    const fechaFin = document.getElementById('fechaFin').value.trim();

    if (!sucursal || !fechaInicio || !fechaFin) {
        toastr.error('Por favor, complete todos los campos antes de filtrar el reporte.', 'Error');
        return;
    }

    console.log('Sucursal:', sucursal);
    console.log('Fecha de inicio:', fechaInicio);
    console.log('Fecha fin:', fechaFin);

    $.ajax({
        type: "POST",
        url: baseURL + 'getVentasMensuales',
        data: {
            fecha_inicio: fechaInicio,
            fecha_fin: fechaFin,
            sucursal: sucursal
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

    // Filtrar los datos por tipo
    const creditos = datos.filter(item => item.tipo === 'CRÉDITO');
    const contados = datos.filter(item => item.tipo === 'CONTADO');

    // Calcular totales
    totalCredito = creditos.reduce((acc, cur) => acc + parseFloat(cur.monto), 0);
    totalContado = contados.reduce((acc, cur) => acc + parseFloat(cur.monto), 0);

    // Construir array para DataTables
    let datosParaTabla = [];

    // Agregar CONTADOS primero
    datosParaTabla = datosParaTabla.concat(contados);

    if (contados.length > 0) {
        datosParaTabla.push({
            tipo: 'TOTAL CONTADO',
            fecha: '',
            cliente: '',
            documento: '',
            monto: totalContado.toFixed(2),
            duiUsuario: '',
            claseFila: 'fila-total'
        });
    }

    // Luego agregar CRÉDITOS
    datosParaTabla = datosParaTabla.concat(creditos);

    if (creditos.length > 0) {
        datosParaTabla.push({
            tipo: 'TOTAL CRÉDITO',
            fecha: '',
            cliente: '',
            documento: '',
            monto: totalCredito.toFixed(2),
            duiUsuario: '',
            claseFila: 'fila-total'
        });
    }

    // Total general
    const totalGeneral = totalCredito + totalContado;
    document.getElementById('totalDisponible').textContent = totalGeneral.toFixed(2);

    // Cargar datos en la tabla
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
