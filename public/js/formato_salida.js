//prodcuto encontrado y agregado
let productosEncontrados = [];
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector('.btn-validarDatosIXC').addEventListener('click', guardarDatosIngresos);

    setTimeout(() => {
        document.getElementById("campoBusqueda").value = "";
        document.getElementById("documento").value = "";
    }, 400);

    var today = new Date().toISOString().split('T')[0];
    /* document.getElementById('fecha').value = today; */

    var tipoMovimientoSelect = document.getElementById('tipoMovimiento');
    var sucursalOrigenGroup = document.getElementById('sucursalOrigenGroup');
    var sucursalOrigenSelect = document.getElementById('sucursal_origen');
    var validaGranTotal = document.getElementById('validaGranTotal');
    validaGranTotal.style.display = 'none';

    tipoMovimientoSelect.addEventListener('change', function () {
        handleTipoMovimientoChange(tipoMovimientoSelect, sucursalOrigenGroup, sucursalOrigenSelect, validaGranTotal);
    });

    $('#filtrotipoMovimiento').on('change', function () {
        var selectedValue = $(this).val(); // Obtener el valor seleccionado
        if (selectedValue !== '-1') { // Verificar que no sea el valor por defecto
            Swal.fire({
                title: 'Espere...',
                html: 'Buscando documentos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            cargarDocumentos(selectedValue);
        }
    });

    $(campoBusqueda).autocomplete({
        source: function (request, response) {
            // Mostrar opción de "Buscando resultados..."
            response([{ label: "Buscando resultados...", value: "" }]);
            $.ajax({
                url: baseURL + "getProducts",
                type: "POST",
                dataType: "json",
                data: { search: request.term}, // Asegurar que se envía correctamente
                success: function (data) {
                    console.log("Respuesta del servidor:", data);

                    // Asegurar que la respuesta sea un array de objetos
                    if (Array.isArray(data.success)) {
                        response(data.success.slice(0, 10).map(item => ({
                            label: item.nombre + " - " + item.codigo_producto,
                            value: item.nombre,
                            codPro: item.codigo_producto
                        })));
                    } else {
                        // Si no es un array, retornar mensaje amigable
                        response([{ label: "No se encontraron resultados", value: "" }]);
                    }
                },
                error: function () {
                    console.error("Error en la búsqueda de clientes");
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            campoBusqueda.value = ui.item.value; // Mostrar el nombre en el input
            //duiBuscarCliente(ui.item.dui); // Pasar el DUI como parámetro
            addProduct(ui.item.codPro);
        }
    });

    function addProduct(inputProduct) {
        var sucursal_origen;
    
        let productInput = inputProduct;//$('#buscar_producto').val();
    
        var search = productInput
    
        sucursal_origen = document.getElementById("sucursal_origen").value;
    
        if (sucursal_origen == '-1') {
            toastr.error("La sucursal de origen es requerida para la busqueda", "Campo requerido");
            Swal.close();
            return;
        }
    
        Swal.fire({
            title: 'Espere...',
            html: 'buscando producto...',
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
            data: {
                search: search,
                sucursal_origen: sucursal_origen
            },
            dataType: "json",
            success: function (rsp) {
                console.log(rsp.success.length);
                if (rsp.success && Array.isArray(rsp.success)) {
                    if (rsp.success.length > 1) {
                        console.log("entro porque trae dos")
                        toastr.error("Al parecer hay mas de un producto, ingrese el codigo completo", "Ingrese codigo");
                        return;
                    } if (rsp.success.length === 0) {
                        mostrarError("Verifique el codigo o nombre ingresado", `No hay registros con ${search}`);
                        return;
                    } else if (rsp.success.length === 1) {
                        let codigoProducto = rsp.success[0].codigo_producto;
                        if (isProductInTable(codigoProducto)) {
                            Swal.close();
                            toastr.error("El producto ya está en la tabla.", "Codigo existente");
                            return;
                        }
                        console.log(rsp.success);
                        llenarTablaSalidas(rsp.success);
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
    }
});


function llenarTablaSalidas(datos) {
    datos.forEach(product => {
        let productTable = document.getElementById('productTable');
        let newRow = productTable.insertRow();

        let cellCodigo = newRow.insertCell(0);
        let cellNombre = newRow.insertCell(1);
        let cellCantidad = newRow.insertCell(2);
        let cellEliminar = newRow.insertCell(3);

        cellCodigo.innerHTML = product.codigo_producto;
        cellNombre.innerHTML = product.nombre;
        cellCantidad.innerHTML = `
            <input type='text' min='1' style='width: 80px;' class='form-control form-control-sm soloNumeros'>
            <input type='hidden' name='id_producto[]' value='${product.id_producto}'>
        `;
        cellEliminar.innerHTML = `<button type='button' class='btn btn-danger btn-sm' onclick='eliminarFila(this)'><i class='fas fa-trash'></i></button>`;

        $('.soloNumeros').mask('0000', { placeholder: "0" });
    });
}

function eliminarFila(button) {
    var row = button.closest('tr');
    row.remove();
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

function guardarDatosIngresos() {
    console.log("ingresosSave");
    var formValid = true;
    var formData = {};
    var productos = [];

    // Collect data and validate
    $('.ingreso_x_compra [name]').each(function () {
        var name = $(this).attr('name');
        var value = $(this).val();

        var isVisible = $(this).is(':visible');

        if (isVisible) {
            formData[name] = value;

            if (value === '') {
                toastr.error(`El campo ${name} es requerido`, "Campo incompleto");
                formValid = false;
                return false;
            } else if (value === '-1') {
                toastr.error(`Seleccione una opcion en ${name.replace(/_/g, ' ')}`, "Campo incompleto");
                formValid = false;
                return false;
            }
        }
    });

    console.log(" valor del formValid:: " + formValid);
    
    productos = llenarProductoArraySalida();
    
    console.log("valor del productos:: " + productos);
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

        console.log("Datos enviados:: " + formData);

        $.ajax({
            type: "POST",
            url: baseURL + "procesar_ingreso",
            data: JSON.stringify(formData),
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (rsp) {
                if (rsp.success) {
                    toastr.success(rsp.message, "Success");
                    location.reload();
                    //cargarTablaProductos();
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

function cargarDocumentos(idMovimiento) {//cargara la tabla segun tipo que se seleccione
    // Llamada Ajax para obtener los documentos
    $.ajax({
        type: "GET",
        url: baseURL + "obtenerRegDocumentos/" + idMovimiento,
        dataType: "json",
        success: function (response) {
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
                $(document).on('click', '.btnImprimir', function () {
                    var idDocumento = $(this).data('id');
                    crearPDF(idDocumento); // Llamar a la función crearPDF con el ID del documento
                });

            } else {
                Swal.fire('Error', 'No se pudieron obtener los documentos.', 'error');
            }
            Swal.close();
        },
        error: function () {
            Swal.fire('Error', 'Ocurrió un error al cargar los datos...xsff.', 'error');
        }
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
        success: function (response) {
            Swal.close(); // Cerrar el mensaje de carga

            if (response.success) {
                // Abrir la URL del PDF en una nueva pestaña para descargarlo automáticamente
                window.open(response.url, '_blank');
            } else {
                Swal.fire('Error', 'Ocurrió un error al generar el PDF.', 'error');
            }
        },
        error: function () {
            Swal.close(); // Cerrar el mensaje de carga
            Swal.fire('Error', 'Ocurrió un error al generar el PDF.', 'error');
        }
    });
}

function handleTipoMovimientoChange(tipoMovimientoSelect, sucursalOrigenGroup, sucursalOrigenSelect, validaGranTotal) {
    var selectedOption = tipoMovimientoSelect.options[tipoMovimientoSelect.selectedIndex];
    var tipoMov = selectedOption.getAttribute('data-tipo-mov');

    if (tipoMov == '-1') {
        // Revertir cambios a su estado inicial
        sucursalOrigenGroup.style.display = 'none';
        sucursalOrigenSelect.disabled = true;
        validaGranTotal.style.display = 'none';
        tablaContainer.innerHTML = '';
        $('#sucursal_origen').val('-1');
    } else {
        // Mostrar y habilitar los selects
        crearTabla2(); //se carga la tabla dos porque es nota de remision
        sucursalOrigenGroup.style.display = 'block';
        sucursalOrigenSelect.disabled = false;
        validaGranTotal.style.display = 'none';
    }
}

function crearTabla2() {
    var tabla = $('<table>').addClass('table table-bordered');
    var thead = $('<thead>').appendTo(tabla);
    var tbody = $('<tbody>').attr('id', 'productTable').appendTo(tabla);

    var headers = ['Código', 'Nombre', 'Cant salida'];
    var productos = []; // Inicialmente vacío, se llenará más tarde

    var headerRow = $('<tr>').appendTo(thead);
    headers.forEach(function (header) {
        $('<th>').text(header).appendTo(headerRow);
    });

    $('#tablaContainer').empty().append(tabla);
}

function llenarProductoArraySalida() {
    var productos = [];

    $('#productTable tr').each(function (index) {
        var codigo = $(this).find('td').eq(0).text();
        var nombre = $(this).find('td').eq(1).text();
        var cantSalida = $(this).find('td').eq(2).find('input[type="text"]').val();
        var id_producto = $(this).find('input[name="id_producto[]"]').val();

        console.log("Variables a validar:");
        console.log("Código:", codigo);
        console.log("Nombre:", nombre);
        console.log("Cantidad Traslado:", cantSalida);
        console.log("ID Producto:", id_producto);

        if (codigo && nombre && cantSalida && id_producto) {
            productos.push({
                codigo: codigo,
                nombre: nombre,
                cantTraslado: cantSalida,
                id_producto: id_producto
            });
        } else {
            console.warn("Fila omitida debido a datos incompletos:", { codigo, nombre, cantSalida, id_producto });
        }
    });
    console.log("el producto agregado es::: ", productos);
    return productos; // Devolver el array de productos válidos
}