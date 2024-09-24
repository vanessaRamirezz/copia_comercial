var dataTableProductos;
document.addEventListener("DOMContentLoaded", function () {
    console.log("Ready productos!");

    cargarTablaProductos();

    $('#nuevoProductoBtn').click(function () {
        $('.modalGuardar').show();
        $('.modalEditarProducto').hide();
        $('.div-ajuste').hide();
        $('.modalAjuste').hide();
        $('.estado').hide();
        // Limpiar campos del formulario
        $('#nombre').val('').prop('disabled', false);
        $('#marca').val('').prop('disabled', false);
        $('#codigo').val('').prop('disabled', false);
        $('#modeloProducto').val('').prop('disabled', false);
        $('#colorProducto').val('').prop('disabled', false);
        $('#medidasProducto').val('').prop('disabled', false);
        $('#precioProducto').val('').prop('disabled', false);
        $('#productoDisponible').val('').prop('disabled', true);
        $('#categoriaProducto').val('-1').prop('disabled', false);
        $('#costo_unitario').val('').prop('disabled', false);
        $('#estado').val('-1');
        $('#disponible').val('').hide().prop('disabled', true);
        $('.disponible').hide();

        // Mostrar modal de Bootstrap
        $('#nuevoProducto').modal('show');
    });
    $('.alertEstadoInactivo').hide();

    $("#cerrarModalProducto").click(() => {
        $('#nuevoProducto').modal('hide');
    });

    $('#estado').change(function () {
        if ($(this).val() == "0") {
            $('.alertEstadoInactivo').show();
        } else {
            $('.alertEstadoInactivo').hide();
        }
    });
});

function procesoProductos(tipoSol) {
    console.log(tipoSol);
    var formValid = true;
    var formData = {};

    // Collect data and validate
    $('.modal-body .producto [name]').each(function () {
        var name = $(this).attr('name');
        var value = $(this).val();
        formData[name] = value;

        if (value === '' && (tipoSol != 1 && name != 'id_producto')) {
            toastr.error(`El campo ${name} es requerido`, "Campo incompleto");
            formValid = false;
            return false;
        }
    });

    if (formData['id_categoria'] === '-1') {
        toastr.warning("Debe seleccionar una categoría", "Categoría incompleta");
        formValid = false;
    } else if (formData['id_categoria'] === '-2') {
        toastr.error("Debe crear una categoría primero", "Categoría faltante");
        formValid = false;
    }

    if (formValid) {
        console.log("Formulario válido. Datos:", formData);
        // Aquí puedes realizar la llamada AJAX para enviar los datos al servidor
        var tipo_sol = tipoSol === '1' ? 'saveProduct' : 'updateProduct';

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
            data: formData,
            dataType: "json",
            success: function (rsp) {
                if (rsp.success) {
                    toastr.success(rsp.success, "Success");
                    cargarTablaProductos();
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
                $('#nuevoProducto').modal('hide');
                setTimeout(function () {
                    Swal.close();
                }, 2000);
            }
        });
    }
}

function cargarTablaProductos() {
    var dataTableProductos = $('#dataTableProductos');

    if ($.fn.DataTable.isDataTable(dataTableProductos)) {
        dataTableProductos.DataTable().ajax.reload(null, false); // Recargar DataTable si ya está inicializado
        Swal.close(); // Cerrar Swal después de recargar
    } else {
        dataTableProductos.DataTable({
            ajax: {
                url: baseURL + 'getProductos',
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
                { data: 'codigo_producto' },
                { data: 'nombre' },
                { data: 'disponible' },
                { data: 'precio' },
                { data: 'costo_unitario' },
                { data: 'nombre_categoria' },
                {
                    data: 'estado',
                    render: function (data, type, row) {
                        if (data == 1) {
                            return 'Activo';
                        } else {
                            return 'Inactivo';
                        }
                    }
                },
                { data: 'nombre_usuario' },
                { data: 'fecha_creacion' },
                {
                    data: null,
                    className: 'dt-center',
                    defaultContent: `
                        <button class="btn btn-info btn-sm btnVerOpciones">Editar</button>
                    `,
                    orderable: false
                }
            ]
        });

        // Configurar evento de clic para mostrar modal con opciones de edición
        dataTableProductos.on('click', '.btnVerOpciones', function () {
            $('.modalGuardar').hide();
            $('.modalEditarProducto').show();
            $('.modalAjuste').hide();
            $('.div-ajuste').hide();
            $('.estado').show();

            var $row = $(this).closest('tr');
            var rowData = dataTableProductos.DataTable().row($row).data();
            if (!rowData) return;
            
            $('#id_producto').val(rowData.id_producto).prop('disabled', false);
            $('#nombre').val(rowData.nombre).prop('disabled', false);
            $('#marca').val(rowData.marca).prop('disabled', false);
            $('#codigo').val(rowData.codigo_producto).prop('disabled', false);
            $('#modeloProducto').val(rowData.modelo).prop('disabled', false);
            $('#colorProducto').val(rowData.color).prop('disabled', false);
            $('#medidasProducto').val(rowData.medidas).prop('disabled', false);
            $('#precioProducto').val(rowData.precio).prop('disabled', false);
            $('#productoDisponible').val(rowData.disponible).prop('disabled', true);
            $('#categoriaProducto').val(rowData.id_categoria).prop('disabled', false);
            $('#estado').val(rowData.estado).prop('disabled', false);
            $('#costo_unitario').val(rowData.costo_unitario).prop('disabled', false);
            $('.estado').prop('hidden', false);

            $('#nuevoProducto').modal('show');
        });
    }
}