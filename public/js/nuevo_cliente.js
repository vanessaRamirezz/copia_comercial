document.addEventListener('DOMContentLoaded', function () {
    configurarFechaNacimiento();
    cargarDepartamentos();

    $('#deptoClienteCN').on('change', function () {
        var deptoSeleccionado = $(this).val();
        cargarMunicipios(deptoSeleccionado);
    });
});

function cargarDepartamentos() {
    $.ajax({
        type: 'GET',
        url: baseURL + 'departamentos',
        dataType: 'json',
        success: function (response) {
            var select = $('#deptoClienteCN');
            select.empty();

            select.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));

            response.departamentos.forEach(function (depto) {
                var option = $('<option></option>')
                    .attr('value', depto.id)
                    .text(depto.nombre);
                select.append(option);
            });

            select.trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los departamentos:", status, error);
        }
    });
}

function cargarMunicipios(deptoId) {
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
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los municipios:", status, error);
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


function validarDatos() {
    console.log("entra clic");

    function obtenerValor(campoId) {
        return document.getElementById(campoId).value.trim();
    }

    function resaltarCampoVacio(campoId, vacio) {
        var campo = document.getElementById(campoId);
        campo.style.border = vacio ? "1px solid red" : "";
    }

    function validarCamposCasado() {
        var nombreConyugue = obtenerValor("nombreConyugueCN");
        var dirTrabajoConyugue = obtenerValor("dirTrabajoConyugueCN");
        var telTrabajoConyugue = obtenerValor("telTrabajoConyugueCN");
        var camposVacios = nombreConyugue === "" || dirTrabajoConyugue === "" || telTrabajoConyugue === "";
        resaltarCampoVacio("nombreConyugueCN", camposVacios);
        resaltarCampoVacio("dirTrabajoConyugueCN", camposVacios);
        resaltarCampoVacio("telTrabajoConyugueCN", camposVacios);
        return camposVacios;
    }

    var validador = false;

    var camposFormulario = [
        "nombrePersonalCN", "duiPersonal", "fechaNacimientoCN", "direccionActualCN",
        "deptoClienteCN", "muniClienteCN", "estadoCivilCN", "telPersonal", "correoCN",
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
        // Si la propiedad es propia, no se requieren ni CpromesaVentaCN ni CalquiladaCN
        CpromesaVentaCN.required = false;
        CalquiladaCN.required = false;
        resaltarCampoVacio("CpromesaVentaCN", false);
        resaltarCampoVacio("CalquiladaCN", false);
    } else {
        // Si la propiedad no es propia, se requieren ambas
        CpromesaVentaCN.required = true;
        CalquiladaCN.required = true;
        // Resaltar en rojo los campos CpromesaVentaCN y CalquiladaCN
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
                validador = false;
            }
        });
    } else if (esPropia) {
        // Si esPropia es verdadero, quitar el resaltado de los campos
        ["aQuienPerteneceCN", "telPropietarioCN", "tiempoDeVivirDomicilioCN"].forEach(function (campoId) {
            resaltarCampoVacio(campoId, false);
            validador = false;
        });
    }



    console.log(validador);
    if (validador) {
        $(".alert-danger").removeClass("d-none");
    } else {
        Swal.fire({
            title: 'Espere...',
            html: 'Ingresando datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        $(".alert-danger").addClass("d-none");

        var data = {
            nombrePersonalCN: obtenerValor("nombrePersonalCN"),
            duiPersonal: obtenerValor("duiPersonal"),
            fechaNacimientoCN: obtenerValor("fechaNacimientoCN"),
            direccionActualCN: obtenerValor("direccionActualCN"),
            deptoClienteCN: obtenerValor("deptoClienteCN"),
            muniClienteCN: obtenerValor("muniClienteCN"),
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
    console.log(data);
    $.ajax({
        type: 'POST',
        url: baseURL + 'guardar_cliente',
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
                    timer: 5000,
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
