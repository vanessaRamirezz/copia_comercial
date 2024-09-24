var dataTableCategorias;

document.addEventListener("DOMContentLoaded", function () {
    console.log("Ready categorias!");
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            cargarTablaCategorias(); // Llamamos a cargarTablaCategorias aquí para mostrar el Swal mientras carga
        }
    });

    $('#estado').change(function(){
        if($(this).val() == "0") {
            $('.alertEstadoInactivo').show();
        } else {
            $('.alertEstadoInactivo').hide();
        }
    });

    $('#nuevaCategoriaBtn').click(function () {
        $('.modalGuardar').show();
        $('.modalEditarCategoria').hide();
        $('.estado').hide();
        // Limpiar campos del formulario
        $('#nombreCategoria').val('');
        $('#estado').val('-1');
        $('#id_categoria').val('');

        // Mostrar modal de Bootstrap
        $('#nuevaCategoria').modal('show');
    });
});

function procesoCategoria(tipoSolicitud) {
    var nombreCategoria = $('#nombreCategoria').val();
    var id_categoria = $('#id_categoria').val();
    var estado = $('#estado').val();
    console.log(nombreCategoria);
    console.log(id_categoria);
    console.log(estado);

    var tipo_sol = tipoSolicitud === '1' ? 'guardarCategoria' : 'updateInfoCategoria';

    if (tipoSolicitud == '2') {
        if (estado === "-1") {
            toastr.info("Seleccione un estado", "Campo incompleto");
            return false;
        }
    }

    if (nombreCategoria === "") {
        toastr.error("El nombre de la categoría es requerido", "Campo incompleto");
        return false;
    }

    $.ajax({
        type: 'POST',
        url: baseURL + tipo_sol,
        data: {
            nombre: nombreCategoria,
            id_categoria: id_categoria,
            estado: estado
        },
        dataType: 'json',
        success: function(data) {
            console.log(data);
            if (data.success) {
                toastr.success(data.success, "Success");
                cargarTablaCategorias();
                $('#nuevaCategoria').modal('hide');
            } else {
                toastr.error("ERROR", data.error);
            }
        },
        error: function() {
            toastr.error("ERROR","Error al guardar la categoría.");
        }
    });
}

function cargarTablaCategorias() {
    var dataTableElement = $('#dataTableCategorias');
    if ($.fn.DataTable.isDataTable(dataTableElement)) { // Verificar si DataTable ya está inicializado
        dataTableCategorias.ajax.reload(null, false); // Recargar DataTable si ya está inicializado
        Swal.close(); // Cerrar Swal después de recargar
    } else {
        dataTableCategorias = dataTableElement.DataTable({
            ajax: {
                url: baseURL + 'getCategorias',
                dataSrc: 'success',
                complete: function () {
                    Swal.close(); // Cerramos el Swal cuando los datos se hayan cargado
                }
            },
            columns: [
                { data: 'nombre' },
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
        dataTableElement.on('click', '.btnVerOpciones', function () {
            $('.modalGuardar').hide();
            $('.modalEditarCategoria').show();
            $('.estado').show();

            // Obtener los datos de la fila seleccionada
            var $row = $(this).closest('tr');
            var rowData = dataTableCategorias.row($row).data();
            if (!rowData) return; // Salir si no hay datos en la fila

            // Llenar los campos del formulario con los datos de la fila seleccionada
            $('#id_categoria').val(rowData.id_categoria);
            $('#nombreCategoria').val(rowData.nombre);
            $('#estado').val(rowData.estado);

            $('#nuevaCategoria').modal('show');
        });
    }
}
