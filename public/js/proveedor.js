document.addEventListener("DOMContentLoaded", function () {
    console.log("Ready proveedor!");
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    cargarTablaCategorias();

    $('#nuevoProveedorBtn').click(function () {
        $('.modalGuardar').show();
        $('.modalEditarProveedor').hide();
        $('.estado').hide();
        // Limpiar campos del formulario
        $('#nombre').val('');
        $('#contacto').val('');
        $('#telefono').val('');
        $('#email').val('');
        $('#direccion').val('');

        // Mostrar modal de Bootstrap
        $('#nuevoProveedorModal').modal('show');
    });

    $("#cerrarModalProveedor").click(() => {
        $('#nuevoProveedorModal').modal('hide');
    });

    $('#estado').change(function(){
        if($(this).val() == "0") {
            $('.alertEstadoInactivo').show();
        } else {
            $('.alertEstadoInactivo').hide();
        }
    });
});

function procesoProveedor(tipoSolicitud) {
    var nombre = $('#nombre').val().trim();
    var contacto = $('#contacto').val().trim();
    var email = $('#email').val().trim();
    var telefono = $('#telefono').val().trim();
    var direccion = $('#direccion').val().trim();
    var estado = $('#estado').val().trim();
    var id_proveedor = $('#id_proveedor').val().trim();

    const nameRegex = /^[a-zA-Z\s]+$/;
    const emailRegex = /^\S+@\S+\.\S+$/;
    const numberRegex = /^\d+$/;

    var tipo_sol = tipoSolicitud === '1' ? 'guardarProveedor' : 'updateInfoProveedor';

    if (tipoSolicitud == '2') {
        if (estado === "-1") {
            toastr.info("Seleccione un estado", "Campo incompleto");
            return false;
        }
    }

    if (nombre === "") {
        toastr.info("El nombre es requerido", "Campo incompleto");
        return false;
    }
    if (!nameRegex.test(nombre)) {
        toastr.info("El nombre solo debe contener letras", "Entrada inválida");
        return false;
    }
    if (contacto === "") {
        toastr.info("El contacto es requerido", "Campo incompleto");
        return false;
    }
    if (!nameRegex.test(contacto)) {
        toastr.info("El contacto solo debe contener letras", "Entrada inválida");
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
    if (direccion === "") {
        toastr.info("La dirección es requerida", "Campo incompleto");
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
            "nombre": nombre,
            "contacto": contacto,
            "correo": email,
            "telefono": telefono,
            "direccion": direccion,
            "estado": estado,
            "id_proveedor": id_proveedor
        },
        dataType: "json",
        success: function (rsp) {
            if (rsp.success) {
                toastr.success(rsp.success, "Success");
                cargarTablaCategorias();
            } else if (rsp.error) {
                Swal.close();
                toastr.error(rsp.error, "ERROR");
            }
        },
        error: function () {
            Swal.close();
            toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
        },
        complete: function () {
            $('#nuevoProveedorModal').modal('hide');
            setTimeout(function () {
                Swal.close();
            }, 2000);
        }
    });
}

function cargarTablaCategorias() {
    var dataTableProveedores = $('#dataTableProveedores');

    if ($.fn.DataTable.isDataTable(dataTableProveedores)) { 
        dataTableProveedores.DataTable().ajax.reload(null, false); // Recargar DataTable si ya está inicializado
        Swal.close(); // Cerrar Swal después de recargar
    } else {
        dataTableProveedores.DataTable({
            ajax: {
                url: baseURL + 'getProveedor',
                dataType: 'JSON',
                dataSrc: function (data) {
                    console.log(data);
                    if (data.error) {
                        Swal.close();
                        toastr.error(data.error, "Error al cargar datos");
                        return [];
                    }
                    return data.success;
                },
                error: function (xhr, error, thrown) {
                    Swal.close();
                    toastr.error("Error en la carga de datos: " + thrown, "Error");
                },
                complete: function () {
                    Swal.close(); // Cerramos el Swal cuando los datos se hayan cargado
                }
            },
            columns: [
                { data: 'nombre' },
                { data: 'contacto' },
                { data: 'telefono' },
                { data: 'email' },
                { data: 'direccion' },
                {
                    data: 'estado',
                    render: function(data, type, row) {
                        if (data == 1) {
                            return 'Activo';
                        } else {
                            return 'Inactivo';
                        }
                    }
                },
                {
                    data: null,
                    className: 'dt-center',
                    defaultContent: '<button class="btn btn-info btn-sm btnVerOpciones">Editar</button>',
                    orderable: false
                }
            ]
        });

        // Configurar evento de clic para mostrar modal con opciones
        dataTableProveedores.on('click', '.btnVerOpciones', function () {
            $('.modalGuardar').hide();
            $('.modalEditarProveedor').show();
            $('.estado').show();

            // Obtener los datos de la fila seleccionada
            var $row = $(this).closest('tr');
            var rowData = dataTableProveedores.DataTable().row($row).data();
            if (!rowData) return; // Salir si no hay datos en la fila

            // Llenar los campos del formulario con los datos de la fila seleccionada
            $('#id_proveedor').val(rowData.id_proveedor);
            $('#nombre').val(rowData.nombre);
            $('#contacto').val(rowData.contacto);
            $('#telefono').val(rowData.telefono);
            $('#email').val(rowData.email);
            $('#direccion').val(rowData.direccion);
            $('#estado').val(rowData.estado);

            $('#nuevoProveedorModal').modal('show');
        });
    }
}
