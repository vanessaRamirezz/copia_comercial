var duiClienteBusqueda = '';
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        document.getElementById("campoBusqueda").value = "";
    }, 100);
    $('#procesarCuotas').hide();

    const tipoBusqueda = document.getElementById("tipoBusqueda");
    const campoBusqueda = document.getElementById("campoBusqueda");

    campoBusqueda.disabled = true;

    tipoBusqueda.addEventListener("change", function () {
        campoBusqueda.value = "";
        btnBuscar.style.display = "none";

        if (this.value === "dui") {
            $(campoBusqueda).addClass('duiG').mask('00000000-0', { placeholder: "00000000-0" });
            campoBusqueda.disabled = false;
        } else if (this.value === "nombre") {
            $(campoBusqueda).removeClass('duiG').unmask();
            campoBusqueda.disabled = false;
        } else {
            campoBusqueda.disabled = true;
        }
    });

    $(campoBusqueda).autocomplete({
        source: function (request, response) {
            // Mostrar opción de "Buscando resultados..."
            response([{ label: "Buscando resultados...", value: "" }]);
            $.ajax({
                url: baseURL + "searchClient",
                type: "POST",
                dataType: "json",
                data: request.term, // Asegurar que se envía correctamente
                success: function (data) {
                    console.log("Respuesta del servidor:", data);

                    // Asegurar que la respuesta sea un array de objetos
                    if (Array.isArray(data)) {
                        response(data.map(item => ({
                            label: item.nombre_completo + " - " + item.dui, // Lo que se muestra en la lista
                            value: item.nombre_completo, // Lo que se coloca en el input
                            dui: item.dui // Guardamos el DUI en la selección
                        })));
                    } else {
                        // Si no es un array, retornar mensaje amigable
                        response([{ label: "No se encontraron resultados", value: "" }]);
                    }
                },
                error: function () {
                    console.error("Error en la búsqueda de clientes");
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            campoBusqueda.value = ui.item.value; // Mostrar el nombre en el input
            //duiBuscarCliente(ui.item.dui); // Pasar el DUI como parámetro
            duiClienteBusqueda = ui.item.dui;
            buscarClienteDeudas(ui.item.dui);
        }
    });

    // Mostrar botón "Buscar" al escribir en el campo
    campoBusqueda.addEventListener("input", function () {
        if (tipoBusqueda.value === "dui" && campoBusqueda.value.trim().length >= 9) {
            btnBuscar.style.display = "inline-block";
        } else {
            btnBuscar.style.display = "none";
        }
    });

    // Evento para buscar cliente dui
    btnBuscar.addEventListener("click", function () {
        // Obtener el valor de búsqueda dependiendo del tipo seleccionado
        const valorBusqueda = campoBusqueda.value.trim();

        if (valorBusqueda === "") {
            Swal.fire('Error', 'Debe ingresar un valor para buscar.', 'error');
            return;
        }

        if (tipoBusqueda.value === "dui") {
            // Si es búsqueda por DUI, verificar que el formato sea correcto
            if (valorBusqueda.length === 10) {
                buscarClienteDeudas(valorBusqueda); // Pasar el DUI
            } else {
                Swal.fire('Error', 'El DUI debe tener un formato válido (00000000-0).', 'error');
            }
        } /* else if (tipoBusqueda.value === "nombre") {
            // Si es búsqueda por nombre, buscar por el nombre completo
            buscarClienteDeudas(valorBusqueda);
        } */
    });
});
$('#selectAll').click(function () {
    $('input[name="selectedPayments[]"]').prop('checked', this.checked);
});

function submitSelectedPayments() {

    const today = new Date();
    today.setHours(0, 0, 0, 0); // Establece la fecha de hoy a medianoche para una comparación precisa

    const interestRate = 0.07; // 7% de interés mensual
    const daysInMonth = 30; // Mes comercial de 30 días
    const solicitudNu = $("#solicitudNumero").val();
    const idSolicitudSelect = $("#idSolicitud").val();

    const cuotasCubiertas = $('#cuotasCubiertas').val();
    const saldoRestante = $('#saldoRestanteAFavor').val();
    const moraTotalAPagar = $('#moraTotalAPagar').val();
    const deseaCobrarMora = $('#deseaCobrarMora').val();
    const montoTotalaCancelar = $('#montoTotalaCancelar').val();
    const esProntoPago = $('#prontoPago').val();
    let alertMessage = '';
    if (esProntoPago == 'si') {
        alertMessage += `<p><strong>Es pronto pago, con el monto ingresado ($${montoTotalaCancelar}), se cancelara la totalidad del credito</strong></p>`;
    } else {
        // Validar que montoTotalaCancelar no esté vacío o sea 0
        if (!montoTotalaCancelar || parseFloat(montoTotalaCancelar) <= 0) {
            toastr.error("Por favor, ingresa un monto válido para cancelar.", "Error");
            return; // Detener la ejecución de la función si el monto no es válido
        }

        // Construir el mensaje con los valores fijos
        
        if (cuotasCubiertas > 0) {
            alertMessage += `<p><strong>El monto ingresado cubre ${cuotasCubiertas} cuota(s)</strong></p>`;
        }

        if (saldoRestante !== null && saldoRestante !== 0 && saldoRestante !== '') {
            alertMessage += `<p><strong>El saldo restante es de $${parseFloat(saldoRestante).toFixed(2)}, se usará para abonar la siguiente cuota</strong></p>`;
        }

        if (moraTotalAPagar !== null && moraTotalAPagar !== 0 && moraTotalAPagar !== '') {
            alertMessage += `<p><strong>Del saldo ingresado, $${parseFloat(moraTotalAPagar).toFixed(2)} se usará para cubrir la mora</strong></p>`;
        }
    }

    // Aquí no se valida si hay pagos seleccionados, se asume que siempre hay un monto a pagar
    // Mostrar el mensaje de confirmación
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success",
            cancelButton: "btn btn-danger"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "¿Estás seguro de pagar?",
        html: alertMessage,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, pagar",
        cancelButtonText: "No, cancelar",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            procesarPagos(solicitudNu, cuotasCubiertas, saldoRestante, moraTotalAPagar, deseaCobrarMora, montoTotalaCancelar, idSolicitudSelect,esProntoPago);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Cancelado",
                text: "El pago no se ha realizado.",
                icon: "error"
            });
        }
    });
}


function procesarPagos(solicitudNu, cuotasCubiertas, saldoRestante, moraTotalAPagar, deseaCobrarMora, montoTotalaCancelar, idSolicitudSelect,esProntoPago) {
    // Mostrar alerta de carga mientras se procesa el pago
    Swal.fire({
        title: 'Espere...',
        html: 'Procesando pagos, por favor espere.',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Crear objeto con los datos para enviar
    const datos = JSON.stringify({
        solicitud: solicitudNu,
        cuotasCubiertas: cuotasCubiertas,
        saldoRestante: saldoRestante,
        moraTotalAPagar: moraTotalAPagar,
        deseaCobrarMora: deseaCobrarMora,
        montoTotalaCancelar: montoTotalaCancelar,
        ArrayPago: cobrosProcesados,
        idSolicitudSelect: idSolicitudSelect,
        esProntoPago: esProntoPago
    });

    $.ajax({
        url: baseURL + 'procesarPagosCuotas',  // La URL para procesar pagos en el servidor
        method: 'POST',
        contentType: 'application/json',
        data: datos,
        success: function (response) {
            console.log(response);
            Swal.close();
            if (response.ok) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    if (response.flagDoc && response.documento) {
                        Swal.showLoading();
                        descargarDocumento(response.documento); // Descargar documento si existe
                    }
                });

            } else {
                Swal.fire({
                    title: 'Atención',
                    text: response.message, // Usar el mensaje proporcionado por el servidor
                    icon: 'warning'
                });
            }
        },
        error: function (xhr, status, error) {
            // Manejo de errores HTTP u otros problemas
            Swal.close();
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un problema al procesar los pagos. Por favor, inténtalo de nuevo.',
                icon: 'error'
            });
        }
    });
}

function descargarDocumento(ruta) {
    let countdown = 5; // Tiempo total de la cuenta regresiva
    const totalTime = countdown; // Tiempo total

    Swal.fire({
        title: 'Descargando...',
        html: `
            <div>Esperando... <span id="countdownText">${countdown} segundos</span></div>
            <div class="progress" style="height: 25px;">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        `,
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            const interval = setInterval(() => {
                countdown--;

                document.getElementById('countdownText').textContent = `${countdown} segundos`;
                let progress = ((totalTime - countdown) / totalTime) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                document.getElementById('progressBar').setAttribute('aria-valuenow', progress);

                if (countdown === 0) {
                    clearInterval(interval);

                    // Ejecutar la función antes de cerrar el modal
                    buscarClienteDeudas(duiClienteBusqueda);
                    // Descargar el archivo
                    // Descargar automáticamente el archivo
                    const fileUrl = baseURL + ruta;
                    const link = document.createElement('a');
                    link.href = fileUrl;
                    link.download = 'Factura.pdf'; // Puedes poner un nombre personalizado si quieres
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    $('#modalCobros').modal('hide');

                    document.getElementById('modalCobros').classList.remove('show');
                    document.getElementById('modalCobros').setAttribute('aria-hidden', 'true');
                    document.getElementById('modalCobros').style.display = 'none';
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());

                    // Asegurar que el modal se cierre sí o sí
                    Swal.fire({
                        title: 'Completado',
                        text: 'Procesando información...',
                        icon: 'success',
                        timer: 1000, // Se cierra automáticamente después de 1 segundo
                        showConfirmButton: false
                    }).then(() => {
                        Swal.close(); // Asegura que se cierre si aún está abierto
                    });
                }
            }, 1000);
        }
    });
}



/* function descargarDocumento(ruta) {
    let countdown = 5; // Tiempo total de la cuenta regresiva
    const totalTime = countdown; // Tiempo total

    const swalTimer = Swal.fire({
        title: 'Descargando...',
        html: `
            <div>Esperando... <span id="countdownText">${countdown} segundos</span></div>
            <div class="progress" style="height: 25px;">
                <div id="progressBar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        `,
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            const interval = setInterval(function () {
                countdown--;

                // Actualizar el texto con el tiempo restante
                document.getElementById('countdownText').textContent = `${countdown} segundos`;

                // Calcular el porcentaje de la barra de progreso
                let progress = ((totalTime - countdown) / totalTime) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
                document.getElementById('progressBar').setAttribute('aria-valuenow', progress);

                // Cuando el contador llegue a 0, cerrar el modal y descargar el archivo
                if (countdown === 0) {
                    clearInterval(interval);
                    swalTimer.close(); // Cerrar el modal

                    // Descargar el archivo
                    var fileUrl = baseURL + ruta;
                    console.log(fileUrl); // Para depuración
                    window.open(fileUrl, '_blank'); // Abrir la descarga en una nueva ventana

                    location.reload(true);
                }
            }, 1000); // Intervalo de 1 segundo
        }
    });
} */

function buscarClienteDeudas(valorEntrada) {
    var duiCliente = valorEntrada;

    console.log(duiCliente);

    var flagValidate = false;

    if (duiCliente.trim() == "") {
        toastr.info("El DUI es requerido", "Campo incompleto");
        flagValidate = true;
        return;
    }

    if (!flagValidate) {
        Swal.fire({
            title: 'Espere...',
            html: `Buscando solicitud del DUI: ${duiCliente}`,
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        var formData = {
            duiCliente: duiCliente
        };

        $.ajax({
            type: "POST",
            url: baseURL + "getCobrosCliente",
            data: JSON.stringify(formData),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (rsp) {
                console.log("los datos de rsp son:: ", rsp);
                let solicitudesHtml = '';
                // Ordenar por fecha_creacion descendente (más reciente primero)
                rsp.sort((a, b) => new Date(b.fecha_creacion) - new Date(a.fecha_creacion));

                rsp.forEach(function (solicitud) {
                    const estadoCuenta = parseFloat(solicitud.montoApagar) === 0
                        ? 'Cuenta Cancelada'
                        : 'Cuenta Activa';

                    const deshabilitarBotones = solicitud.tipo_solicitud === 'CONTADO' ? 'disabled' : '';
                    const claseBoton = solicitud.tipo_solicitud === 'CREDITO' ? 'btn-danger' : 'btn-success';

                    solicitudesHtml += `<tr data-id="${solicitud.id_solicitud}">
                    <td>${solicitud.numero_solicitud}</td>
                    <td>${solicitud.dui}</td>
                    <td>${solicitud.nombre_completo}</td>
                    <td>${solicitud.fecha_creacion}</td>
                    <td>${solicitud.productos_descripcion ? solicitud.productos_descripcion : solicitud.codigos_productos}</td>
                    <td>${solicitud.sucursal}</td>
                    <td>$${solicitud.montoApagar}</td>
                    <td>${estadoCuenta}</td>
                    <td>${solicitud.tipo_solicitud}</td>
                    <td>
                        <div style="display: flex; gap: 6px; justify-content: center;">
                            <button class="btn ${claseBoton} btn-sm realizarCobro" 
                                    title="Cobro"
                                    data-soli="${solicitud.numero_solicitud}" 
                                    data-id="${solicitud.id_solicitud}"
                                    data-codProd="${solicitud.productos_descripcion}">
                                <i class="fa-solid fa-cash-register"></i>
                            </button>
                            <button class="btn btn-info btn-sm text-white" 
                                    title="Estado de Cuenta"
                                    data-id="${solicitud.id_solicitud}"
                                    onclick="buscarEstadoDecuenta('${solicitud.numero_solicitud}', '${solicitud.id_solicitud}')"
                                    ${deshabilitarBotones}>
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </button>
                        </div>
                    </td>
                    <td style="display: none;">${solicitud.productos_descripcion}</td>
                </tr>`;
                });

                $('#cuerpoSolicitudes').html(solicitudesHtml);
                Swal.close();
            },
            error: function () {
                toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
                Swal.close();
            }
        });
    }
}

$(document).on('click', '.realizarCobro', function () {
    var solicitudId = $(this).data('id');
    var solicitudNu = $(this).data('soli');
    var codProd = $(this).data('codprod');

    Swal.fire({
        title: 'Espere...',
        html: `Buscando deudas de la solicitud: ${solicitudNu}`,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        type: "POST",
        url: baseURL + "getDeudasPorSolicitud",
        data: JSON.stringify({ id_solicitud: solicitudId }),
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (rsp) {
            $('#btnValidarCuotas').show();
            $('#procesarCuotas').hide();
            const alertGeneral = $('.alert-general');
            alertGeneral.hide();
            let cobrosHtml = `<input value="${solicitudNu}" id="solicitudNumero" readonly hidden>`;
            const interestRate = 0.07;
            const daysInMonth = 30;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            let moraTotal = 0.0;
            let allCancelled = true; // Variable para verificar si todos están cancelados

            rsp.forEach(function (cobro) {
                $('#cuotasCubiertas').val(0);
                $('#saldoRestanteAFavor').val("");
                $('#idSolicitud').val("");
                $('.alert-general').html('');
                let mora = 0;
                let daysLate = 0;

                if (cobro.estado === 'CANCELADO') {
                    cobrosHtml += `<tr>
                        <td class="text-center"><input class="form-check-input" type="checkbox" name="selectedPayments[]" value="${cobro.id_cobro}" disabled></td>
                        <td>${cobro.numero_cuota} ${cobro.esPrima == 1 ? '(Prima)' : ''}</td>
                        <td>${cobro.fecha_vencimiento}</td>
                        <td>${cobro.fecha_pago}</td>
                        <td>$${cobro.monto_cuota}</td>
                        <td>$${cobro.cantAbono}</td>
                        <td>$0.00</td>
                        <td>CANCELADO</td>
                        <td><input type="text" disabled class="form-control" name="moraGenerada" value="$0"></td>
                        <td style="display: none;">${cobro.id_cobro}</td>
                    </tr>`;
                } else {
                    allCancelled = false; // Si hay un cobro pendiente, no están todos cancelados
                    const paymentDateStr = cobro.fecha_vencimiento;
                    const paymentDate = new Date(paymentDateStr + 'T23:59:59');
                    paymentDate.setHours(0, 0, 0, 0);
                    const timeDifference = today - paymentDate;
                    daysLate = timeDifference > 0 ? Math.floor(timeDifference / (1000 * 60 * 60 * 24)) : 0;

                    if (daysLate > 0) {
                        const amount = parseFloat(cobro.monto_cuota);
                        const dailyInterest = (amount * interestRate) / daysInMonth;
                        mora = dailyInterest * daysLate;
                        moraTotal += mora;
                    }

                    cobrosHtml += `<tr>
                        <td class="text-center"><input disabled class="form-check-input cobroCheck" type="checkbox" name="selectedPayments[]" value="${cobro.id_cobro}" data-monto="${cobro.monto_cuota}"></td>
                        <td>${cobro.numero_cuota}</td>
                        <td>${cobro.fecha_vencimiento}</td>
                        <td>${cobro.fecha_pago != null ? cobro.fecha_pago : ''}</td>
                        <td>$${cobro.monto_cuota}</td>
                        <td>$${cobro.cantAbono}</td>
                        <td>$${(cobro.monto_cuota - cobro.cantAbono).toFixed(2)}</td>
                        <td>${cobro.estado}</td>
                        <td><input type="text" readonly class="form-control moraGenerada" name="moraGenerada" value="$${cobro.interesGenerado != 0 ? 0 : mora.toFixed(2)}"></td>
                        <td style="display: none;">${cobro.id_cobro}</td>
                    </tr>`;
                }
            });

            cobrosHtml += `<tr id="totalRow">
                <td colspan="3" class="text-right"><strong>Ingrese monto a cancelar:</strong></td>
                <td colspan="1"><input type="text" class="form-control montosG" id="montoTotalaCancelar" name="montoTotalaCancelar"></td>
                <td colspan="3" class="text-right"><strong>Mora total a cancelar:</strong></td>
                <td colspan="1"><input type="text" disabled class="form-control montosG" id="moraTotalAPagar" value="${moraTotal.toFixed(2)}" name="moraTotalAPagar" data-original-value="${moraTotal.toFixed(2)}"></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>¿Desea cobrar mora?</strong></td>
                <td colspan="1">
                    <select class="form-control" ${moraTotal.toFixed(2) > 0.0 ? '' : 'disabled'} id="deseaCobrarMora" name="deseaCobrarMora">
                        <option value="si_total">Sí, total</option>
                        <option value="no_parcial">No, parcial</option>
                        <option value="no_cobrar">No cobrar</option>
                    </select>
                </td>
                <td colspan="3" class="text-right"><strong>¿Cancelación por pronto pago?</strong></td>
                <td colspan="1">
                    <select class="form-control" id="prontoPago" name="prontoPago">
                        <option value="si">Si</option>
                        <option value="no" selected>No</option>
                    </select>
                </td>
            </tr>`;

            if (codProd) {
                $('#codigosProductosTexto').text(codProd);
            } else {
                $('#codigosProductosTexto').text('No hay productos');
            }

            $('#cobrosList').html(cobrosHtml);
            $('#modalCobros').modal('show');
            $('#idSolicitud').val(solicitudId);
            $('.montosG').mask('000000000.00', { reverse: true, placeholder: "00.00" });
            Swal.close();

            // Bloquear botones si todos los cobros están cancelados
            if (allCancelled) {
                $('#btnValidarCuotas, #procesarCuotas').prop('disabled', true);
            } else {
                $('#btnValidarCuotas, #procesarCuotas').prop('disabled', false);
            }
        },
        error: function () {
            toastr.error("Ocurrió un error al cargar los cobros", "Error en carga de cobros");
            Swal.close();
        }
    });
});


// Evento para manejar cambios en #deseaCobrarMora
$(document).on('change', '#deseaCobrarMora', function () {
    const selectedValue = $(this).val();
    const moraInput = $('#moraTotalAPagar');
    let moraTotal = parseFloat(moraInput.data('original-value')); // Valor original de la mora almacenado en data-attribute

    if (selectedValue === 'no_cobrar') {
        // Si selecciona "No cobrar", deshabilitar el input y establecer el valor a 0
        moraInput.val('0.00');
        moraInput.prop('disabled', true);
    } else if (selectedValue === 'no_parcial') {
        // Si selecciona "No parcial", habilitar el input para edición
        moraInput.prop('disabled', false);
    } else if (selectedValue === 'si_total') {
        // Si selecciona "Sí, total", deshabilitar el input y restaurar el valor original
        const originalMora = $('#moraTotalAPagar').data('original-value');
        moraInput.val(`${originalMora.toFixed(2)}`);
        moraInput.prop('disabled', true);
    }

    // Disparar el evento para recalcular los saldos después del cambio
    $('#montoTotalaCancelar').trigger('input');
    $('#btnValidarCuotas').show();
    $('#procesarCuotas').hide();
});

$(document).on('input', '#moraTotalAPagar, #montoTotalaCancelar', function () {
    $('#btnValidarCuotas').show();
    $('#procesarCuotas').hide();
});

// Evento para manejar cambios en el input #moraTotalAPagar
// Asegúrate de que el DOM esté listo antes de ejecutar el script
// Función para procesar el monto ingresado

let cobrosProcesados = [];
function validarMontosPago() {
    cobrosProcesados = [];
    const montoIngresado = parseFloat($('#montoTotalaCancelar').val()); // Monto ingresado
    const moraTotal = parseFloat($('#moraTotalAPagar').val()); // Mora total a pagar
    const esProntoPago = $('#prontoPago').val(); // valida si es pronto pago
    const alertGeneral = $('.alert-general'); // Seleccionar el div de alert
    let saldo = montoIngresado;
    console.log("saldo inicial::", saldo);
    // Reiniciar contenido del div de alerta
    alertGeneral.removeClass('alert-success alert-danger').html('');

    // Validar monto ingresado
    if (isNaN(montoIngresado) || montoIngresado <= 0) {
        alertGeneral
            .addClass('alert-danger')
            .html(`<h4 class="alert-heading">Error</h4><p>Por favor, ingrese un monto válido para el total a cancelar.</p>`)
            .show();
        return;
    }

    // Validar mora
    if (isNaN(moraTotal) || moraTotal < 0) {
        alertGeneral
            .addClass('alert-danger')
            .html(`<h4 class="alert-heading">Error</h4><p>Por favor, ingrese un valor válido para la mora.</p>`)
            .show();
        return;
    }

    let cuotasCubiertas = 0;
    let mensaje = "";
    if (esProntoPago === 'si') {
        mensaje += `<p>Se ha seleccionado pronto pago, el saldo ingresado cancelara por completo el credito</p>`;
    } else {
        // Restar la mora del saldo disponible
        const cobrarMora = $('#deseaCobrarMora').val();
        if (cobrarMora !== "no_cobrar") {
            if (saldo >= moraTotal) {
                saldo -= moraTotal;
                console.log("saldo al restar la mora:: ", saldo);
                mensaje += `<p>Del saldo ingresado, $${moraTotal.toFixed(2)} se usará para cubrir la mora.</p>`;
                cobrosProcesados.push({
                    id_cobro: 0, // Identificador temporal para la mora
                    monto_abonado: moraTotal.toFixed(2),
                    completo: 0
                });
            } else {
                alertGeneral
                    .addClass('alert-danger')
                    .html(`<h4 class="alert-heading">Error</h4><p>El monto ingresado no es suficiente para cubrir la mora total.</p>`)
                    .show();
                return;
            }
        } else {
            mensaje += `<p>No se cobrará mora según lo seleccionado.</p>`;
        }

        // Iterar sobre las cuotas en la tabla

        $('#cobrosList tr').each(function (index) {
            const estado = $(this).find('td:nth-child(8)').text().trim(); // Estado de la fila
            const montoCuota = parseFloat($(this).find('td:nth-child(5)').text().replace('$', '').trim()); // Monto de la cuota
            let montoAbono = parseFloat($(this).find('td:nth-child(6)').text().replace('$', '').trim()) || 0; // Monto abonado
            const checkbox = $(this).find('input.cobroCheck'); // Checkbox de la fila
            const idCobro = $(this).find('td:nth-child(10)').text().trim();
            const noCuota = $(this).find('td:nth-child(2)').text().trim();

            // Ignorar las cuotas canceladas
            if (estado.toLowerCase() === "cancelado") {
                checkbox.prop('checked', false);
                return;
            }
            //102.78-100
            let montoRestante = parseFloat((montoCuota - montoAbono).toFixed(2)); // Cuánto falta para cubrir la cuota
            // 2.78 >= 2.78
            if (saldo >= montoRestante) {
                // Si el saldo es suficiente para cubrir el restante de la cuota, se paga completa
                checkbox.prop('checked', true);
                $(this).css('background-color', 'lightgreen');
                saldo -= montoRestante;
                montoAbono += montoRestante; // Aquí solo sumamos el abono de esta transacción
                //montoAbono = montoCuota; // Se marca como totalmente pagada
                cuotasCubiertas++;
                mensaje += `<p>La cuota ${noCuota != 0 ? noCuota : index} ha sido cubierta totalmente $${montoRestante}.</p>`;

                cobrosProcesados.push({
                    id_cobro: idCobro,
                    monto_abonado: (montoRestante < montoCuota) ? montoRestante : montoAbono.toFixed(2),
                    completo: 1
                });

            } else if (saldo > 0) {
                // Si no alcanza para cubrir toda la cuota, se abona lo que se pueda
                let montoPendiente = (montoCuota - montoAbono - saldo).toFixed(2);
                montoAbono += saldo;
                mensaje += `<p>Se ha abonado $${saldo.toFixed(2)} a la cuota ${noCuota != 0 ? noCuota : index}, pero aún queda saldo pendiente en esa cuota (${montoPendiente}).</p>`;
                $('#saldoRestanteAFavor').val((saldo != null && saldo != '') ? saldo.toFixed(2) : 0.00);//

                cobrosProcesados.push({
                    id_cobro: idCobro,
                    monto_abonado: saldo.toFixed(2),
                    completo: 0
                });
                saldo = 0;
            }
        });

        // Mostrar mensaje si queda saldo restante
        if (saldo > 0) {
            mensaje += `<p>El saldo restante de $${saldo.toFixed(2)} se usará para abonar la siguiente cuota.</p>`;
        }
    }

    // Actualizar valores ocultos
    $('#cuotasCubiertas').val(cuotasCubiertas || 0);
    $('#procesarCuotas').show();
    $('#btnValidarCuotas').hide();

    // Mostrar mensaje final
    alertGeneral
        .addClass('alert-success')
        .html(`<h4 class="alert-heading">¡Proceso completado!</h4>${mensaje}`)
        .show();

    console.log(cobrosProcesados);
}



// Asociar la función a los eventos de los inputs
//$(document).on('input', '#montoTotalaCancelar, #moraTotalAPagar', validarMontosPago);
$(document).on('click', '#btnValidarCuotas', function () {
    // Mostrar el modal de SweetAlert con un mensaje de validación
    Swal.fire({
        title: 'Validando...',
        text: 'Por favor espere mientras validamos las cuotas...',
        allowOutsideClick: false, // Evitar que el usuario cierre el modal
        didOpen: () => {
            Swal.showLoading(); // Mostrar el indicador de carga
        }
    });

    // Esperar 3 segundos antes de ejecutar la validación
    setTimeout(function () {
        // Cerrar el modal
        Swal.close();

        // Ejecutar la validación
        validarMontosPago();
    }, 3000); // 3000 ms = 3 segundos
});




// Al cargar, guardar el valor original de la mora en data-attribute
$(document).ready(function () {
    const moraTotal = parseFloat($('#moraTotalAPagar').val());
    $('#moraTotalAPagar').data('original-value', moraTotal); // Guardar el valor original en un atributo
});

