$(document).ready(function () {
    $('#dataTableSolCreadas').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false // Desactiva el buscador
    });

    $('#dataTableSolVariasTab').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false // Desactiva el buscador
    });

    cargarSolicitudesCreadas();
    cargarSolicitudesVarias();
});

function cargarSolicitudesCreadas() {
    $.ajax({
        url: baseURL + "getSolicitudXSucursalCreadas",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.status === 'success') {
                pintarTablaSolicitudesCreadas(response.data);
            } else {
                console.error("Error del servidor:", response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", error);
        }
    });
}

function pintarTablaSolicitudesCreadas(solicitudes) {
    let tbody = $('#dataTableSolCreadas tbody');
    tbody.empty();

    solicitudes.forEach((solicitud, index) => {
        const estado = parseInt(solicitud.id_estado_actual);
        let color = '', icono = '';
        switch (estado) {
            case 1:
                color = 'blue';
                icono = '<i class="fa-solid fa-check"></i>';
                break;
            case 2:
                color = 'green';
                icono = '<i class="fa-solid fa-check-double"></i>';
                break;
            case 3:
            case 4:
                color = 'red';
                icono = '<i class="fa-solid fa-ban"></i>';
                break;
            case 5:
                color = '#FFA500';
                icono = '<i class="fa-solid fa-check-double"></i>';
                break;
        }

        const estadoHTML = `<span style="color: ${color};">${solicitud.estado} ${icono}</span>`;

        const modalID = `verObservaciones${index + 1}`;
        const modalHTML = solicitud.observacion ? `
            <div class="modal fade" id="${modalID}" tabindex="-1" aria-labelledby="${modalID}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="${modalID}Label">Observación solicitud ${solicitud.numero_solicitud}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <textarea class="form-control" rows="3" disabled>${solicitud.observacion}</textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>` : '';

        const acciones = `
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton${solicitud.id_solicitud}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Acciones
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton${solicitud.id_solicitud}">
                    <a class="dropdown-item" href="#" onclick="redirectToCopySolicitud('${solicitud.id_solicitud}')">
                        <i class="fa-solid fa-copy"></i> Copiar solicitud
                    </a>
                    <a class="dropdown-item" href="#" onclick="redirectToSolicitud('${solicitud.id_solicitud}')">
                        <i class="fas fa-eye"></i> Ver solicitud
                    </a>
                    <a class="dropdown-item" href="#" onclick="redirectToSolicitudDocSol('${solicitud.id_solicitud}')">
                        <i class="fa-solid fa-download"></i> Descargar solicitud de crédito
                    </a>
                    ${solicitud.observacion ? `
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#${modalID}">
                        <i class="fa-regular fa-message"></i> Ver observación
                    </a>` : ''}
                    ${solicitud.num_contrato ?
                `<a class="dropdown-item" href="#" onclick="descargarContrato('${solicitud.id_solicitud}')">
                            <i class="fa-solid fa-file-arrow-down"></i> Descargar contrato
                        </a>` :
                `<a class="dropdown-item" href="#" onclick="generarContrato('${solicitud.id_solicitud}')">
                            <i class="fa-solid fa-file-circle-plus"></i> Generar contrato
                        </a>`}
                    <a class="dropdown-item" href="#" onclick="getDataPagare('${solicitud.id_solicitud}')">
                        <i class="fa-solid fa-copy"></i> Generar Pagare
                    </a>
                </div>
            </div>`;

        const fila = `
            <tr>
                <td>${solicitud.numero_solicitud || ''}</td>
                <td>${solicitud.dui || ''}</td>
                <td>${solicitud.nombre_completo || ''}</td>
                <td>${solicitud.fecha_creacion || ''}</td>
                <td>${estadoHTML}</td>
                <td>${solicitud.user_creador || ''}</td>
                <td>${acciones}</td>
            </tr>
            ${modalHTML}
        `;

        tbody.append(fila);
    });
}

function cargarSolicitudesVarias() {
    $.ajax({
        url: baseURL + "getSolicitudXSucursalVarias",
        method: "GET",
        dataType: "json",
        success: function (response) {
            renderTablaSolicitudesVarias(response.data);
        },
        error: function () {
            alert('Ocurrió un error al comunicarse con el servidor.');
        }
    });
}

function renderTablaSolicitudesVarias(data) {
    console.log("Log renderTablaSolicitudesVarias ", data);
    if ($.fn.DataTable.isDataTable('#dataTableSolVariasTab')) {
        $('#dataTableSolVariasTab').DataTable().destroy();
    }
    const tbody = $('#dataTableSolVarias');
    tbody.empty();

    let contador = 1;

    data.forEach(solicitud => {
        let colorEstado = '';
        let iconoEstado = '';

        // Determina el color y el icono según el estado
        switch (parseInt(solicitud.id_estado_actual)) {
            case 1:
                colorEstado = 'blue';
                iconoEstado = '<i class="fa-solid fa-check"></i>';
                break;
            case 2:
                colorEstado = 'green';
                iconoEstado = '<i class="fa-solid fa-check-double"></i>';
                break;
            case 3:
            case 4:
                colorEstado = 'red';
                iconoEstado = '<i class="fa-solid fa-ban"></i>';
                break;
            case 5:
                colorEstado = '#FFA500';
                iconoEstado = '<i class="fa-solid fa-check-double"></i>';
                break;
        }

        const modalId = `verObservaciones${contador}`;
        const estadoHTML = `<span style="color: ${colorEstado};">${solicitud.estado} ${iconoEstado}</span>`;
        const observacionModal = solicitud.observacion ? ` 
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#${modalId}" onclick="verObservacion('${solicitud.observacion}')">
                <i class="fa-regular fa-message"></i> Ver observación
            </a>
            <div class="modal fade" id="${modalId}" tabindex="-1" aria-labelledby="${modalId}Label" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="${modalId}Label">Observación solicitud ${solicitud.numero_solicitud}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Observaciones realizadas</label>
                                <textarea class="form-control" rows="3" disabled>${solicitud.observacion}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        ` : '';

        const contratoLink = solicitud.num_contrato ? ` 
            <a class="dropdown-item" href="#" onclick="descargarContrato('${solicitud.id_solicitud}')">
                <i class="fa-solid fa-file-arrow-down"></i> Descargar contrato
            </a>
        ` : '';

        const fila = ` 
            <tr>
                <td style="display:none;">${solicitud.id_solicitud}</td>
                <td>${solicitud.numero_solicitud}</td>
                <td>${solicitud.dui}</td>
                <td>${solicitud.nombre_completo}</td>
                <td>${solicitud.fecha_creacion}</td>
                <td>${estadoHTML}</td>
                <td>${solicitud.user_creador}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton${solicitud.id_solicitud}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Acciones
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton${solicitud.id_solicitud}">
                            <a class="dropdown-item" href="#" onclick="redirectToCopySolicitud('${solicitud.id_solicitud}')">
                                <i class="fa-solid fa-copy"></i> Copiar solicitud
                            </a>
                            <a class="dropdown-item" href="#" onclick="redirectToSolicitud('${solicitud.id_solicitud}')">
                                <i class="fas fa-eye"></i> Ver solicitud
                            </a>
                            <a class="dropdown-item" href="#" onclick="redirectToSolicitudDocSol('${solicitud.id_solicitud}')">
                                <i class="fa-solid fa-download"></i> Descargar solicitud de crédito
                            </a>
                            ${observacionModal}
                            ${contratoLink}
                            <a class="dropdown-item" href="#" onclick="getDataPagare('${solicitud.id_solicitud}')">
                                <i class="fa-solid fa-copy"></i> Generar Pagare
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        `;

        tbody.append(fila);
        contador++;
    });

    $('#dataTableSolVariasTab').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false,
        "order": [[0, "desc"]]
    });
}

function generarPagare(data) {
    console.log(data);
    const cliente = data.datosClientes.data;
    const solicitud = data.dataSol.data;
    const sucursal = data.sucursal.data;

    const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    const numerosEnTexto = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete',
        'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós', 'veintitrés',
        'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho',
        'veintinueve', 'treinta', 'treinta y uno'
    ];

    // ======= FECHA DEL CONTRATO =======
    const fecha_contrato = new Date(data.dataSol.data.fecha_creacion);
    const diaNum = fecha_contrato.getDate();
    const mesNum = fecha_contrato.getMonth(); // 0-11
    const anioStr = fecha_contrato.getFullYear().toString().slice(-2);
    const anioNum = parseInt(anioStr, 10);

    const diaC = numerosEnTexto[diaNum];
    const mesC = meses[mesNum];
    const anioC = numerosEnTexto[anioNum];

    // ======= FECHA DEL PRIMER PAGO =======
    //const fecha_prime_pago = new Date(data.primerCobro.data.fecha_vencimiento);
    const fecha_prime_pago = new Date(data.dataSol.data.fecha_creacion);
    const diaPPNum = fecha_prime_pago.getDate();
    const mesPPNum = fecha_prime_pago.getMonth() +1;
    const anioPPStr = fecha_prime_pago.getFullYear().toString().slice(-2);
    const anioPPNum = parseInt(anioPPStr, 10);

    const diaPP = numerosEnTexto[diaPPNum];
    const mesPP = meses[mesPPNum];
    const anioPP = numerosEnTexto[anioPPNum];

    const monto = solicitud.monto_sin_prima || "__________";
    const montoNumero = parseFloat(monto);
    const montoLetras = isNaN(montoNumero) ? "__________________" : numeroALetras(montoNumero);
    const nombre = cliente.nombre_completo || "";
    const dui = cliente.dui || "";
    const nit = "__________"; // Si tienes NIT agrégalo aquí
    const direccion = cliente.direccion || "";
    const telefono = cliente.telefono || "";
    const correo = cliente.correo || "";
    const departamento = sucursal.nombre_departamento || "";
    const municipio = sucursal.nombre_municipio || "";
    const distrito = sucursal.nombre_distrito || "";
    const colonia = sucursal.nombre_colonia || "";
    const contenido = `
        <style>
            .pagare-container {
                font-family: 'Arial', sans-serif;
                line-height: 1.5;
                color: black;
                padding: 20px;
            }
            .pagare-title {
                text-align: center;
                font-weight: bold;
                color: #003366;
                font-size: 18px;
                margin-bottom: 10px;
                text-transform: uppercase;
            }
            .text-right {
                text-align: right;
            }
            .field-line {
                border-bottom: 1px solid black;
                display: inline-block;
                width: 200px;
                height: 14px;
            }
            .signature-line {
                border-bottom: 1px solid black;
                display: inline-block;
                width: 150px;
            }
                .texto-mayusculas {
                    text-transform: uppercase;
                }
                    .pagare-container {
        font-family: 'Arial', sans-serif;
        line-height: 1.5;
        color: black;
        padding: 20px;
        text-align: justify; /* 👈 Justifica el contenido */
    }
        </style>

        <div class="pagare-container">
            <div class="pagare-title">PAGARÉ SIN PROTESTO</div>
            <div class="text-right"><strong>POR: $${monto}/100</strong></div>

            <p>
                Por medio del presente PAGARÉ SIN PROTESTO, me obligo a pagar de manera incondicional y solidaria a la orden de
                <strong>ALEX NORBERTO PÉREZ MAYORGA</strong> la cantidad de <strong class="texto-mayusculas">${montoLetras} DE LOS ESTADOS UNIDOS DE AMÉRICA.</strong>
                Pagaderos a partir del día <strong class="texto-mayusculas">${diaPP}</strong> del mes <strong class="texto-mayusculas">${mesPP}</strong> del año dos mil <strong class="texto-mayusculas">${anioPP}</strong>.
                El pago se hará en las oficinas situadas en el distrito de <strong class="texto-mayusculas">${distrito}</strong>,
                municipio de <strong class="texto-mayusculas">${municipio}</strong>, departamento de <strong class="texto-mayusculas">${departamento}</strong>
                dirección que es pleno conocimiento del parte deudor(a).
            </p>

            <p>
                En caso de mora, reconoceré un interés del cinco punto sesenta y cinco por ciento mensual sobre saldos vencidos. Para todos los efectos de esta obligación mercantil, en caso de acción judicial, fijo como domicilio especial el distrito de San Salvador, municipio de San Salvador Centro, departamento de San Salvador.
            </p>

            <p>
                Y siendo a mi cargo, todos los gastos en que <strong class="texto-mayusculas">${nombre}</strong> tuviera que incurrir para el cobro de este Pagaré, en cualquier concepto, incluidos los de cancelación y de cobranza judiciales o extrajudiciales, inclusive los 
                llamados personales y aun cuando por regla general no fuere condenado a ello, faculto al acreedor señor ALEX NORBERTO PÉREZ MAYORGA, para que designe y nombre a 
                la persona depositaria de los bienes que se embarguen, a quien relevo de la obligación de rendir fianza para ejercer su cargo.
            </p>

            <p>
                En el distrito de <strong class="texto-mayusculas">${distrito}</strong>, municipio de <strong class="texto-mayusculas">${municipio}</strong>,
                departamento de <strong class="texto-mayusculas">${departamento}</strong> a los <strong class="texto-mayusculas">${diaC}</strong> días, del mes de
                <strong class="texto-mayusculas">${mesC}</strong> del año dos mil <strong class="texto-mayusculas">${anioC}</strong>.
            </p>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <tbody>
                    <tr>
                        <td style="width: 10%;"><strong>Nombre:</strong></td>
                        <td style="width: 35%;"><strong class="texto-mayusculas">${nombre}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>DUI:</strong></td>
                        <td style="width: 35%;"><strong></span>${dui}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>NIT:</strong></td>
                        <td style="width: 35%;"><strong>${dui}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>Dirección:</strong></td>
                        <td style="width: 35%;"><strong>${direccion}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>Teléfono:</strong></td>
                        <td style="width: 35%;"><strong>${telefono}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>Correo electrónico:</strong></td>
                        <td style="width: 35%;"><strong>${correo}</strong></td>
                    </tr>
                    <tr>
                        <td style="width: 10%;"><strong>Referencia crediticia:</strong></td>
                        <td style="width: 35%;"><strong></strong></td>
                    </tr>
                    <tr class="mt-5">
                        <td style="width: 10%;"><strong>Firma:</strong></td>
                        <td style="width: 35%;"><strong><span class="signature-line" style="width: 150px; display: inline-block;"></span></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <div style="text-align: right; margin-bottom: 10px;">
    <button onclick="imprimirPagare()" style="padding: 6px 12px; font-size: 14px;">🖨️ Imprimir</button>
</div>
    `;

    $('#modalPagareLabel').text(`Pagaré - Solicitud ${solicitud.numero_solicitud}`);
    $('#modalPagareBody').html(contenido);
    $('#modalPagare').modal('show');
}

async function imprimirPagare() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const elemento = document.querySelector('.pagare-container');
    const opciones = {
        margin: 0.5,
        filename: 'pagare-container.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    await html2pdf().set(opciones).from(elemento).save();

    Swal.close();
}

function numeroALetras(num) {
    const UNIDADES = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve'
    ];
    const DECENAS = [
        '', 'diez', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta',
        'setenta', 'ochenta', 'noventa'
    ];
    const DIEZ_A_DIECINUEVE = [
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve'
    ];
    const CENTENAS = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos', 'quinientos',
        'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];

    function convertirGrupo(n) {
        let output = '';
        const centenas = Math.floor(n / 100);
        const decenas = Math.floor((n % 100) / 10);
        const unidades = n % 10;

        if (n === 100) return 'cien';

        if (centenas > 0) {
            output += CENTENAS[centenas] + ' ';
        }

        if (decenas === 1) {
            output += DIEZ_A_DIECINUEVE[unidades];
        } else {
            if (decenas > 0) {
                output += DECENAS[decenas];
                if (unidades > 0) {
                    output += ' y ' + UNIDADES[unidades];
                }
            } else if (unidades > 0) {
                output += UNIDADES[unidades];
            }
        }

        return output.trim();
    }

    function convertirEntero(num) {
        let n = parseInt(num);
        if (n === 0) return 'cero';
        let millones = Math.floor(n / 1000000);
        let miles = Math.floor((n % 1000000) / 1000);
        let cientos = n % 1000;
        let texto = '';

        if (millones > 0) {
            texto += (millones === 1 ? 'un millón' : convertirGrupo(millones) + ' millones') + ' ';
        }

        if (miles > 0) {
            texto += (miles === 1 ? 'mil' : convertirGrupo(miles) + ' mil') + ' ';
        }

        if (cientos > 0) {
            texto += convertirGrupo(cientos);
        }

        return texto.trim();
    }

    function convertirCentavos(num) {
        return convertirGrupo(num);
    }

    const [parteEntera, parteDecimal] = num.toFixed(2).split('.');
    const enteroTexto = convertirEntero(parseInt(parteEntera));
    const centavosTexto = parseInt(parteDecimal) > 0
        ? ' con ' + convertirCentavos(parseInt(parteDecimal)) + ' centavos'
        : '';

    return `${enteroTexto} dólares${centavosTexto}`;
}


function encryptData(data) {
    return btoa(data);
}

function recargarSoli() {
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
    console.log("el encryptedSolicitud: ",encryptedSolicitud);
    const encodedSolicitud = encodeURIComponent(encryptedSolicitud); // Codifica el valor para usar en la URL
    console.log("el encodedSolicitud: ",encodedSolicitud);
    const redirect = `${baseURL}documentos?solicitud=${encodedSolicitud}`;
    console.log("Redireccionando a:", redirect); // Verifica la URL en la consola
    window.location.href = redirect; // Redirige a la URL construida
}

function descargarContrato(id_solicitud) {

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
            var fileUrl = baseURL + 'archivo/descargar/' + id_solicitud;
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

function getDataPagare(id_solicitud) {
    $.ajax({
        url: baseURL + 'getDataClientePagare',
        type: 'POST',
        data: { id_solicitud: id_solicitud },
        dataType: "json",
        success: function (response) {
            console.log("El valor del response en gtedataPagare::: ",response);
            if (response.success) {
                generarPagare(response); // Llama a la función callback con los datos
            } else {
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudieron obtener los datos del cliente.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function () {
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al generar el pagaré. Por favor, intenta nuevamente.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}