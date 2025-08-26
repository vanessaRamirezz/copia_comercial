let tabla; // variable global
$(document).ready(function () {
    tabla = $('#tblMovimientosInventario').DataTable({
        responsive: true,
        paging: true,
        ordering: true,
        searching: false,
        dom: 'Bfrtip',
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        }
    });
});




function buscarReporte() {
    //const sucursal = document.getElementById('filtroSucursal').value.trim();
    const fechaInicio = document.getElementById('fechaInicio').value.trim();
    const fechaFin = document.getElementById('fechaFin').value.trim();

    if (!fechaInicio || !fechaFin) {
        toastr.error('Por favor, complete todos los campos antes de filtrar el reporte.', 'Error');
        return;
    }

    //console.log('Sucursal:', sucursal);
    console.log('Fecha de inicio:', fechaInicio);
    console.log('Fecha fin:', fechaFin);

    $.ajax({
        type: "POST",
        url: baseURL + 'getDataMovimientosInvent',
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
    const tbody = document.querySelector("#tblMovimientosInventario tbody");
    tbody.innerHTML = ""; // Limpiar tabla

    // Agrupar por noDocumento + des_mov
    const agrupado = {};

    datos.forEach(item => {
        const noDoc = item.noDocumento || '';
        const tipoMov = item.des_mov || '';
        const claveGrupo = `${noDoc}|||${tipoMov}`;  // Separador seguro para dividir después si se quiere

        if (!agrupado[claveGrupo]) {
            agrupado[claveGrupo] = [];
        }

        agrupado[claveGrupo].push(item);
    });

    // Recorrer los grupos y pintar
    for (const clave in agrupado) {
        const [noDoc, des_mov] = clave.split("|||");

        // Insertar encabezado único por grupo
        const trGrupo = document.createElement("tr");
        trGrupo.classList.add("grupo-titulo");
        trGrupo.innerHTML = `<td colspan="7" style="font-weight: bold; background: #e9ecef;">
            ${noDoc.toUpperCase()} - ${des_mov.toUpperCase()}
        </td>`;
        tbody.appendChild(trGrupo);

        // Pintar cada fila de producto dentro del grupo
        agrupado[clave].forEach(item => {
            const fila = document.createElement("tr");

            const suc_origen = item.suc_origen_doc || item.suc_mov || '';
            const suc_destino = item.suc_destino_doc || '';

            fila.innerHTML = `
                <td style="font-weight: 500;">
                    <div style="font-size: 14px;">${item.codProd}</div>
                    <div style="font-size: 12px; color: #6c757d;">${item.nombre_producto}</div>
                </td>
                <td>${item.cantMov}</td>
                <td>${item.precio}</td>
                <td>${item.fecha_mov.substring(0, 10)}</td>
                <td>${item.noDocumento || ''}</td>
                <td>${suc_origen}</td>
                <td>${suc_destino}</td>
            `;
            tbody.appendChild(fila);
        });
    }

    // Activar o desactivar el botón según haya datos
    const btnImprimir = document.getElementById("btnImprimir");
    const filasDatos = tbody.querySelectorAll("tr:not(.grupo-titulo)");
    btnImprimir.disabled = filasDatos.length === 0;
}

function ImprimirReporte() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    const elemento = document.querySelector('.movimientos-inventario-rp');
    const opciones = {
        margin: 0.2,
        filename: 'movimientos-inventario-rp.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opciones).from(elemento).save();
    Swal.close();
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
