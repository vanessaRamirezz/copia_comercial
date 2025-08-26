document.addEventListener("DOMContentLoaded", function () {
    const inputsAndSelects = document.querySelectorAll('.ver_datos_solicitud input, .ver_datos_solicitud select');
    inputsAndSelects.forEach(function (element) {
        element.disabled = true;
    });

    $('.next-btn').click(function () {
        var currentCollapse = $(this).closest('.collapse');
        var nextCollapse = currentCollapse.closest('.card').next('.card').find('.collapse');

        currentCollapse.collapse('hide');
        nextCollapse.collapse('show');
    });
});

function handleAction(tipoMov) {
    document.querySelectorAll('[onclick^="handleAction"]').forEach(button => {
        button.disabled = true;  // Se deshabilitan completamente
    });

    var detalleSeries = document.getElementById('numeroSerie').value.trim();
    if (tipoMov == 'Aprobar') {
        const urlParams = new URLSearchParams(window.location.search);
        const solicitud = urlParams.get('solicitud');
        const decodedSolicitud = atob(solicitud);
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: "btn btn-success",
                cancelButton: "btn btn-danger"
            },
            buttonsStyling: false
        });
        swalWithBootstrapButtons.fire({
            title: "Estas seguro de aprobar esta solicitud?",
            text: "No podrás revertir esto",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Si, aprobar",
            cancelButtonText: "No, cancelar!",
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success ml-2', // Agrega margen izquierdo
                cancelButton: 'btn btn-danger mr-2'   // Agrega margen derecho
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Espere...',
                    html: 'Procesando solicitud...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                $.ajax({
                    type: "POST",
                    url: baseURL + "actualizarEstado",
                    data: {
                        "id_solicitud": decodedSolicitud,
                        "detalleSeries": detalleSeries
                    },
                    dataType: "json",
                    success: function (rsp) {
                        console.log("Respuesta al editar:", JSON.stringify(rsp, null, 2));

                        if (rsp.success) {
                            toastr.success(rsp.message, "Success");
                    
                            // Mostrar el modal con SweetAlert
                            let countdown = 5; // Tiempo total de la cuenta regresiva
                            const totalTime = countdown; // Tiempo total para calcular el porcentaje de la barra de progreso
                    
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
                                    setTimeout(() => {
                                        const countdownElement = document.getElementById('countdownText');
                                        const progressBar = document.getElementById('progressBar');
                                
                                        if (!countdownElement || !progressBar) {
                                            console.error("Elementos no encontrados en el DOM");
                                            return;
                                        }
                                
                                        const interval = setInterval(function () {
                                            countdown--; // Decrementar el contador
                                
                                            // Actualizar el texto con el tiempo restante
                                            countdownElement.textContent = `${countdown} segundos`;
                                
                                            // Calcular el porcentaje de la barra de progreso
                                            let progress = ((totalTime - countdown) / totalTime) * 100;
                                            progressBar.style.width = progress + '%';
                                            progressBar.setAttribute('aria-valuenow', progress);
                                
                                            // Cuando el contador llegue a 0, cerrar el modal y descargar el archivo
                                            if (countdown === 0) {
                                                clearInterval(interval); // Detener el intervalo
                                                swalTimer.close(); // Cerrar el modal de progreso
                                
                                                // Descargar el archivo
                                                var fileUrl = baseURL + 'archivo/descargar/' + rsp.solicitud;
                                                console.log(fileUrl); // Para depuración
                                                window.open(fileUrl, '_blank'); // Abrir la descarga en una nueva ventana
                                
                                                // Redirigir a solicitudes después de la descarga
                                                setTimeout(function () {
                                                    window.location.href = baseURL + 'solicitudes';
                                                }, 2000); // 2 segundos de espera antes de redirigir
                                            }
                                        }, 1000); // Intervalo de 1 segundo
                                    }, 100); // Esperar 100ms para asegurarse de que el modal cargue
                                }
                                
                            });
                        }
                         else if (rsp.error) {
                            Swal.close();
                            toastr.error(rsp.error, "ERROR");
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("Error en la solicitud AJAX:", status, error);
                    },
                    complete: function () {
                        Swal.close();
                    }
                });

            } else if (result.dismiss === Swal.DismissReason.cancel) {
                swalWithBootstrapButtons.fire({
                    title: "Cancelado",
                    text: "Proceso cancelado :)",
                    icon: "error"
                });
            }
        });
    } else if (tipoMov == 'AprobadoConObs') {
        $("#accionAprobLabel").text("Aprobacion de solicitud con observaciones");
        $('.contenidoHandle').empty();
        $('.contenidoHandle').append(`
            <div class="form-group" style="text-align: left; display: block;">
                <label for="estado_solicitud">Estado de la solicitud</label>
                <select class="form-control" id="estado_solicitud" disabled>
                    <option value="2">Aprobada con observacion</option>
                </select>
            </div>
            <div class="form-group" style="text-align: left; display: block;">
              <label for="aprobadaConObservacion">Ingresa la observacion</label>
              <textarea class="form-control" id="aprobadaConObservacion" rows="3"></textarea>
            </div>
          `);


        $('#accionAprob').modal('show');
    } else if (tipoMov == 'Denegada') {
        $("#accionAprobLabel").text("Solicitud denegada");
        $('.contenidoHandle').empty();
        $('.contenidoHandle').append(`
            <div class="form-group" style="text-align: left; display: block;">
                <label for="estado_solicitud">Estado de la solicitud</label>
                <select class="form-control" id="estado_solicitud" disabled>
                    <option value="3">Solicitud denegada</option>
                </select>
            </div>
            <div class="form-group" style="text-align: left; display: block;">
              <label for="denegadaObservacion">Porque deniegas la solicitud, ingresa un comentario</label>
              <textarea class="form-control" id="denegadaObservacion" rows="3"></textarea>
            </div>
          `);
        $('#accionAprob').modal('show');
    } else if (tipoMov == 'Anulada') {
        $("#accionAprobLabel").text("Solicitud anulada");
        $('.contenidoHandle').empty();
        $('.contenidoHandle').append(`
            <div class="form-group" style="text-align: left; display: block;">
                <label for="estado_solicitud">Estado de la solicitud</label>
                <select class="form-control" id="estado_solicitud" disabled>
                    <option value="4">Solicitud anulada</option>
                </select>
            </div>
            <div class="form-group" style="text-align: left; display: block;">
              <label for="anuladaObservacion" class="text-left">Porque anulas la solicitud, ingresa un comentario</label>
              <textarea class="form-control" id="anuladaObservacion" rows="3"></textarea>
            </div>
          `);
        $('#accionAprob').modal('show');
    }
}

function guardarEstado() {
    let observacion = '';
    let error = false;
    let id_estado = $('#estado_solicitud').val();
    const urlParams = new URLSearchParams(window.location.search);
    const solicitud = urlParams.get('solicitud');
    const decodedSolicitud = atob(solicitud);
    console.log('Valor decodificado:', decodedSolicitud);

    if ($('#aprobadaConObservacion').length) {
        observacion = $('#aprobadaConObservacion').val().trim();
        if (observacion === '') {
            toastr.error("La observación es obligatoria para la aprobación con observaciones.");
            error = true;
        }
    } else if ($('#denegadaObservacion').length) {
        observacion = $('#denegadaObservacion').val().trim();
        if (observacion === '') {
            toastr.error("El comentario es obligatorio para denegar la solicitud.");
            error = true;
        }
    } else if ($('#anuladaObservacion').length) {
        observacion = $('#anuladaObservacion').val().trim();
        if (observacion === '') {
            toastr.error("El comentario es obligatorio para anular la solicitud.");
            error = true;
        }
    }

    if (!error) {
        Swal.fire({
            title: 'Espere...',
            html: 'Procesando solicitud...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: "POST",
            url: baseURL + "actualizarEstado",
            data: {
                "observacion": observacion,
                "id_estado": id_estado,
                "id_solicitud": decodedSolicitud
            },
            dataType: "json",
            success: function (rsp) {
                if (rsp.success) {
                    toastr.success(rsp.message, "Success");
                    setTimeout(function () {
                        window.history.back(); // Redirige a la página anterior
                    }, 2000);
                } else if (rsp.error) {
                    Swal.close();
                    toastr.error(rsp.message, "ERROR");
                }
            },
            error: function () {
                Swal.close();
                toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
            }, complete: function () {
                Swal.close();
            }
        });
    }
}
