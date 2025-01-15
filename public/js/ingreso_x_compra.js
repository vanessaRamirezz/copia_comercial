//prodcuto encontrado y agregado
let productosEncontrados = [];
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector('.btn-validarDatosIXC').addEventListener('click', guardarDatosIXC);

    var today = new Date().toISOString().split('T')[0];
    document.getElementById('fecha').value = today;

    cargarDocumentos();
});
function addProduct() {
    let productInput = $('#buscar_producto').val();

    var search = productInput
    console.log(search);
    Swal.fire({
        title: 'Espere...',
        html: 'Procesando solicitud...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    if (productInput.length == 0) {
        toastr.error("Ingrese el codigo del producto", "Campo vacio");
        Swal.close();
        return;
    }

    $.ajax({
        type: "POST",
        url: baseURL + 'getProducts',
        data: { search: search },
        dataType: "json",
        success: function (rsp) {
            console.log(rsp.success.length);
            if (rsp.success && Array.isArray(rsp.success)) {
                if (rsp.success.length > 1) {
                    console.log("entro porque trae dos")
                    toastr.error("Al parecer hay mas de un producto con el codigo ingresado", "Ingrese codigo");
                    return;
                } else if (rsp.success.length === 1) {
                    let codigoProducto = rsp.success[0].codigo_producto;
                    if (isProductInTable(codigoProducto)) {
                        Swal.close();
                        toastr.error("El producto ya está en la tabla.", "Codigo existente");
                        return;
                    }
                    llenarTablaIngreso(rsp.success);
                }
            } else if (rsp.error) {
                Swal.close();
                toastr.error(rsp.error, "ERROR");
            } else {
                Swal.close();
                toastr.error("La respuesta del servidor no es válida.", "Error en carga de datos");
            }
        },
        error: function () {
            Swal.close();
            toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
        },
        complete: function () {
            Swal.close();
        }
    });

    document.getElementById('buscar_producto').value = '';
}

function llenarTablaIngreso(datos) {
    datos.forEach(product => {
        let productTable = document.getElementById('productTable');
        let newRow = productTable.insertRow();

        let cellCodigo = newRow.insertCell(0);
        let cellNombre = newRow.insertCell(1);
        let cellCantidad = newRow.insertCell(2);
        let cellPrecio = newRow.insertCell(3);
        let cellTotal = newRow.insertCell(4);
        let cellEliminar = newRow.insertCell(5);

        cellCodigo.innerHTML = product.codigo_producto;
        cellNombre.innerHTML = product.nombre;
        cellCantidad.innerHTML = `
            <input type='text' value='1' min='1' style='width: 80px;' class='form-control form-control-sm soloNumeros' onkeyup='updateTotal(this)'>
            <input type='hidden' name='id_producto[]' value='${product.id_producto}'>
        `;
        cellPrecio.innerHTML = product.precio;
        cellTotal.innerHTML = product.precio;
        cellEliminar.innerHTML = `<button type='button' class='btn btn-danger btn-sm' onclick='eliminarFila(this)'><i class='fas fa-trash'></i></button>`; // Agregar el botón de eliminar con Font Awesome

        $('.soloNumeros').mask('0000', { placeholder: "0" });
        updateGrandTotal();
    });
}

function eliminarFila(button) {
    var row = button.closest('tr');
    row.remove();
    updateGrandTotal();
}

// Función para verificar si un producto ya está en la tabla
function isProductInTable(codigoProducto) {
    let productTable = document.getElementById('productTable');
    let rows = productTable.rows;

    for (let i = 0; i < rows.length; i++) {
        let codigo = rows[i].cells[0].innerText;
        if (codigo === codigoProducto) {
            return true;
        }
    }
    return false;
}

function updateTotal(element) {
    let row = element.parentElement.parentElement;
    let quantity = element.value;
    let price = parseFloat(row.cells[3].innerText);
    row.cells[4].innerText = (quantity * price).toFixed(2);

    updateGrandTotal();
}

function updateGrandTotal() {
    let productTable = document.getElementById('productTable');
    let rows = productTable.rows;
    let grandTotal = 0;

    for (let i = 0; i < rows.length; i++) {
        grandTotal += parseFloat(rows[i].cells[4].innerText);
    }

    document.getElementById('total').value = grandTotal.toFixed(2);
}

function guardarDatosIXC() {
    console.log("ixc");
    var formValid = true;
    var formData = {};
    var productos = [];

    // Collect product data from the table
    $('#productTable tr').each(function () {
        var codigo = $(this).find('td').eq(0).text();
        var nombre = $(this).find('td').eq(1).text();
        var cantidad = $(this).find('td').eq(2).find('input[type="text"]').val();
        var precio = $(this).find('td').eq(3).text();
        var total = $(this).find('td').eq(4).text();
        var id_producto = $(this).find('input[name="id_producto[]"]').val();

        if (codigo && nombre && cantidad && precio && total && id_producto) {
            productos.push({
                codigo: codigo,
                nombre: nombre,
                cantidad: cantidad,
                precio: precio,
                total: total,
                id_producto: id_producto
            });
        }
    });

    console.log("Productos:", productos);

    // Collect data and validate
    $('.ingreso_x_compra [name]').each(function () {
        var name = $(this).attr('name');
        var value = $(this).val();
        formData[name] = value;

        if (value === '') {
            toastr.error(`El campo ${name} es requerido`, "Campo incompleto");
            formValid = false;
            return false;
        }
    });

    if (formValid && productos.length == 0) {
        toastr.error("Debe agregar al menos un producto", "Productos incompletos");
        formValid = false;
        return false;
    }

    if (formValid) {
        formData["productos"] = productos;
        console.log("Formulario válido. Datos:", formData);

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
            url: baseURL + "procesar_ingreso_x_compra",
            data: JSON.stringify(formData),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (rsp) {
                if (rsp.success) {
                    toastr.success(rsp.message, "Success");
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
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

function cargarDocumentos() {
    $('#modalVerDocumentos').on('show.bs.modal', function () {
        // Llamada Ajax para obtener los documentos
        $.ajax({
            type: "GET",
            url: baseURL + "obtenerRegDocumentos/1",
            dataType: "json",
            success: function (response) {
                console.log(response.documentos);
                if (response.success) {
                    // Limpiar y destruir la tabla antes de volver a cargar los datos
                    if ($.fn.DataTable.isDataTable('#documentosTable')) {
                        $('#documentosTable').DataTable().clear().destroy();
                    }

                    // Insertar los datos en la tabla
                    var table = $('#documentosTable').DataTable({
                        data: response.documentos,
                        columns: [
                            { data: 'usuario' },
                            { data: 'sucursal' },
                            { data: 'proveedor' },
                            { data: 'nombre_movimiento' },
                            { data: 'estado' },
                            { data: 'noDocumento' },
                            { data: 'correlativo' },
                            {
                                // Columna para el botón de impresión
                                data: null,
                                render: function (data, type, row) {
                                    return '<button type="button" class="btn btn-info btn-sm btnImprimir" title="imprimir documento" data-id="' + row.id_documento + '"><i class="fa-solid fa-print"></i></button>';
                                }
                            }
                        ]
                    });

                    // Desactivar autocompletado en el campo de búsqueda
                    $('#documentosTable_filter input').attr('autocomplete', 'off').attr('name', 'new_search_field');

                     // Evento click para el botón Imprimir PDF
                     $(document).on('click', '.btnImprimir', function() {
                        var idDocumento = $(this).data('id');
                        crearPDF(idDocumento); // Llamar a la función crearPDF con el ID del documento
                    });
                } else {
                    Swal.fire('Error', 'No se pudieron obtener los documentos.', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Ocurrió un error al cargar los datos.', 'error');
            }
        });
    });
}

function crearPDF(idDocumento) {
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
        url: baseURL + 'generaPDfInXCompra',
        data: { id_documento: idDocumento },
        success: function(response) {
            Swal.close(); // Cerrar el mensaje de carga

            if (response.success) {
                // Abrir la URL del PDF en una nueva pestaña para descargarlo automáticamente
                window.open(response.url, '_blank');
            } else {
                Swal.fire('Error', 'Ocurrió un error al generar el PDF.', 'error');
            }
        },
        error: function() {
            Swal.close(); // Cerrar el mensaje de carga
            Swal.fire('Error', 'Ocurrió un error al generar el PDF.', 'error');
        }
    });
}
