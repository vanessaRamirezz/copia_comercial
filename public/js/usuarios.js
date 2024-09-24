document.addEventListener("DOMContentLoaded", function () {
    $('.dui').mask('00000000-0');
    console.log("Ready usuarios!");
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    cargarTablaUsuarios();

    $('#btnAgregarUsuarioModal').click(function () {
        $('.modalGuardar').show();
        $('.modalEditarUsuario').hide();
        // Limpiar campos del formulario
        $('#nombreUsuarioMtn').val('');
        $('#diuNew').val('').prop('disabled', false);
        $('#apellidosUsuarioMtn').val('');
        $('#emailUsuarioMtn').val('');
        $('#numTelefonoMtn').val('');

        // Mostrar modal de Bootstrap
        $('#mntUsuarios').modal('show');
    });

    $("#cerraMntUsuarios").click(() => {
        $('#mntUsuarios').modal('hide');
    })
});

function guardarNuevoUsuario(tipoSolicitud) {
    var nombres = $('#nombreUsuarioMtn').val().trim();
    var apellidos = $('#apellidosUsuarioMtn').val().trim();
    var email = $('#emailUsuarioMtn').val().trim();
    var telefono = $('#numTelefonoMtn').val().trim();
    var perfileSelect = $("#perfilesCmb").val();
    var dui = $("#duiNew").val().trim();

    const nameRegex = /^[a-zA-Z\s]+$/;
    const emailRegex = /^\S+@\S+\.\S+$/;
    const numberRegex = /^\d+$/;
    const duiRegex = /^\d{8}-\d}$/;

    var tipo_sol = tipoSolicitud === '1' ? 'postUsuarioNew' : 'updateInfoUser';

    if (dui === "") {
        toastr.info("El DUI es requerido", "Campo incompleto");
        return false;
    }
    if (nombres === "") {
        toastr.info("El nombre es requerido", "Campo incompleto");
        return false;
    }
    if (!nameRegex.test(nombres)) {
        toastr.info("El nombre solo debe contener letras", "Entrada inválida");
        return false;
    }
    if (apellidos === "") {
        toastr.info("El apellido es requerido", "Campo incompleto");
        return false;
    }
    if (!nameRegex.test(apellidos)) {
        toastr.info("El apellido solo debe contener letras", "Entrada inválida");
        return false;
    }
    if (email === "") {
        toastr.info("El correo es requerido", "Campo incompleto");
        return false;
    }
    if (!emailRegex.test(email)) {
        toastr.info("Por favor ingrese un correo válido", "Entrada inválida");
        return false;
    }
    if (telefono === "") {
        toastr.info("El número de teléfono es requerido", "Campo incompleto");
        return false;
    }
    if (!numberRegex.test(telefono)) {
        toastr.info("El número de teléfono solo debe contener dígitos", "Entrada inválida");
        return false;
    }
    if (perfileSelect === "-1") {
        toastr.info("Seleccione un perfil para el usuario", "Campo incompleto");
        return false;
    }

    // Si todas las validaciones pasan, continuar con el código aquí
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
        url: baseURL + tipo_sol,
        data: {
            "nombres": nombres,
            "apellidos": apellidos,
            "correo": email,
            "telefono": telefono,
            "perfil": perfileSelect,
            "duiNew": dui,
            "id_perfil": perfileSelect
        },
        dataType: "json",
        success: function (rsp) {
            if (rsp.success) {
                toastr.success(rsp.success, "Success");
                cargarTablaUsuarios();
            } else if (rsp.error) {
                Swal.close();
                toastr.error(rsp.error, "ERROR");
            }
        },
        error: function () {
            Swal.close();
            toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
        }, complete: function () {
            $('#mntUsuarios').modal('hide');
            setTimeout(function () {
                Swal.close();
            }, 2000);
        }
    });
}

function cargarTablaUsuarios() {
    var dataTable = document.getElementById("dataTable");
    if (dataTable) {
        $.ajax({
            type: 'GET',
            url: baseURL + 'getUsuarios',
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    var table = $(dataTable).DataTable();
                    table.clear().draw(); // Limpiar y volver a dibujar la tabla
                    data.success.forEach(function (row) {
                        var keysToShow = ['dui', 'nombres', 'apellidos', 'correo', 'telefono', 'tipo_perfil'];
                        var rowData = keysToShow.map(function (key) {
                            return row[key];
                        });

                        rowData.push('<button class="btn btn-info btn-sm btnVerOpciones">Ver Opciones</button>');
                        table.row.add(rowData).draw();
                    });

                    // Configurar evento de clic para mostrar modal con opciones
                    $(dataTable).off('click', '.btnVerOpciones').on('click', '.btnVerOpciones', function () {
                        $('.modalGuardar').hide();
                        $('.modalEditarUsuario').show();

                        // Obtener los datos de la fila seleccionada
                        var $row = $(this).closest('tr');
                        var rowData = table.row($row).data();
                        if (!rowData) return; // Salir si no hay datos en la fila

                        // Llenar los campos del formulario con los datos de la fila seleccionada
                        $('#duiNew').val(rowData[0]).prop('disabled', true);
                        $('#nombreUsuarioMtn').val(rowData[1]); // nombres
                        $('#apellidosUsuarioMtn').val(rowData[2]); // apellidos
                        $('#emailUsuarioMtn').val(rowData[3]); // correo
                        $('#numTelefonoMtn').val(rowData[4]); // telefono

                        // Verificar si el tipo_perfil está en la lista de opciones
                        var tipoPerfil = rowData[5];
                        var $selectPerfiles = $('#perfilesCmb');
                        $selectPerfiles.find('option').each(function () {
                            if ($(this).text().trim() === tipoPerfil.trim()) {
                                $(this).prop('selected', true);
                            } else {
                                $(this).prop('selected', false);
                            }
                        });

                        $('#mntUsuarios').modal('show');
                    });
                }
                else if (data.error) {
                    toastr.error("ERROR", data.error);
                }
            },
            error: function () {
                console.log("Error al cargar los datos de la tabla.");
            },
            complete: function () {
                setTimeout(function () {
                    Swal.close();
                }, 2000);
            }
        });
    }

}