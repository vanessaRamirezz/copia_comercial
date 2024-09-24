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
                        "id_solicitud": decodedSolicitud
                    },
                    dataType: "json",
                    success: function (rsp) {
                        console.log(rsp);
                        if (rsp.success) {
                            toastr.success(rsp.message, "Success");
                        
                            // Mostrar el modal
                            $('#modalDescarga').modal('show');
                            
                            var countdown = 5; // Segundos para la cuenta regresiva
                            var interval = setInterval(function () {
                                countdown--;
                                $('#countdown').text(countdown); // Actualizar el contador en el modal
                                
                                if (countdown === 0) {
                                    clearInterval(interval);
                                    
                                    // Descargar el archivo
                                    var fileUrl = baseURL + 'archivo/descargar/' + rsp.solicitud;
                                    window.open(fileUrl, '_blank');
                                    
                                    // Redirigir a solicitudes después de la descarga
                                    setTimeout(function () {
                                        window.location.href = baseURL + 'solicitudes';
                                    }, 2000); // 2 segundos de espera para que comience la descarga antes de redirigir
                                }
                            }, 1000); // Intervalo de 1 segundo para el conteo regresivo
                        }
                         else if (rsp.error) {
                            Swal.close();
                            toastr.error(rsp.error, "ERROR");
                        }
                    },
                    error: function () {
                        Swal.close();
                        toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
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
