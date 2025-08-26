//prodcuto encontrado y agregado
let productosEncontrados = [];
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        document.getElementById("campoBusqueda").value = "";
        document.getElementById("documento").value = "";
    }, 400);

    const tipoBusqueda = document.getElementById("tipoBusqueda");
    const campoBusqueda = document.getElementById("campoBusqueda");

    $(campoBusqueda).autocomplete({
        source: function (request, response) {
            // Mostrar opción de "Buscando resultados..."
            response([{ label: "Buscando resultados...", value: "" }]);
            $.ajax({
                url: baseURL + "getProducts",
                type: "POST",
                dataType: "json",
                data: { search: request.term }, // Asegurar que se envía correctamente
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

    // Mostrar botón "Buscar" al escribir en el campo
    /* campoBusqueda.addEventListener("input", function () {
        if (tipoBusqueda.value === "codpro") {
            btnBuscar.style.display = "inline-block";
        } else {
            btnBuscar.style.display = "none";
        }
    }); */

    document.querySelector('.btn-validarDatosIXC').addEventListener('click', guardarDatosIngresos);

    var today = new Date().toISOString().split('T')[0];
    /* document.getElementById('fecha').value = today; */

    var tipoMovimientoSelect = document.getElementById('tipoMovimiento');
    var sucursalOrigenGroup = document.getElementById('sucursalOrigenGroup');
    var sucursalDestinoGroup = document.getElementById('sucursalDestinoGroup');
    var sucursalOrigenSelect = document.getElementById('sucursal_origen');
    var sucursalDestinoSelect = document.getElementById('sucursal_destino');
    var validaGranTotal = document.getElementById('validaGranTotal');
    validaGranTotal.style.display = 'none';

    tipoMovimientoSelect.addEventListener('change', function () {
        handleTipoMovimientoChange(tipoMovimientoSelect, sucursalOrigenGroup, sucursalDestinoGroup, sucursalOrigenSelect, sucursalDestinoSelect, validaGranTotal);
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

    function addProduct(inputProduct) {
        var tipoMov = document.getElementById('tipoMovimiento').selectedOptions[0].dataset.tipoMov;
        var sucursal_origen;

        //let productInput = $('#buscar_producto').val();
        let productInput = inputProduct;

        var search = inputProduct;

        if (tipoMov == '1') {
            sucursal_origen = document.getElementById("sucursal_origen").value;

            if (sucursal_origen == '-1') {
                toastr.error("La sucursal de origen es requerida para la busqueda", "Campo requerido");
                Swal.close();
                return;
            }
        } else if (tipoMov == '-1') {
            toastr.error("El tipo de movimiento es requerido para ejecutar esta funcion", "Campo requerido");
            Swal.close();
            return;
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
        if (productInput.length == 0) {
            toastr.error("Ingrese el codigo del producto", "Campo vacio");
            Swal.close();
            return;
        }

        let tipoMovimiento = $('#tipoMovimiento').val();
        let urlAccion = '';
        if (tipoMovimiento == 2) {
            urlAccion = 'getProductoExistencia';
        } else {
            urlAccion = 'getProducts';
        }

        $.ajax({
            type: "POST",
            url: baseURL + urlAccion,
            data: {
                search: search,
                sucursal_origen: sucursal_origen
            },
            dataType: "json",
            success: function (rsp) {
                console.log(rsp);
                if (rsp.success && Array.isArray(rsp.success)) {
                    if (rsp.success.length > 1) {
                        toastr.error("Al parecer hay mas de un producto con el codigo ingresado", "Ingrese codigo");
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
                        if (typeof sucursal_origen !== 'undefined' && sucursal_origen !== null && sucursal_origen !== '' && sucursal_origen !== '-1') {
                            console.log("llenarTablaIngresoNotaRemision");
                            llenarTablaIngresoNotaRemision(rsp.success);
                        } else {
                            console.log("llenarTablaIngreso");
                            llenarTablaIngreso(rsp.success);
                        }
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
        cellEliminar.innerHTML = `<button type='button' class='btn btn-danger btn-sm' onclick='eliminarFila(this)'><i class='fas fa-trash'></i></button>`;

        $('.soloNumeros').mask('0000', { placeholder: "0" });
        updateGrandTotal();
    });
}

function llenarTablaIngresoNotaRemision(datos) {
    datos.forEach((product, index) => {
        let productTable = document.getElementById('productTable');
        let newRow = productTable.insertRow();
        var disponibilidad = (product.disponibilidad !== undefined && product.disponibilidad !== null)
            ? product.disponibilidad
            : 0;

        let cellCodigo = newRow.insertCell(0);
        let cellNombre = newRow.insertCell(1);
        let cellDisponible = newRow.insertCell(2);
        let cellCantidadTraslado = newRow.insertCell(3);
        let cellEliminar = newRow.insertCell(4);

        cellCodigo.innerHTML = product.codigo_producto;
        cellNombre.innerHTML = product.nombre;
        //cellDisponible.innerHTML = product.disponibilidad;
        cellDisponible.innerHTML = disponibilidad;

        cellCantidadTraslado.innerHTML = `
            <input type='text' style='width: 80px;' class='form-control form-control-sm soloNumeros' id='cantidadTraslado_${index}' onkeyup='validarCantidad(${index}, ${disponibilidad})'>
            <input type='hidden' name='id_producto[]' value='${product.id_producto}'>
        `;
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

function validarCantidad(index, disponibilidad) {
    let cantidadInput = document.getElementById(`cantidadTraslado_${index}`);
    let cantidad = parseInt(cantidadInput.value, 10);

    if (cantidad > disponibilidad) {
        cantidadInput.value = disponibilidad;
        mostrarError(`La cantidad no puede ser mayor que la disponibilidad (${disponibilidad}).`, 'Verifica la disponibilidad');
    } else if (cantidad < 1) {
        cantidadInput.value = 1;
        mostrarError('La cantidad no puede ser menor que 1.', 'Verifica la disponibilidad');
    }
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

            if (value === '' && formData.tipo_Movimiento ===1) {
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
    if (formData.tipo_Movimiento == 2) {
        console.log("es nota de remision");
        productos = llenarProductoArrayNotaR();
    } else {
        console.log("no es nota de remision");
        productos = llenarProductoArrayNormal();
    }
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
                    setTimeout(function () {
                        location.reload(); // Recargar la página después de mostrar el mensaje
                    }, 1500);
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
                console.log(response.documentos)
                // Limpiar y destruir la tabla antes de volver a cargar los datos
                if ($.fn.DataTable.isDataTable('#documentosTable')) {
                    $('#documentosTable').DataTable().clear().destroy();
                }
                // Insertar los datos en la tabla
                var table = $('#documentosTable').DataTable({
                    data: response.documentos,
                    columns: [
                        { data: 'usuario' },
                        { 
                            data: null,
                            render: function (data, type, row) {
                                // Mostrar destino y origen, y manejar null
                                let destino = row.sucursal_destino ? row.sucursal_destino : '--';
                                let origen  = row.sucursal_origen ? row.sucursal_origen : '--';
                                return `Origen: ${origen} / Destino: ${destino}`;
                            }
                        },
                        { data: 'proveedor' },
                        { data: 'nombre_movimiento' },
                        { data: 'estado' },
                        { data: 'noDocumento' },
                        { data: 'correlativo' },
                        {
                            data: null,
                            render: function (data, type, row) {
                                let botones = '';

                                if (row.estado === 'Procesado' && response.sucActual == row.id_sucursal_destino) {
                                    botones += '<button type="button" class="btn btn-success btn-sm btnAceptar me-1" title="Aceptar remisión" data_seleccion="aceptado" data-id="' + row.id_documento + '"><i class="fa-solid fa-check"></i></button>';
                                    botones += '<button type="button" class="btn btn-danger btn-sm btnRechazar" title="Rechazar remisión" data_seleccion="rechazado" data-id="' + row.id_documento + '"><i class="fa-solid fa-xmark"></i></button>';
                                }else{
                                    botones = '<button type="button" class="btn btn-info btn-sm btnImprimir me-1" title="Imprimir documento" data-id="' + row.id_documento + '"><i class="fa-solid fa-print"></i></button>';
                                }

                                return botones;
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

                // Evento click para aceptar remisión
                $(document).on('click', '.btnAceptar', function () {
                    var idDocumento = $(this).data('id');
                    responderRemision(idDocumento, 'aceptado');
                });

                // Evento click para rechazar remisión
                $(document).on('click', '.btnRechazar', function () {
                    var idDocumento = $(this).data('id');
                    responderRemision(idDocumento, 'rechazado');
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

function responderRemision(idDocumento, respuesta) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Estás a punto de " + (respuesta === 'aceptado' ? 'aceptar' : 'rechazar') + " esta remisión.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: respuesta === 'aceptado' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, confirmar'
    }).then((result) => {
        Swal.fire({
        title: 'Espere...',
        html: '',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
        if (result.isConfirmed) {
            $.ajax({
                url: baseURL + "responderRemision",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    id_documento: idDocumento,
                    respuesta: respuesta
                }),
                success: function (res) {
                    if (res.success) {
                        $('#modalVerDocumentos').modal('hide');
                        Swal.close();
                        Swal.fire('¡Hecho!', res.message, 'success');
                        //cargarDocumentos(2); // Recargar tabla (ajusta si usas otro ID)
                        location.reload();
                    } else {
                        Swal.close();
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function () {
                    Swal.close();
                    Swal.fire('Error', 'No se pudo procesar la respuesta.', 'error');
                }
            });
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

function handleTipoMovimientoChange(tipoMovimientoSelect, sucursalOrigenGroup, sucursalDestinoGroup, sucursalOrigenSelect, sucursalDestinoSelect, validaGranTotal) {
    var selectedOption = tipoMovimientoSelect.options[tipoMovimientoSelect.selectedIndex];
    var tipoMov = selectedOption.getAttribute('data-tipo-mov');

    if (tipoMov == '-1') {
        // Revertir cambios a su estado inicial
        sucursalOrigenGroup.style.display = 'none';
        sucursalDestinoGroup.style.display = 'none';
        sucursalOrigenSelect.disabled = true;
        sucursalDestinoSelect.disabled = true;
        validaGranTotal.style.display = 'none';
        tablaContainer.innerHTML = '';
        $('#sucursal_origen').val('-1');
    } else if (tipoMov == '1') {
        // Mostrar y habilitar los selects
        crearTabla2(); //se carga la tabla dos porque es nota de remision
        sucursalOrigenGroup.style.display = 'block';
        sucursalDestinoGroup.style.display = 'block';
        sucursalOrigenSelect.disabled = false;
        sucursalDestinoSelect.disabled = false;
        validaGranTotal.style.display = 'none';
    } else {
        $('#sucursal_origen').val('-1');
        crearTabla1(); //se carga la tabla uno porque es flujo normal
        // Ocultar sucursal origen, mostrar sucursal destino y habilitar solo sucursal destino
        sucursalOrigenGroup.style.display = 'none';
        sucursalOrigenSelect.disabled = true;

        sucursalDestinoGroup.style.display = 'block';
        sucursalDestinoSelect.disabled = false;
        validaGranTotal.style.display = 'block';
    }
}

function crearTabla1() {
    var tabla = $('<table>').addClass('table table-bordered');
    var thead = $('<thead>').appendTo(tabla);
    var tbody = $('<tbody>').attr('id', 'productTable').appendTo(tabla);

    var headers = ['Código', 'Nombre', 'Cantidad', 'Precio', 'Total', 'Acción'];
    var productos = []; // Inicialmente vacío, se llenará más tarde

    var headerRow = $('<tr>').appendTo(thead);
    headers.forEach(function (header) {
        $('<th>').text(header).appendTo(headerRow);
    });

    $('#tablaContainer').empty().append(tabla);
}

function crearTabla2() {
    var tabla = $('<table>').addClass('table table-bordered');
    var thead = $('<thead>').appendTo(tabla);
    var tbody = $('<tbody>').attr('id', 'productTable').appendTo(tabla);

    var headers = ['Código', 'Nombre', 'Disponible', 'Cant traslado'];
    var productos = []; // Inicialmente vacío, se llenará más tarde

    var headerRow = $('<tr>').appendTo(thead);
    headers.forEach(function (header) {
        $('<th>').text(header).appendTo(headerRow);
    });

    $('#tablaContainer').empty().append(tabla);
}

function llenarProductoArrayNormal() {
    var productos = [];
    $('#productTable tr').each(function () {
        var codigo = $(this).find('td').eq(0).text();
        var nombre = $(this).find('td').eq(1).text();
        var cantidad = $(this).find('td').eq(2).find('input[type="text"]').val();
        var precio = $(this).find('td').eq(3).text();
        var total = $(this).find('td').eq(4).text();
        var id_producto = $(this).find('input[name="id_producto[]"]').val();

        /* if (codigo && nombre && cantidad && precio && total && id_producto) { */
        if (
            codigo && nombre && !isNaN(disponible) && !isNaN(cantTraslado) && id_producto
        ) {
            if (disponible <= 0) {
                Swal.fire('Advertencia', `El producto "${nombre}" no tiene stock disponible.`, 'warning');
            } else if (cantTraslado <= 0 || cantTraslado > disponible) {
                Swal.fire('Advertencia', `La cantidad a trasladar del producto "${nombre}" debe ser mayor que 0 y menor o igual que el disponible (${disponible}).`, 'warning');
            } else {
                productos.push({
                    codigo: codigo,
                    nombre: nombre,
                    disponible: disponible,
                    cantTraslado: cantTraslado,
                    id_producto: id_producto
                });
            }
        } else {
            Swal.fire('Advertencia', 'Faltan datos del producto o son inválidos.', 'warning');
        }

    });
    return productos; // Devolver el array de productos válidos
}

function llenarProductoArrayNotaR() {
    var productos = [];
    var hayError = false;

    $('#productTable tr').each(function (index) {
        var codigo = $(this).find('td').eq(0).text().trim();
        var nombre = $(this).find('td').eq(1).text().trim();
        var disponible = parseFloat($(this).find('td').eq(2).text().trim());
        var cantTraslado = parseFloat($(this).find('td').eq(3).find('input[type="text"]').val().trim());
        var id_producto = $(this).find('input[name="id_producto[]"]').val();

        console.log("Variables a validar:");
        console.log("Código:", codigo);
        console.log("Nombre:", nombre);
        console.log("Disponible:", disponible);
        console.log("Cantidad Traslado:", cantTraslado);
        console.log("ID Producto:", id_producto);

        if (!codigo || !nombre || isNaN(disponible) || isNaN(cantTraslado) || !id_producto) {
            console.warn("Fila omitida debido a datos incompletos.");
            return; // sigue con la siguiente fila
        }

        if (disponible <= 0) {
            Swal.fire('Advertencia', `El producto "${nombre}" no tiene stock disponible.`, 'warning');
            hayError = true;
            return false; // rompe el .each si quieres detener la validación en el primer error
        }

        if (cantTraslado <= 0 || cantTraslado > disponible) {
            Swal.fire('Advertencia', `La cantidad a trasladar del producto "${nombre}" debe ser mayor que 0 y menor o igual que el disponible (${disponible}).`, 'warning');
            hayError = true;
            return false; // rompe el .each
        }

        productos.push({
            codigo: codigo,
            nombre: nombre,
            disponible: disponible,
            cantTraslado: cantTraslado,
            id_producto: id_producto
        });
    });

    if (hayError) {
        return []; // Devuelve vacío si hubo algún error
    }

    console.log("Productos válidos agregados:", productos);
    return productos;
}
