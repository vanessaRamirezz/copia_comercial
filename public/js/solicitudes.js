document.addEventListener("DOMContentLoaded", function () {
    console.log("Ver solicitudes");
    $('#dataTableSol').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
    });
});

function encryptData(data) {
    return btoa(data);
}

function redirectToSolicitud(id_solicitud) {
    const encryptedSolicitud = encryptData(id_solicitud);
    const encodedSolicitud = encodeURIComponent(encryptedSolicitud); // Codifica el valor para usar en la URL
    const redirect = `${baseURL}ver_solicitud?solicitud=${encodedSolicitud}`;
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


function verObservacion(observacion) {
    console.log(observacion);
}