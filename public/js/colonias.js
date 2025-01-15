document.addEventListener('DOMContentLoaded', function () {
    cargarDepartamentos();

    $('#deptoClienteCN').on('change', function () {
        var deptoSeleccionado = $(this).val();
        cargarMunicipios(deptoSeleccionado);
    });

    $('#muniClienteCN').on('change', function () {
        var municipioSeleccionado = $(this).val();
        cargarDistrito(municipioSeleccionado);
    });

    $('#distritoClienteCN').on('change', function () {
        var distritoSeleccionado = $(this).val();
        if (distritoSeleccionado !== "-1") {
            $('#optionNewColonia').show();
        } else {
            $('#optionNewColonia').hide();
        }

        cargarColonias(distritoSeleccionado);
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
            Swal.close();
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los municipios:", status, error);
            Swal.close();
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
            var selectColonias = $('#coloniasCN');
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
            Swal.close();
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los colonias:", status, error);
            Swal.close();
        }
    });
}

function agregarColonia() {
    Swal.fire({
        title: 'Espere...',
        html: 'Agregando colonia...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    var nombreColonia = $('#textColonia').val();
    var id_distrito = $('#distritoClienteCN').val();

    if (nombreColonia === "" || id_distrito === "-1") {
        toastr.error("Debes ingresar un nombre para la colonia y seleccionar un municipio.", "ERROR");
        return;
    }

    $.ajax({
        type: 'POST',
        url: baseURL + 'colonias',
        data: {
            nombre: nombreColonia,
            id_distrito: id_distrito
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                toastr.success(response.msg, "Success");
            } else {
                toastr.error(response.msg, "ERROR");
            }
            cargarColonias(id_distrito);
            Swal.close();
        },
        error: function(xhr, status, error) {
            console.error("Error al guardar la colonia:", status, error);
            toastr.error("Ocurrió un error en la solicitud", "ERROR");
            Swal.close();
        }
    });
}
