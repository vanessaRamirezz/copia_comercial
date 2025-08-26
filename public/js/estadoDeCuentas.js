document.addEventListener("DOMContentLoaded", function () {
    let table = $('#dataTableEstadoCuentas').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "stateSave": false, // Asegura que no guarda el estado previo
        "initComplete": function () {
            let searchInput = $('.dataTables_filter input');
            searchInput.val('').trigger('input'); // Limpia el buscador y actualiza la tabla
        }
    });

    // Forzar que el input de búsqueda se limpie cada vez que se recarga la tabla
    setTimeout(function () {
        let searchInput = $('.dataTables_filter input');
        searchInput.val('').trigger('input');
    }, 500);
});


function buscarEstadoDecuenta(solicitud, id_solicitud) {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando estado de cuentas...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    // Obtener el valor de data-solicitud
    var numeroSolicitud = solicitud;
    // Mostrar un mensaje de carga mientras se obtiene la información
    $('.contenido-estado-cuenta').html('<p class="text-center">Cargando estado de cuenta...</p>');

    // Realizamos la petición AJAX
    obtenerEstadoCuenta(numeroSolicitud, id_solicitud);
}

// Función para hacer la petición AJAX
function obtenerEstadoCuenta(numeroSolicitud,id_solicitud) {
    const modal = new bootstrap.Modal(document.getElementById('modalEstadoCuenta'));
    $.ajax({
        url: baseURL + 'dataEstadoCuenta',  // URL de la API que devuelve el estado de cuenta
        type: 'POST',  // Método HTTP
        data: {
            noSolicitud: numeroSolicitud,
            idSolicitud: id_solicitud
        },
        dataType: 'json',  // Esperamos una respuesta JSON
        success: function (data) {
            console.log("Respuesta completa:", data);  // Aquí imprimimos toda la respuesta
            if (data.status === 'success') {
                // Cargar los datos del cliente y crédito
                var contenido = generarEstadoCuentaHTML(data.data);  // Accedemos a 'data' que contiene los sub-datos
                // Insertar el HTML generado en el contenedor de la tarjeta
                $('.contenido-estado-cuenta').html(contenido);

                mostrarBotonSiHayDatos();
                Swal.close();
            } else {
                // En caso de error en la respuesta, mostramos un mensaje
                $('.contenido-estado-cuenta').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        ${data.message}
                    </div>
                `);                
                Swal.close();
            }

            modal.show();
        },
        error: function (xhr, status, error) {
            // En caso de un error en la petición AJAX
            console.error("Error en la solicitud:", error);
            $('.contenido-estado-cuenta').html('<p class="text-center text-danger">Ocurrió un error al obtener los datos.</p>');
            modal.show();
        }
    });
}

function generarEstadoCuentaHTML(data) {
    const solicitud = data.solicitud?.[0] || {};
    var contenido = `
    <style>
        .estado-cuenta .info p,
.estado-cuenta .detalle {
    margin: 0;
    line-height: 1.3;
    font-size: 10px;
}

.estado-cuenta .text-end p {
    margin: 0;
    font-size: 10px;
    font-weight: bold;
}

.estado-cuenta table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 13px;
}

.estado-cuenta th {
    font-weight: bold;
    font-size: 10px;
    padding: 5px;
    text-align: center;
    border: none;
}

.estado-cuenta td {
    font-size: 9px;
    padding: 3px;
    text-align: center;
    border: none;
}

.estado-cuenta table tr:not(:last-child) {
    border-bottom: 1px solid #ccc;
}

.estado-cuenta .small {
    font-size: 11px;
    text-align: center;
    color: #666;
    margin-top: 8px;
}

    </style>

    <div class="col-12 info text-center mb-3">
        <h3><strong>Estado de cuenta</strong></h3>
    </div>
    <div class="d-flex justify-content-between">
        <div class="row w-100">
            <div class="col-6 info text-left">
                <p><strong>Nombre:</strong> ${data.cliente[0].nombre_completo}</p>
                <p><strong>DUI:</strong> ${data.cliente[0].dui}</p>
                <p><strong>Dirección:</strong> ${data.cliente[0].direccion_completa}</p>
                <p><strong>Teléfono:</strong> ${data.cliente[0].telefono}</p>
            </div>
            <div class="col-6 info text-right">
                <p><strong>No Crédito:</strong> ${solicitud.numero_solicitud || 'N/A'}</p>
                <p><strong>Monto del crédito:</strong> $${solicitud.monto_solicitud || 'N/A'}</p>
                <p><strong>Plazo:</strong> ${solicitud.cuotas ? solicitud.cuotas + ' meses' : 'N/A'}</p>
                <p><strong>Códigos de artículos:</strong> ${solicitud.codigos_productos || ''}</p>
            </div>
        </div>
    </div>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>No Cuota</th>
                <th>Descripción</th>
                <th>Cuota Mensual</th>
                <th>Interés Generado</th>
                <th>Fecha trans</th>
                <th>Fecha vence</th>
                <th>Pago</th>
                <th>Saldo Restante</th>
                <th>Suc. procesó</th>
            </tr>
        </thead>
        <tbody>`;

    let abonoTotal = 0;
    let interesTotalPagado = 0;
    let cuotasVencidas = 0;

    // ✅ Corrección: saldo inicial correcto
    let saldoRestante = parseFloat(solicitud.monto_solicitud) || 0;

    let fechaActual = new Date();
    let capitalPendiente = 0;
    let interesPendiente = 0;
    let montoVencido = 0;

    $.each(data.pagos, function (index, cuota) {
        let abono = parseFloat(cuota.abono) || 0;
        let interesGenerado = parseFloat(cuota.interesGenerado) || 0;
        let montoCuota = parseFloat(cuota.monto_cuota) || 0;

        abonoTotal += abono;

        // Resta el abono al saldo
        saldoRestante -= abono;

        if (cuota.estado === 'PENDIENTE') {
            capitalPendiente += montoCuota;
        } else if (cuota.estado === 'CANCELADO') {
            interesTotalPagado += interesGenerado;
        }

        // Si cuota está vencida y no pagada
        if (!cuota.fecha_formateada && cuota.fecha_vence) {
            let fechaVenceParts = cuota.fecha_vence.split("/");
            if (fechaVenceParts.length === 3) {
                let fechaVence = new Date(`${fechaVenceParts[2]}-${fechaVenceParts[1]}-${fechaVenceParts[0]}`);
                if (fechaVence < fechaActual) {
                    cuotasVencidas++;
                    interesPendiente += interesGenerado;
                    montoVencido += montoCuota;
                }
            }
        }

        let nuevoMontoCuota = montoCuota;

        if (abono < montoCuota) {
            nuevoMontoCuota = montoCuota - abono;
        }

        contenido += `
            <tr>
                <td>${cuota.numero_cuota}</td>
                <td>${cuota.descripcion || ''}</td>
                <td>$${nuevoMontoCuota.toFixed(2)}</td>
                <td>$${interesGenerado.toFixed(2)}</td>
                <td>${cuota.fecha_formateada || ''}</td>
                <td>${cuota.fecha_vence || ''}</td>
                <td>$${abono.toFixed(2)}</td>
                <td>$${saldoRestante.toFixed(2)}</td>
                <td>${cuota.sucursal_proceso_codigo !== undefined && cuota.sucursal_proceso_codigo !== null ? cuota.sucursal_proceso_codigo : ''}</td>
            </tr>`;
    });

    contenido += `
        </tbody>
    </table>

    <div class="text-end">
        <p><b>Total de abonos: $</b>${abonoTotal.toFixed(2)}</p>
        <p><b>Saldo Pendiente: $</b>${(saldoRestante >= 0 ? saldoRestante.toFixed(2) : '0.00')}</p>
    </div>

    <div style="border: 1px solid #000; padding: 10px; border-radius: 5px; margin-top: 10px;">
        <div class="row mt-4">
            <div class="col-sm-12">
                <b>***** Resumen del contrato *****</b>
            </div>
            <div class="col-sm-6">
                <p class="detalle"><b>Monto Pagado: $</b>${abonoTotal.toFixed(2)}</p>
                <p class="detalle"><b>Interés Pagado: $</b>${interesTotalPagado.toFixed(2)}</p>
                <p class="detalle"><b>Cuotas vencidas: </b>${cuotasVencidas}</p>
                <p class="detalle"><b>Total a pagar: $</b>${(saldoRestante >= 0 ? saldoRestante.toFixed(2) : '0.00')}</p>
            </div>
            <div class="col-sm-6">
                <p class="detalle"><b>Capital Pendiente: $</b>${(saldoRestante >= 0 ? saldoRestante.toFixed(2) : '0.00')}</p>
                <p class="detalle"><b>Interés Pendiente: $</b>${interesPendiente.toFixed(2)}</p>
                <p class="detalle"><b>Monto vencido: $</b>${montoVencido.toFixed(2)}</p>
            </div>
        </div>
    </div>

    <div class="text-center small">
        * Gracias por elegir nuestros servicios.
    </div>`;

    return contenido;
}



function mostrarBotonSiHayDatos() {
    let contenido = document.querySelector('.contenido-estado-cuenta').innerHTML.trim();
    let footerBoton = document.getElementById('footerBoton');

    if (contenido !== '') {
        footerBoton.classList.remove('d-none');
    } else {
        footerBoton.classList.add('d-none');
    }
}
function imprimirEstadoCuenta() {
    const elemento = document.querySelector('.contenido-estado-cuenta');

    const opciones = {
        margin: 0.5,
        filename: 'estado_cuenta.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opciones).from(elemento).save();
}
