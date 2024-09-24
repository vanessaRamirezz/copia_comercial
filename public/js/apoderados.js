$(document).ready(function () {
    console.log("Ready apoderados!");
    cargarTablaApoderado();
});

function openModal(tipoModal,item) {
    console.log(item);
    if (tipoModal == 1) {
        console.log("entro en el if");
        $('.modalEditarApoderado').hide();
        $('.modalGuardar').show();
        $('#duiApoderado').val('').prop('disabled', false);
        $('#nombre_apoderado').val('');
        $('#nombre_representante_legal').val('');
        $('#dui_representante_legal').val('').prop('disabled', false);
        $('#fecha_nacimiento_rLegal').val('');
        $('#fecha_nacimiento_apoderado').val('');
    } else {
        console.log("entro en el else");
        $('.modalGuardar').hide();
        $('.modalEditarApoderado').show();
        $('#nombre_apoderado').val(item.nombre_apoderado);
        $('#duiApoderado').val(item.dui_apoderado).prop('disabled', true);
        $('#nombre_representante_legal').val(item.representante_legal);
        $('#dui_representante_legal').val(item.dui_representante).prop('disabled', true);
        $('#idapoderado').val(item.idapoderado);
        $('#fecha_nacimiento_rLegal').val(item.fecha_nacimiento_rLegal);
        $('#fecha_nacimiento_apoderado').val(item.fecha_nacimiento_apoderado);
    }

    $('#mntApoderados').modal('show');
}
function cerraModal(){
    $('#mntApoderados').modal('hide');
}

function generateOptions(tipoMov) {
    var url = '';
    var mensaje = '';
    if (tipoMov == 1) { //se guarda
        url = 'apoderadosNew';
    } else {//se actuliza
        url = 'apoderadosUpdate';
    }

    var duiApoderado = $('#duiApoderado').val();
    var nombreApoderado = $('#nombre_apoderado').val();
    var fecha_nacimiento_apoderado = $('#fecha_nacimiento_apoderado').val();
    var nombreRepreLegal = $('#nombre_representante_legal').val();
    var duiRepre = $('#dui_representante_legal').val();
    var fecha_nacimiento_rLegal = $('#fecha_nacimiento_rLegal').val();
    var idapoderado = $('#idapoderado').val();

    if (duiApoderado.trim().length < 10) {
        mensaje = 'El DUI del apoderado es requerido';
    } else if (nombreApoderado.trim().length == 0) {
        mensaje = 'El nombre del apoderado es requerido';
    } else if (duiRepre.trim().length < 10) {
        mensaje = 'El DUI del representante es requerido';
    } else if (nombreRepreLegal.trim().length == 0) {
        mensaje = 'El nombre del representante es requerido';
    } else if (!validarEdad(fecha_nacimiento_apoderado)) {
        mensaje = 'El apoderado debe ser mayor de edad';
    }else if (!validarEdad(fecha_nacimiento_rLegal)) {
        mensaje = 'El representante legal debe ser mayor de edad';
    }

    if (mensaje.length > 0) {
        toastr.error(mensaje, "");
        return false;
    }
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
        type: 'POST',
        url: baseURL + url,
        data: {
            duiApoderado,
            nombreApoderado,
            nombreRepreLegal,
            duiRepre,
            tipoMov,
            idapoderado,
            fecha_nacimiento_apoderado,
            fecha_nacimiento_rLegal
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                toastr.success("Success", response.success);
                $('#mntApoderados').modal('hide');
            } else if (response.error) {
                toastr.error("Error", response.error);
            } else if (response.info) {
                toastr.info("Info", response.info);
            }
            cargarTablaApoderado();
            Swal.close();
        }
    })
}

function validarEdad(fecha_nacimiento) {

    // Convertir la fecha de nacimiento a un objeto Date
    var fechaNacimiento = new Date(fecha_nacimiento);

    // Obtener la fecha actual
    var fechaActual = new Date();

    // Calcular la diferencia en años
    var edad = fechaActual.getFullYear() - fechaNacimiento.getFullYear();

    // Ajustar la diferencia si el cumpleaños no ha ocurrido este año
    var mes = fechaActual.getMonth() - fechaNacimiento.getMonth();
    if (mes < 0 || (mes === 0 && fechaActual.getDate() < fechaNacimiento.getDate())) {
        edad--;
    }

    // Validar si es mayor o igual a 18 años
    if (edad >= 18) {
        return true;  // La edad es válida
    } else {
        return false;  // La edad no es válida
    }
}
function cargarTablaApoderado() {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando apoderados...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: 'get',
        url: baseURL + 'getApoderado',
        dataType: 'json',
        success: function (response) {
            console.log(response);
            if (response.success) {
                // Limpiar el contenido actual del tbody
                $('#dataTable tbody').empty();

                // Iterar sobre los datos recibidos y agregarlos a la tabla
                $.each(response.data, function (index, item) {
                    $('#dataTable tbody').append(
                        '<tr>' +
                        '<td>' + item.nombre_apoderado + '</td>' +
                        '<td>' + item.dui_apoderado + '</td>' +
                        '<td>' + item.representante_legal + '</td>' +
                        '<td>' + item.dui_representante + '</td>' +
                        '<td>' +
                        '<button class="btn btn-sm btn-primary" onclick=\'openModal(2, ' + JSON.stringify(item).replace(/'/g, "\\'") + ')\'>Editar</button>' +
                        '</td>' +
                        '</tr>'
                    );
                });

                $('#mntApoderados').modal('hide');
            } else if (response.error) {
                toastr.error("Error", response.error);
            }
            Swal.close();
        }
    })
}