document.addEventListener('DOMContentLoaded', function () {
    configurarFechaNacimiento();
    cargarDepartamentos();

    $('#deptoClienteCN').on('change', function () {
        var deptoSeleccionado = $(this).val();
        cargarMunicipios(deptoSeleccionado);
    });

    $('#muniClienteCN').on('change', function () {
        var muniSelect = $(this).val();
        cargarDistrito(muniSelect);
        //cargarColonias(muniSelect);
    });

    $('#distritoClienteCN').on('change', function () {
        var distrito = $(this).val();
        cargarColonias(distrito);
    });

    document.getElementById('CpropiaCN').addEventListener('change', manejarCambioCpropiaCN);

    manejarCambioCpropiaCN();
    manejarCamposConyuge();
});

function manejarCambioCpropiaCN() {
    var esPropia = document.getElementById('CpropiaCN').value === 'SI';

    var CpromesaVentaCN = document.getElementById('CpromesaVentaCN');
    var CalquiladaCN = document.getElementById('CalquiladaCN');
    var aQuienPerteneceCN = document.getElementById('aQuienPerteneceCN');
    var telPropietarioCN = document.getElementById('telPropietarioCN');

    if (esPropia) {
        // Establecer el valor "NO" y deshabilitar los select
        CpromesaVentaCN.value = 'NO';
        CalquiladaCN.value = 'NO';
        CpromesaVentaCN.disabled = true;
        CalquiladaCN.disabled = true;

        // Limpiar y establecer readonly en los inputs
        aQuienPerteneceCN.value = 'N/A';
        telPropietarioCN.value = '0000-0000';
        aQuienPerteneceCN.readOnly = true;
        telPropietarioCN.readOnly = true;

        // Eliminar el atributo required
        aQuienPerteneceCN.removeAttribute('required');
        telPropietarioCN.removeAttribute('required');
    } else {
        // Habilitar los select y quitar readonly de los inputs
        CpromesaVentaCN.disabled = false;
        CalquiladaCN.disabled = false;
        aQuienPerteneceCN.readOnly = false;
        telPropietarioCN.readOnly = false;

        // Volver a agregar el atributo required si es necesario
        aQuienPerteneceCN.setAttribute('required', true);
        telPropietarioCN.setAttribute('required', true);
    }
}

function cargarDepartamentos() {
    $.ajax({
        type: 'GET',
        url: baseURL + 'departamentos',
        dataType: 'json',
        success: function (response) {
            var select = $('#deptoClienteCN');
            select.empty();

            // Agregar opción predeterminada
            select.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));

            // Agregar opciones de departamentos
            response.departamentos.forEach(function (depto) {
                var option = $('<option></option>')
                    .attr('value', depto.id)
                    .text(depto.nombre);
                select.append(option);
            });

            // Obtener el valor del departamento seleccionado desde el atributo data-*
            var departamentoSeleccionado = select.data('depto-seleccionado');
            if (departamentoSeleccionado !== -1) {
                select.val(departamentoSeleccionado); // Establecer el valor del select
                cargarMunicipios(departamentoSeleccionado); // Cargar municipios para el departamento seleccionado
            }
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los departamentos:", status, error);
        }
    });
}


function cargarMunicipios(deptoId) {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando municipios...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: 'POST',
        url: baseURL + 'municipios',
        data: { departamento_id: deptoId },
        dataType: 'json',
        success: function (data) {
            var selectMunicipios = $('#muniClienteCN');
            selectMunicipios.empty();

            selectMunicipios.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));

            data.forEach(function (municipio) {
                var option = $('<option></option>')
                    .attr('value', municipio.id)
                    .text(municipio.nombre);
                selectMunicipios.append(option);
            });

            var municipioSeleccionado = selectMunicipios.data('muni-seleccionado');
            if (municipioSeleccionado !== -1) {
                selectMunicipios.val(municipioSeleccionado); // Establecer el valor del select
                cargarDistrito(municipioSeleccionado);
            }

            Swal.close();
        },
        error: function (xhr, status, error) {
            Swal.close();
            console.error("Error al cargar los municipios:", status, error);
        }
    });
}

function cargarDistrito(municipio_id) {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando distritos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: 'POST',
        url: baseURL + 'distritos',
        data: { municipio_id: municipio_id },
        dataType: 'json',
        success: function (data) {
            var selectDistrito = $('#distritoClienteCN');
            selectDistrito.empty();

            selectDistrito.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));

            data.forEach(function (distrito) {
                var option = $('<option></option>')
                    .attr('value', distrito.id_distrito)
                    .text(distrito.nombre);
                selectDistrito.append(option);
            });

            var distritoSeleccionado = selectDistrito.data('distrito-seleccionado');
            if (distritoSeleccionado !== -1) {
                selectDistrito.val(distritoSeleccionado);
                cargarColonias(distritoSeleccionado); // Cargar municipios para el departamento seleccionado
            }

            Swal.close();
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los colonias:", status, error);
            Swal.close();
        }
    });
}

function cargarColonias(id_distrito) {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando colonias...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: 'POST',
        url: baseURL + 'getColonias',
        data: { id_distrito: id_distrito },
        dataType: 'json',
        success: function (data) {
            var selectColonias = $('#coloniaClienteCN');
            selectColonias.empty();

            selectColonias.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));

            data.forEach(function (colonia) {
                var option = $('<option></option>')
                    .attr('value', colonia.id)
                    .text(colonia.nombre);
                selectColonias.append(option);
            });

            var coloniaSelecionada = selectColonias.data('colonia-seleccionado');
            if (coloniaSelecionada !== -1) {
                selectColonias.val(coloniaSelecionada); // Establecer el valor del select
            }

            Swal.close();
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los colonias:", status, error);
            Swal.close();
        }
    });
}

function configurarFechaNacimiento() {
    var fechaNacimientoInput = document.getElementById('fechaNacimientoCN');
    var fechaMaxima = new Date();
    fechaMaxima.setFullYear(fechaMaxima.getFullYear() - 18);
    var fechaMaximaFormato = fechaMaxima.toISOString().split('T')[0];
    fechaNacimientoInput.setAttribute('max', fechaMaximaFormato);
}

function obtenerValor(campoId) {
    return document.getElementById(campoId).value.trim();
}

// ✅ Resaltar los campos vacíos
function resaltarCampoVacio(campoId, activar) {
    var campo = document.getElementById(campoId);
    if (activar) {
        campo.style.border = "2px solid red";
    } else {
        campo.style.border = "1px solid #ccc";
    }
}

function validarCamposCasado() {
    var nombreConyugue = obtenerValor("nombreConyugueCN");
    var dirTrabajoConyugue = obtenerValor("dirTrabajoConyugueCN");
    var telTrabajoConyugue = obtenerValor("telTrabajoConyugueCN");

    var camposVacios = nombreConyugue === "" || dirTrabajoConyugue === "" || telTrabajoConyugue === "";

    resaltarCampoVacio("nombreConyugueCN", nombreConyugue === "");
    resaltarCampoVacio("dirTrabajoConyugueCN", dirTrabajoConyugue === "");
    resaltarCampoVacio("telTrabajoConyugueCN", telTrabajoConyugue === "");

    return camposVacios;
}

// ✅ Manejar los campos del cónyuge según el estado civil
function manejarCamposConyuge() {
    var estadoCivil = obtenerValor("estadoCivilCN");
    var inputsConyuge = ["nombreConyugueCN", "dirTrabajoConyugueCN", "telTrabajoConyugueCN"];

    if (estadoCivil === "Casada/o" || estadoCivil === "Acompañada/o") {
        // Habilitar los campos del cónyuge
        inputsConyuge.forEach(function (campoId) {
            var campo = document.getElementById(campoId);
            campo.removeAttribute("readonly");
            campo.removeAttribute("disabled");
        });
    } else if(estadoCivil != '-1'){
        // Deshabilitar los campos y limpiar sus valores
        inputsConyuge.forEach(function (campoId) {
            var campo = document.getElementById(campoId);
            campo.setAttribute("readonly", true);
            campo.setAttribute("disabled", true);
            campo.value = ""; // Limpia el campo
            resaltarCampoVacio(campoId, false); // Elimina el borde rojo si estaba resaltado
        });
    }
}

// ✅ Evento para manejar cambios en el select de estado civil
document.getElementById("estadoCivilCN").addEventListener("change", manejarCamposConyuge);

function validarDatos() {
    var validador = false;

    var idPersonaEditar = obtenerValor("idPersonaEditar");

    var estadoCivil = obtenerValor("estadoCivilCN");
    manejarCamposConyuge(estadoCivil);

    var camposFormulario = [
        "nombrePersonalCN", "duiPersonal", "fechaNacimientoCN", "direccionActualCN",
        "deptoClienteCN", "muniClienteCN", "coloniaClienteCN", "estadoCivilCN", "telPersonal", "correoCN",
        "CpropiaCN", "aQuienPerteneceCN", "telPropietarioCN", "tiempoDeVivirDomicilioCN"
    ];

    camposFormulario.forEach(function (campoId) {
        var valorCampo = obtenerValor(campoId);

        if (campoId === "estadoCivilCN" && (valorCampo === "Casada/o" || valorCampo === "Acompañada/o")) {
            if (validarCamposCasado()) {
                validador = true;
            }
        } else if (valorCampo === "") {
            resaltarCampoVacio(campoId, true);
            validador = true;
        } else {
            resaltarCampoVacio(campoId, false);
        }
    });

    var esPropia = document.getElementById('CpropiaCN').value === 'SI';
    var esPromesaVenta = document.getElementById('CpromesaVentaCN').value === 'SI';
    var esAlquilada = document.getElementById('CalquiladaCN').value === 'SI';

    if (esPropia) {
        CpromesaVentaCN.required = false;
        CalquiladaCN.required = false;
        resaltarCampoVacio("CpromesaVentaCN", false);
        resaltarCampoVacio("CalquiladaCN", false);
    } else {
        CpromesaVentaCN.required = true;
        CalquiladaCN.required = true;
        resaltarCampoVacio("CpromesaVentaCN", true);
        resaltarCampoVacio("CalquiladaCN", true);
    }

    if (esPromesaVenta || esAlquilada) {
        ["aQuienPerteneceCN", "telPropietarioCN", "tiempoDeVivirDomicilioCN"].forEach(function (campoId) {
            var valorCampo = obtenerValor(campoId);
            if (valorCampo === "") {
                resaltarCampoVacio(campoId, true);
                validador = true;
            } else {
                resaltarCampoVacio(campoId, false);
            }
        });
    } else if (esPropia) {
        ["aQuienPerteneceCN", "telPropietarioCN", "tiempoDeVivirDomicilioCN"].forEach(function (campoId) {
            resaltarCampoVacio(campoId, false);
        });
    }

    if (validador) {
        $(".alert-danger").removeClass("d-none");
    } else {
        $(".alert-danger").addClass("d-none");
        Swal.fire({
            title: 'Espere...',
            html: 'Ingresando datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var data = {
            idPersonaEditar: idPersonaEditar,
            nombrePersonalCN: obtenerValor("nombrePersonalCN"),
            duiPersonal: obtenerValor("duiPersonal"),
            fechaNacimientoCN: obtenerValor("fechaNacimientoCN"),
            direccionActualCN: obtenerValor("direccionActualCN"),
            deptoClienteCN: obtenerValor("deptoClienteCN"),
            muniClienteCN: obtenerValor("muniClienteCN"),
            distritoClienteCN: obtenerValor("distritoClienteCN"),
            coloniaClienteCN: obtenerValor("coloniaClienteCN"),
            estadoCivilCN: obtenerValor("estadoCivilCN"),
            nombreConyugueCN: obtenerValor("nombreConyugueCN"),
            dirTrabajoConyugueCN: obtenerValor("dirTrabajoConyugueCN"),
            telTrabajoConyugueCN: obtenerValor("telTrabajoConyugueCN"),
            nombresPadresCN: obtenerValor("nombresPadresCN"),
            direccionDeLosPadresCN: obtenerValor("direccionDeLosPadresCN"),
            telPadresCN: obtenerValor("telPadresCN"),
            correoCN: obtenerValor("correoCN"),
            telPersonal: obtenerValor("telPersonal"),
            CpropiaCN: obtenerValor("CpropiaCN"),
            CpromesaVentaCN: (obtenerValor("CpromesaVentaCN") == '-1' ? 'NO' : obtenerValor("CpromesaVentaCN")),
            CalquiladaCN: (obtenerValor("CalquiladaCN") == '-1' ? 'NO' : obtenerValor("CalquiladaCN")),
            aQuienPerteneceCN: obtenerValor("aQuienPerteneceCN"),
            telPropietarioCN: obtenerValor("telPropietarioCN"),
            tiempoDeVivirDomicilioCN: obtenerValor("tiempoDeVivirDomicilioCN")
        };

        guardarDatos(data);
    }
}



function guardarDatos(data) {
    var url = baseURL + (data.idPersonaEditar ? 'editar_cliente' : 'guardar_cliente');

    Swal.fire({
        title: 'Espere...',
        html: data.idPersonaEditar ? 'Actualizando cliente...' : 'Guardando cliente...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: 'POST',
        url: url,
        data: data,
        dataType: 'json',
        success: function (rsp) {
            Swal.close();
            console.log(rsp);
            if (rsp.success) {
                Swal.fire({
                    icon: 'success',
                    title: rsp.success,
                    text: 'Serás redirigido en unos segundos...',
                    timer: 1500,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then((result) => {
                    window.location.href = baseURL + 'clientes';
                });
            } else if (rsp.error) {
                Swal.close();
                toastr.error(rsp.error, "ERROR");
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al guardar los datos:', status, error);
            toastr.error("ERROR", "Error al guardar los datos");
            Swal.close();
        }
    });
}
