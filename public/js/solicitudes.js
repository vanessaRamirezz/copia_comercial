document.addEventListener("DOMContentLoaded", function () {
    let table = $('#dataTableSolCreadas').DataTable({
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

    let table2 = $('#dataTableSolVarias').DataTable({
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

function encryptData(data) {
    return btoa(data);
}

function recargarSoli () {
    location.reload(true);
}

function redirectToSolicitud(id_solicitud) {
    const encryptedSolicitud = encryptData(id_solicitud);
    const encodedSolicitud = encodeURIComponent(encryptedSolicitud); // Codifica el valor para usar en la URL
    const redirect = `${baseURL}ver_solicitud?solicitud=${encodedSolicitud}`;
    console.log("Redireccionando a:", redirect); // Verifica la URL en la consola
    window.location.href = redirect; // Redirige a la URL construida
}

function redirectToCopySolicitud(id_solicitud) {
    const encryptedSolicitud = encryptData(id_solicitud);
    const encodedSolicitud = encodeURIComponent(encryptedSolicitud); // Codifica el valor para usar en la URL
    const redirect = `${baseURL}copiar_solicitud?solicitud=${encodedSolicitud}`;
    console.log("Redireccionando a:", redirect); // Verifica la URL en la consola
    window.location.href = redirect; // Redirige a la URL construida
}

function redirectToSolicitudDocSol(id_solicitud) {
    const encryptedSolicitud = encryptData(id_solicitud);
    const encodedSolicitud = encodeURIComponent(encryptedSolicitud); // Codifica el valor para usar en la URL
    const redirect = `${baseURL}documentos?solicitud=${encodedSolicitud}`;
    console.log("Redireccionando a:", redirect); // Verifica la URL en la consola
    window.location.href = redirect; // Redirige a la URL construida
}

function descargarContrato(numero_solicitud) {

    // Mostrar el modal con la cuenta regresiva
    $('#modalDescarga').modal('show');

    var countdown = 5;
    var interval = setInterval(function () {
        countdown--;
        $('#countdown').text(countdown); // Actualizar el contador en el modal

        if (countdown === 0) {
            clearInterval(interval);
            $('#modalDescarga').modal('hide');

            // Descargar el archivo
            var fileUrl = baseURL + 'archivo/descargar/' + numero_solicitud;
            window.open(fileUrl, '_blank'); // Abrir la descarga en una nueva ventana
        }
    }, 1000); // Intervalo de 1 segundo

}

function generarContrato(numeroSolicitud) {
    Swal.fire({
        title: '¿Deseas generar un contrato?',
        text: "Se creará un nuevo contrato para esta solicitud.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, generar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Iniciar la solicitud AJAX
            $.ajax({
                url: baseURL + 'generar_contrato/' + numeroSolicitud,
                method: 'GET',
                success: function (response) {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: 'Contrato generado',
                        text: 'El contrato ha sido generado con éxito.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Recargar la página para reflejar los cambios
                        location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    // Manejar errores
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al generar el contrato. Por favor, intenta nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}



function verObservacion(observacion) {
    console.log(observacion);
}