document.addEventListener("DOMContentLoaded", function () {

    $('#dataTableSolVariasTab').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false // Desactiva el buscador
    });

    cargarSolicitudesVarias();

    const tipoBusqueda = document.getElementById("tipoBusqueda");
    const campoBusqueda = document.getElementById("campoBusqueda");
    const btnBuscar = document.getElementById("btnBuscar");
    const noDuiCheckbox = document.getElementById("noDuiCheckbox");
    const duiCliente = document.getElementById("duiCliente");
    const nombreCliente = document.getElementById("nombreCliente");
    const direccionCliente = document.getElementById("direccionCliente");
    const telefonoCliente = document.getElementById("telefonoCliente");
    const correoCliente = document.getElementById("correoCliente");
    const idCliente = document.getElementById("id_cliente");

    // Asegurarse de que id_cliente esté vacío después de que todo se cargue
    setTimeout(function () {
        idCliente.value = "";
        //seteoFecha();
    }, 50);

    // Inicialmente deshabilitar campo de búsqueda y DUI
    campoBusqueda.disabled = true;
    duiCliente.disabled = true;

    // Manejo del tipo de búsqueda
    tipoBusqueda.addEventListener("change", function () {
        campoBusqueda.value = "";
        btnBuscar.style.display = "none";

        if (this.value === "dui") {
            $(campoBusqueda).addClass('duiG').mask('00000000-0', { placeholder: "00000000-0" });
            campoBusqueda.disabled = false;
        } else if (this.value === "nombre") {
            $(campoBusqueda).removeClass('duiG').unmask();
            campoBusqueda.disabled = false;
        } else {
            campoBusqueda.disabled = true;
        }
    });

    $(campoBusqueda).autocomplete({
        source: function (request, response) {
            // Mostrar opción de "Buscando resultados..."
            response([{ label: "Buscando resultados...", value: "" }]);
            $.ajax({
                url: baseURL + "searchClient",
                type: "POST",
                dataType: "json",
                data: request.term, // Asegurar que se envía correctamente
                success: function (data) {
                    console.log("Respuesta del servidor:", data);

                    // Asegurar que la respuesta sea un array de objetos
                    response(data.map(item => ({
                        label: item.nombre_completo + " - " + item.dui, // Lo que se muestra en la lista
                        value: item.nombre_completo, // Lo que se coloca en el input
                        dui: item.dui // Guardamos el DUI en la selección
                    })));
                },
                error: function () {
                    console.error("Error en la búsqueda de clientes");
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            campoBusqueda.value = ui.item.value; // Mostrar el nombre en el input
            duiCliente.value = "";
            nombreCliente.disabled = true;
            direccionCliente.disabled = true;
            telefonoCliente.disabled = true;
            correoCliente.disabled = true;
            noDuiCheckbox.disabled = true;
            buscarCliente(ui.item.dui); // Pasar el DUI como parámetro
        }
    });



    // Mostrar botón "Buscar" al escribir en el campo
    campoBusqueda.addEventListener("input", function () {
        if (tipoBusqueda.value === "dui" && campoBusqueda.value.trim().length >= 9) {
            btnBuscar.style.display = "inline-block";
        } else {
            btnBuscar.style.display = "none";
        }
    });

    // Evento para buscar cliente
    btnBuscar.addEventListener("click", function () {
        // Obtener el valor de búsqueda dependiendo del tipo seleccionado
        const valorBusqueda = campoBusqueda.value.trim();

        if (valorBusqueda === "") {
            Swal.fire('Error', 'Debe ingresar un valor para buscar.', 'error');
            return;
        }

        if (tipoBusqueda.value === "dui") {
            // Si es búsqueda por DUI, verificar que el formato sea correcto
            if (valorBusqueda.length === 10) {
                buscarCliente(valorBusqueda); // Pasar el DUI
            } else {
                Swal.fire('Error', 'El DUI debe tener un formato válido (00000000-0).', 'error');
            }
        } else if (tipoBusqueda.value === "nombre") {
            // Si es búsqueda por nombre, buscar por el nombre completo
            buscarClientePorNombre(valorBusqueda);
        }
    });

    function buscarCliente(dui) {
        idCliente.value = "";
        Swal.fire({
            title: 'Espere...',
            html: 'Buscando cliente...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch(baseURL + 'searchClient', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: dui
        })
            .then(response => {
                Swal.close();
                if (!response.ok) {
                    throw new Error('Cliente no encontrado');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    toastr.error(data.error);
                    throw new Error(data.error);
                }
                // Llenar los campos con los datos del cliente
                idCliente.value = data.id_cliente || "";
                duiCliente.value = data.dui || "";
                nombreCliente.value = data.nombre_completo || "";
                direccionCliente.value = data.direccion || "";
                telefonoCliente.value = data.telefono || "";
                correoCliente.value = data.correo || "";
            })
            .catch(error => {
                console.error(error.message);
                Swal.fire('Error', error.message, 'error');
                idCliente.value = "";
            });
    }

noDuiCheckbox.addEventListener("change", function () {
    if (this.value === "noDui") {  // Si se selecciona "El cliente no tiene DUI"
        duiCliente.value = "99999999-9";
        nombreCliente.value = "";
        direccionCliente.value = "";
        telefonoCliente.value = "";
        correoCliente.value = "";
        idCliente.value = "1";
        nombreCliente.disabled = false;
        direccionCliente.disabled = false;
        telefonoCliente.disabled = false;
        correoCliente.disabled = false;
    } else if (this.value === "clienteRapido") {
        duiCliente.value = "";
        nombreCliente.value = "";
        direccionCliente.value = "";
        telefonoCliente.value = "";
        correoCliente.value = "";
        idCliente.value = "2";
        nombreCliente.disabled = false;
        direccionCliente.disabled = false;
        telefonoCliente.disabled = false;
        correoCliente.disabled = false;
        duiCliente.disabled = false;
    }
});


    function seteoFecha() {
        const fechaInput = document.getElementById("fecha");

        // Obtener la fecha actual con la zona horaria local
        const hoy = new Date();
        const año = hoy.getFullYear();
        const mes = String(hoy.getMonth() + 1).padStart(2, "0"); // Sumar 1 porque los meses van de 0-11
        const dia = String(hoy.getDate()).padStart(2, "0");

        // Formatear como YYYY-MM-DD
        const fechaActual = `${año}-${mes}-${dia}`;

        // Asignar la fecha al input
        fechaInput.value = fechaActual;
    }

    $('#btnAgregarProductoMdl').click(function () {
        limpiarTabla();
    })

    const campoBusquedaProducto = $("#buscar_producto");
    const campoBusquedaProducto2 = $("#buscar_producto2");

    $('#agregarProductoTemp').on('shown.bs.modal', function () {
        // 🔹 Re-inicializa el autocomplete cada vez que se abre el modal
        campoBusquedaProducto.autocomplete({
            appendTo: "#agregarProductoTemp", // 🔹 Soluciona el problema de visualización en el modal
            source: function (request, response) {
                response([{ label: "Buscando resultados...", value: "" }]);
                $.ajax({
                    url: baseURL + "getProductoDesc",
                    type: "POST",
                    dataType: "json",
                    data: request.term, // Asegurar que se envía correctamente
                    success: function (data) {
                        console.log("Datos recibidos:", data);

                        response(data.map(item => ({
                            label: item.nombre + " - " + item.codigo_producto,
                            value: item.nombre,
                            codigo_producto: item.codigo_producto
                        })));
                    },
                    error: function () {
                        console.error("Error en la búsqueda de clientes");
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                console.log("Seleccionaste:", ui.item.value);
                campoBusquedaProducto.val(ui.item.value); // 🔹 Asigna el valor seleccionado
                buscarProducto(ui.item.codigo_producto);
            }
        });

        // 🔹 Asegura que el input recibe foco automáticamente
        setTimeout(() => {
            campoBusquedaProducto.focus();
        }, 300);
    });

    $('#modalDetalleSolicitud').on('shown.bs.modal', function () {
        // 🔹 Re-inicializa el autocomplete cada vez que se abre el modal
        campoBusquedaProducto2.autocomplete({
            appendTo: "#modalDetalleSolicitud", // 🔹 Soluciona el problema de visualización en el modal
            source: function (request, response) {
                response([{ label: "Buscando resultados...", value: "" }]);
                $.ajax({
                    url: baseURL + "getProductoDesc",
                    type: "POST",
                    dataType: "json",
                    data: request.term, // Asegurar que se envía correctamente
                    success: function (data) {
                        console.log("Datos recibidos:", data);

                        response(data.map(item => ({
                            label: item.nombre + " - " + item.codigo_producto,
                            value: item.nombre,
                            codigo_producto: item.codigo_producto
                        })));
                    },
                    error: function () {
                        console.error("Error en la búsqueda de clientes");
                    }
                });
            },
            minLength: 3,
            select: function (event, ui) {
                console.log("Seleccionaste:", ui.item.value);
                campoBusquedaProducto.val(ui.item.value); // 🔹 Asigna el valor seleccionado
                buscarProducto2(ui.item.codigo_producto);
            }
        });

        // 🔹 Asegura que el input recibe foco automáticamente
        setTimeout(() => {
            campoBusquedaProducto.focus();
        }, 300);
    });
});

document.getElementById('usuarioVendedor').addEventListener('change', function() {
    let select = this;
    let selectedText = select.options[select.selectedIndex].text; // Nombre y DUI
    let selectedValue = select.value; // id_usuario

    // Cambiar el texto del label
    document.getElementById('labelVendedor').textContent = selectedText;

    // Asignar el value al input oculto
    document.getElementById('id_vendedor').value = selectedValue;
});

function guardarContado() {
    var idCliente = document.getElementById('id_cliente').value.trim();
    var idVendedor = document.getElementById('id_vendedor').value.trim();
    /* var duiCheckbox = document.getElementById('noDuiCheckbox').checked; */
    var duiCheckbox = document.getElementById('noDuiCheckbox').value;

    var saldoAPagar = parseFloat(document.getElementById('saldoAPagar').value) || 0;
    var detalleSeries = document.getElementById('numeroSerie').value.trim();

    const productosAgregados = [];

    const filas = document.querySelectorAll('#productosSeleccionadosTbl tbody tr');

    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('td');

        const id_producto = celdas[0]?.textContent.trim(); // Cod
        const precioInput = celdas[3]?.querySelector('input'); // Precio unidad
        const cantidadInput = celdas[4]?.querySelector('input'); // Cantidad

        const precio = parseFloat(precioInput?.value.trim()) || 0;
        const cantidad = parseFloat(cantidadInput?.value.trim()) || 0;

        if (id_producto && cantidad > 0) {
            productosAgregados.push({
                id_producto: id_producto,
                precio: precio,
                cantidad: cantidad
            });
        }
    });


    console.log("productosAgregados es::::: ", productosAgregados);
    var datos = {
        id_cliente: idCliente,
        detalleSeries: detalleSeries,
        id_vendedor: idVendedor
    };

    // Si id_cliente está lleno, validar productos y saldoAPagar
    if (idCliente) {
        // Validar que productosAgregados tenga al menos un producto
        if (productosAgregados.length === 0) {
            toastr.error("Debe agregar al menos un producto.", "Error");
            return;
        }
        // Validar que saldoAPagar sea mayor que 0
        if (saldoAPagar <= 0) {
            toastr.error("El saldo a pagar debe ser mayor que 0.", "Error");
            return;
        }
    }
    // Si id_cliente está vacío y el checkbox está marcado, validar datos personales
    if (idCliente == '1' && duiCheckbox=='noDui') {
        datos.nombre = document.getElementById('nombreCliente').value.trim();
        datos.direccion = document.getElementById('direccionCliente').value.trim();
        datos.telefono = document.getElementById('telefonoCliente').value.trim();
        datos.correo = document.getElementById('correoCliente').value.trim();
        datos.dui = document.getElementById('duiCliente').value.trim();

        // Validar que los campos personales estén completos
        if (!datos.nombre || !datos.direccion || !datos.telefono || !datos.correo) {
            toastr.error("Debe completar todos los datos personales.", "Error");
            return;
        }

        // Validar que productosAgregados tenga al menos un producto
        if (productosAgregados.length === 0) {
            toastr.error("Debe agregar al menos un producto.", "Error");
            return;
        }
    }else if (idCliente == '2' && duiCheckbox=='clienteRapido') {
        datos.nombre = document.getElementById('nombreCliente').value.trim();
        datos.direccion = document.getElementById('direccionCliente').value.trim();
        datos.telefono = document.getElementById('telefonoCliente').value.trim();
        datos.correo = document.getElementById('correoCliente').value.trim();
        datos.dui = document.getElementById('duiCliente').value.trim();

        if (!datos.nombre || !datos.dui || datos.dui.length !== 10) {
            toastr.error("Debe completar todos los datos personales.", "Error");
            return;
        }
    }

    // Agregar los productos a los datos a enviar
    datos.productos = productosAgregados;
    datos.saldoAPagar = saldoAPagar;
    console.log("los productos agregados son:::: ", productosAgregados);
    console.log(datos);

    Swal.fire({
        title: 'Espere...',
        html: 'Procesando datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    fetch(baseURL + 'procesarSoliContado', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(datos)
    })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                cargarSolicitudesVarias();
                Swal.close();
                limpiarFormulario();
                toastr.success(data.message, "Registro guardado");
                Swal.fire({
                    title: "Registro generado!",
                    text: data.message,
                    icon: "success"
                });
            } else {
                Swal.close();
                toastr.error(data.message, "error");
            }
        })
        .catch(error => {
            console.error('Error al enviar datos:', error);
            toastr.error(data.message, "error");
        });

}

function limpiarFormulario() {
    document.getElementById('campoBusqueda').value = '';
    document.getElementById('tipoBusqueda').selectedIndex = 0;
    document.getElementById('contenedorListaClientes').style.display = 'none';
    document.getElementById('listaClientes').innerHTML = '';
    document.getElementById('noDoc').value = '';
    document.getElementById('fecha').value = '';
    document.getElementById('id_cliente').value = '';
    document.getElementById('duiCliente').value = '';
    document.getElementById('nombreCliente').value = '';
    document.getElementById('direccionCliente').value = '';
    document.getElementById('telefonoCliente').value = '';
    document.getElementById('correoCliente').value = '';
    document.getElementById('saldoAPagar').value = '';
    document.getElementById('numeroSerie').value = '';
    document.getElementById('noDuiCheckbox').checked = false;
    productosAgregados = [];

    // Limpiar la tabla de productos
    var tablaProductos = document.querySelector("#productosSeleccionadosTbl tbody");
    tablaProductos.innerHTML = '';

    // Restablecer valores ocultos
    document.querySelectorAll("input[type=hidden]").forEach(input => input.value = '');

    // Resetear otros valores según sea necesario...
}

/* ============================================================================================== */
/* ======================Aqui empiezan los metodo para agregar productos========================= */
function buscarProducto(search) {
    //var search = $('#buscar_producto').val();
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

    $.ajax({
        type: "POST",
        url: baseURL + 'getProductoExistencia',
        data: { search: search },
        dataType: "json",
        success: function (rsp) {
            console.log(rsp.success);
            if (rsp.success && Array.isArray(rsp.success)) {
                limpiarTabla();
                pintarResultados(rsp.success);
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

function buscarProducto2(search) {
    //var search = $('#buscar_producto').val();
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

    $.ajax({
        type: "POST",
        url: baseURL + 'getProductoExistencia',
        data: { search: search },
        dataType: "json",
        success: function (rsp) {
            console.log(rsp.success);
            if (rsp.success && Array.isArray(rsp.success)) {
                limpiarTabla();
                pintarResultados2(rsp.success);
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
var productosAgregados = [];

function limpiarTabla() {
    $('#dataTableBusquedaProducto tbody').empty();
}

function eliminarProductosSeleccionados(producto) {
    productosAgregados = productosAgregados.filter(p => p.codigo_producto !== producto.codigo_producto);
    console.log('Producto eliminado:', producto);
    console.log('Productos agregados:', productosAgregados);
    /* document.getElementById("valorArticulo").value = "";
    document.getElementById("valorPagoPrima").value = 0;
    document.getElementById("saldoAPagar").value = "";
    document.getElementById('prodAgregadosCant').textContent = 0; */
    cargarProductosSeleccionado(productosAgregados);
    //limpiarTablaProdSeleccionados();
}

function limpiarTablaProdSeleccionados() {
    $('#productosSeleccionadosTbl tbody').empty();
}

function pintarResultados(data) {
    var tbody = $('#dataTableBusquedaProducto tbody');
    tbody.empty();
    window.productosGlobal = data;
    data.forEach(function (producto, index) {
        console.log(producto);

        var productoAgregado = productosAgregados.find(function (prod) {
            return prod.codigo_producto === producto.codigo_producto;
        });


        var btnAgregar = productoAgregado ? 'style="display:none;"' : '';
        var btnEliminar = productoAgregado ? '' : 'style="display:none;"';

        var cantidad = productoAgregado ? productoAgregado.cantidad : '';
        //var disabled = cantidad > 0 ? 'disabled' : '';
        var disabled = (producto.disponibilidad === 0 || cantidad > 0) ? 'disabled' : '';

        var row = `<tr>
            <td>${producto.codigo_producto}</td>
            <td>${producto.nombre}</td>
            <td>${producto.marca}</td>
            <td>${producto.modelo}</td>
            <td>${producto.color}</td>
            <td>${producto.precio}</td>
            <td>${producto.disponibilidad}</td>
            <td>
                <div class="input-group col-sm-8">
                <input type="text" class="form-control soloNumeros" id="cantidad${index}" placeholder="Cantidad" value="${cantidad}" ${disabled}>
                </div>
            </td>
            <td>
                <button id="agregarBtn${index}" class="btn btn-primary" ${btnAgregar} ${disabled} onclick='agregarProducto(${index}, "cantidad${index}", "agregarBtn${index}", "eliminarBtn${index}")'>Agregar</button>
                <button id="eliminarBtn${index}" class="btn btn-danger" ${btnEliminar} onclick='eliminarProducto(${index}, "agregarBtn${index}", "eliminarBtn${index}")'>
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        tbody.append(row);
    });
}

function pintarResultados2(data) {
    var tbody = $('#tablaDetalleProductos tbody');

    data.forEach(function (producto, index) {
        console.log(producto);

        // Validar si ya existe en la tabla actual usando su id
        var yaExiste = false;
        tbody.find('tr').each(function () {
            const idExistente = $(this).find('td').eq(0).text().trim();
            if (idExistente == producto.id_producto) {
                yaExiste = true;
                return false; // salir del each
            }
        });

        if (yaExiste) {
            toastr.warning(`El producto "${producto.nombre}" ya se encuentra en la tabla`, "Producto repetido");
            return; // saltar este producto
        }
window.productosGlobal2 = data;
        // Preparar fila nueva
        var cantidad = '';
        var disabled = (producto.disponibilidad === 0) ? 'disabled' : '';
        var disponible = producto.disponibilidad;

        var row = `<tr>
            <td style="display:none;">${producto.id_producto}</td>
            <td>${producto.codigo_producto}</td>
            <td>${producto.nombre}</td>            
            <td>
                <input type="text" class="form-control precioProducto" id="precioDetalle${index}"
                    value="${parseFloat(producto.precio).toFixed(2)}"
                    oninput="formatearPrecio(this); calcularSubtotalDesdeInputs('precioDetalle${index}', 'cantidadDetalle${index}', 'subtotal${index}')">
            </td>

            <td>
                <div class="input-group col-sm-8">
                    <input type="text" class="form-control soloNumeros cantidadDetalleProducto" id="cantidadDetalle${index}" placeholder="Cantidad" value="${cantidad}" ${disabled}
                        oninput="calcularSubtotal(${parseFloat(producto.precio)}, 'cantidadDetalle${index}', 'subtotal${index}')">
                </div>
            </td>
            <td id="subtotal${index}">$0.00</td>
            <td>
                <button id="agregarBtnDetalle${index}" class="btn btn-primary" ${disabled}
                    onclick='agregarProducto2(${index},"cantidadDetalle${index}", "agregarBtnDetalle${index}", "eliminarBtnDetalle${index}")'>
                    Agregar
                </button>
                <button id="eliminarBtnDetalle${index}" class="btn btn-danger" style="display:none;"
                    onclick='eliminarProducto2(${index}, "agregarBtnDetalle${index}", "eliminarBtnDetalle${index}")'>
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;

        tbody.append(row);
    });
}

function formatearPrecio(input) {
    let val = input.value.replace(/[^0-9.]/g, '');

    const parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts[1];
    }

    if (parts[1]) {
        val = parts[0] + '.' + parts[1].substring(0, 2);
    }

    input.value = val;
}

function recalcularSubtotal(idPrecio, idCantidad, idSubtotal) {
    const precio = parseFloat(document.getElementById(idPrecio).value) || 0;
    const cantidad = parseInt(document.getElementById(idCantidad).value) || 0;
    const subtotal = (precio * cantidad).toFixed(2);
    document.getElementById(idSubtotal).innerText = `$${subtotal}`;

    // Recalcular el total general
    let total = 0;
    $('#tablaDetalleProductos tbody tr').each(function () {
        const celdaSubtotal = $(this).find('td[id^="subtotal"]');
        if (celdaSubtotal.length > 0) {
            const valor = parseFloat(celdaSubtotal.text().replace('$', '')) || 0;
            total += valor;
        }
    });

    $('#totalPagarDetalle').val(total.toFixed(2));
}



function calcularSubtotal(precio, inputId, subtotalId) {
    const cantidad = parseInt(document.getElementById(inputId).value);
    const subtotal = (!isNaN(cantidad) && cantidad > 0) ? cantidad * precio : 0;
    document.getElementById(subtotalId).innerText = `$${subtotal.toFixed(2)}`;

    calcularTotalPagar();
}


function recalcularSubtotalVendedor(idPrecio, idCantidad, idSubtotal) {
    const precio = parseFloat(document.getElementById(idPrecio).value) || 0;
    const cantidad = parseInt(document.getElementById(idCantidad).value) || 0;
    const subtotal = (precio * cantidad).toFixed(2);
    document.getElementById(idSubtotal).innerText = `$${subtotal}`;

    // Recalcular el total general
    let total = 0;
    $('#productosSeleccionadosTbl tbody tr').each(function () {
        const celdaSubtotal = $(this).find('td[id^="subtotal"]');
        if (celdaSubtotal.length > 0) {
            const valor = parseFloat(celdaSubtotal.text().replace('$', '')) || 0;
            total += valor;
        }
    });

    $('#saldoAPagar').val(total.toFixed(2));

}

function actualizarContadorProductosAgregados() {
    var cantidadProductosAgregados = productosAgregados.length;
    document.getElementById('prodAgregadosCant').textContent = cantidadProductosAgregados;
}

function eliminarProducto(index, agregarBtnId, eliminarBtnId) {
    var producto = window.productosGlobal[index];
    productosAgregados = productosAgregados.filter(p => p.codigo_producto !== producto.codigo_producto);
    console.log('Producto eliminado:', producto);

    var agregarBtn = document.getElementById(agregarBtnId);
    var eliminarBtn = document.getElementById(eliminarBtnId);
    agregarBtn.style.display = 'inline-block';
    eliminarBtn.style.display = 'none';

    var cantidadInputId = agregarBtnId.replace('agregarBtn', 'cantidad');
    var cantidadInput = document.getElementById(cantidadInputId);
    cantidadInput.removeAttribute('disabled');
    agregarBtn.removeAttribute('disabled');

    actualizarContadorProductosAgregados();
}

function eliminarProducto2(index, agregarBtnId, eliminarBtnId) {
    var producto = window.productosGlobal2[index];

    productos_detalles = productos_detalles.filter(p => p.id_producto !== producto.id_producto);
    console.log('Producto eliminado:', producto);

    var agregarBtn = document.getElementById(agregarBtnId);
    var eliminarBtn = document.getElementById(eliminarBtnId);
    agregarBtn.style.display = 'inline-block';
    eliminarBtn.style.display = 'none';

    var cantidadInputId = agregarBtnId.replace('agregarBtn', 'cantidad');
    var cantidadInput = document.getElementById(cantidadInputId);
    cantidadInput.removeAttribute('disabled');
    agregarBtn.removeAttribute('disabled');

    console.log("el nuevo array al eliminar es::: ", productos_detalles);
}

function agregarProducto(index, cantidadId, agregarBtnId, eliminarBtnId) {
    var producto = window.productosGlobal[index];
    var cantidad = parseInt($('#' + cantidadId).val());
    if (isNaN(cantidad) || cantidad <= 0) {
        toastr.error("Por favor, ingrese una cantidad válida.", "Error");
        return;
    }
    console.log(producto.disponibilidad);
    if (cantidad > producto.disponibilidad) {
        toastr.error("La cantidad ingresada es mayor que la disponibilidad del producto.", "Error");
        return;
    }

    producto.cantidad = cantidad;

    productosAgregados.push(producto);

    var agregarBtn = document.getElementById(agregarBtnId);
    var eliminarBtn = document.getElementById(eliminarBtnId);
    agregarBtn.style.display = 'none';
    eliminarBtn.style.display = 'block';

    var cantidadInputId = agregarBtnId.replace('agregarBtn', 'cantidad');
    var cantidadInput = document.getElementById(cantidadInputId);
    cantidadInput.setAttribute('disabled', 'disabled');
    actualizarContadorProductosAgregados();
}

function agregarProducto2(index, cantidadId, agregarBtnId, eliminarBtnId) {
    var producto = window.productosGlobal2[index];
    var cantidad = parseInt($('#' + cantidadId).val());
    if (isNaN(cantidad) || cantidad <= 0) {
        toastr.error("Por favor, ingrese una cantidad válida.", "Error");
        return;
    }
    console.log(producto.disponibilidad);
    if (cantidad > producto.disponibilidad) {
        toastr.error("La cantidad ingresada es mayor que la disponibilidad del producto.", "Error");
        return;
    }

    /* productos_detalles.push({
        id_producto: producto.id_producto,
        cantidad: cantidad
    });

    console.log("El retorno::: ", productos_detalles); */

    var agregarBtn = document.getElementById(agregarBtnId);
    var eliminarBtn = document.getElementById(eliminarBtnId);
    agregarBtn.style.display = 'none';
    eliminarBtn.style.display = 'block';

    var cantidadInputId = agregarBtnId.replace('agregarBtnDetalle', 'cantidadDetalle');
    var cantidadInput = document.getElementById(cantidadInputId);
    cantidadInput.setAttribute('disabled', 'disabled');
}

function confirmarAgregarProducto() {
    if (productosAgregados.length <= 0) {
        toastr.error("Agregue por lo menos un producto", "Error");
        return;
    }

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: "btn btn-success mr-2",
            cancelButton: "btn btn-info"
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: "Estas agregando los productos temporalmente",
        text: "¡Los productos se agregan definitivamente al finalizar el proceso!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, agregar!",
        cancelButtonText: "Continuar agregando",
        padding: '1rem',
        reverseButtons: true,
        didRender: () => {
            const confirmButton = Swal.getConfirmButton();
            const cancelButton = Swal.getCancelButton();
            confirmButton.classList.add('mr-2');
            cancelButton.classList.add('mr-2');
        }
    }).then((result) => {
        if (result.isConfirmed) {
            swalWithBootstrapButtons.fire({
                title: "Agregados",
                text: "Se han agregado productos temporalmente.",
                icon: "success"
            }).then(() => {
                // Aquí se llama a la función para cargar los productos seleccionados
                cargarProductosSeleccionado(productosAgregados);

                // Cierra el modal después de la confirmación
                $('#agregarProductoTemp').modal('hide');
            });
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            swalWithBootstrapButtons.fire({
                title: "Continuar agregando",
                text: "Continua agregando productos",
                icon: "info"
            });
        }
    });
}

function cargarProductosSeleccionado(productos) {
    const tbody = document.querySelector('#productosSeleccionadosTbl tbody');
    tbody.innerHTML = '';
    let totalGeneral = 0;
    productos.forEach((producto, index) => {
        const total = producto.precio * producto.cantidad;
        totalGeneral += total;
        const row = `
            <tr>
                <td style="display:none;">${producto.id_producto}</td>
                <td>${producto.codigo_producto}</td>
                <td>${producto.nombre}</td>
                <td>
                    <input type="text" class="form-control precioDetalleProducto" style="width:125px;" id="precioDetalle${producto.id_producto}" 
                        value="${producto.precio}"
                        oninput="formatearPrecio(this); recalcularSubtotalVendedor('precioDetalle${producto.id_producto}', 'cantidadDetalle${producto.id_producto}', 'subtotal${producto.id_producto}')">
                </td>
                <td>
                    <input type="text" class="form-control cantidadDetalleProducto" style="width:125px;" id="cantidadDetalle${producto.id_producto}" 
                        value="${producto.cantidad}" 
                        oninput="recalcularSubtotalVendedor('precioDetalle${producto.id_producto}', 'cantidadDetalle${producto.id_producto}', 'subtotal${producto.id_producto}')">
                </td>
                <td id="subtotal${producto.id_producto}">$${total.toFixed(2)}</td>
                <td>
                    <button class="btn btn-danger" onclick="eliminarProductoSeleccionado('${producto.codigo_producto}')">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
                
            </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
    document.getElementById('prodAgregadosCant').textContent = productos.length;
    document.getElementById('saldoAPagar').value = totalGeneral.toFixed(2);
}

function eliminarProductoSeleccionado(codigoProducto) {
    productosAgregados = productosAgregados.filter(p => p.codigo_producto !== codigoProducto);
    cargarProductosSeleccionado(productosAgregados);
}

/* ============================================================================================== */
function cargarSolicitudesVarias() {
    $.ajax({
        url: baseURL + "getContadoData",
        method: "GET",
        dataType: "json",
        success: function (response) {
            renderTablaSolicitudesVarias(response.data);
        },
        error: function () {
            alert('Ocurrió un error al comunicarse con el servidor.');
        }
    });
}

function renderTablaSolicitudesVarias(data) {
    console.log("Log renderTablaSolicitudesVarias ", data);
    window.listaSolicitudes = data; // o el arreglo que tengas


    if ($.fn.DataTable.isDataTable('#dataTableSolVariasTab')) {
        $('#dataTableSolVariasTab').DataTable().destroy();
    }

    const tbody = $('#dataTableSolVarias');
    tbody.empty();

    let contador = 1;

    data.forEach((solicitud, index) => {
        let colorEstado = '';
        let iconoEstado = '';

        // Determina el color y el icono según el estado
        switch (parseInt(solicitud.id_estado_actual)) {
            case 1:
                colorEstado = 'blue';
                iconoEstado = '<i class="fa-solid fa-check"></i>';
                break;
            case 2:
                colorEstado = 'green';
                iconoEstado = '<i class="fa-solid fa-check-double"></i>';
                break;
            case 3:
            case 4:
                colorEstado = 'red';
                iconoEstado = '<i class="fa-solid fa-ban"></i>';
                break;
            case 5:
                colorEstado = '#FFA500';
                iconoEstado = '<i class="fa-solid fa-check-double"></i>';
                break;
        }

        const estadoHTML = `<span style="color: ${colorEstado};">${solicitud.estado} ${iconoEstado}</span>`;

        // Lógica para botones según el perfil y la existencia de ruta_factura
        let botonAccion = '';
        const mostrarBoton = [1, 2, 3, 5].includes(idPerfil); // Asegúrate que idPerfil esté definido en el contexto global

        if (mostrarBoton) {
            console.log("entro a validar porque si el perfil");
            if (solicitud.ruta_factura && solicitud.ruta_factura.trim() !== '') {
                botonAccion = `
                    <button class="btn btn-secondary btn-sm" onclick="window.open('${solicitud.ruta_factura}', '_blank')">
                        <i class="fa fa-print"></i>
                    </button>
                `;
            } else {
                console.log("entro porque la ruta es vacia");
                botonAccion = `
                    <button class="btn btn-info btn-sm" onclick='mostrarDetalleSolicitud(${index})'>
                        <i class="fa fa-eye"></i> Ver Detalle
                    </button>
                `;
            }
        }

        const fila = ` 
            <tr>
                <td>${solicitud.numero_solicitud}</td>
                <td>${solicitud.dui}</td>
                <td>${solicitud.nombre_completo}</td>
                <td>${solicitud.fecha_creacion}</td>
                <td>${estadoHTML}</td>
                <td>${solicitud.user_creador}</td>
                <td>${botonAccion}</td>
            </tr>
        `;

        tbody.append(fila);
        contador++;
    });

    $('#dataTableSolVariasTab').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false
    });
}


/* var productos_detalles = []; */
function mostrarDetalleSolicitud(index) {
    setTimeout(() => {
        document.getElementById("buscar_producto2").value = "";
    }, 100);
    const solicitud = window.listaSolicitudes[index];
    console.log(solicitud);
    /* productos_detalles = []; */
    // Llenar información general
    $('#detalleIdSolicitud').val(solicitud.id_solicitud);
    $('#detalleNumeroSolicitud').text(solicitud.numero_solicitud);
    $('#detalleFechaSolicitud').text(solicitud.fecha_creacion);
    $('#detalleTipoSolicitud').text(solicitud.tipo_solicitud);
    $('#detalleDUI').text(solicitud.dui);
    $('#detalleNombreCliente').text(solicitud.nombre_completo);
    $('#detalleUsuarioCreador').text(solicitud.user_creador);
    //$('#detalleNumeroSeries').text(solicitud.detalle_series);
    $('#detalleNumeroSeries').val(solicitud.detalle_series);


    // Llenar productos
    const tbody = $('#tablaDetalleProductos tbody');
    tbody.empty();

    let total = 0;

    solicitud.productos.forEach(producto => {
        const precio = parseFloat(producto.precio_producto);
        const cantidad = parseInt(producto.cantidad_producto);
        const subtotal = precio * cantidad;
        total += subtotal;

        /* productos_detalles.push({
            id_producto: producto.id_producto,
            cantidad: cantidad
        }); */

        const row = `
            <tr>
                <td style="display:none;">${producto.id_producto}</td>
                <td>${producto.codigo_producto}</td>
                <td>${producto.nombre}</td>
                <td>
                    <input type="text" class="form-control precioDetalleProducto" id="precioDetalle${producto.id_producto}" 
                        value="${precio.toFixed(2)}"
                        oninput="formatearPrecio(this); recalcularSubtotal('precioDetalle${producto.id_producto}', 'cantidadDetalle${producto.id_producto}', 'subtotal${producto.id_producto}')">
                </td>
                <td>
                    <input type="text" class="form-control cantidadDetalleProducto" id="cantidadDetalle${producto.id_producto}" 
                        value="${cantidad}" 
                        oninput="recalcularSubtotal('precioDetalle${producto.id_producto}', 'cantidadDetalle${producto.id_producto}', 'subtotal${producto.id_producto}')">
                </td>
                <td id="subtotal${producto.id_producto}">$${subtotal.toFixed(2)}</td>

                <td>
                    <button class="btn btn-danger btn-sm btnEliminarProducto"><i class="fa fa-trash"></i></button>
                </td>
            </tr>
        `;
        tbody.append(row);
    });

    $('#totalPagarDetalle').val(`${total.toFixed(2)}`);
    // Delegar el evento de eliminar fila
    tbody.off('click').on('click', '.btnEliminarProducto', function () {
        const fila = $(this).closest('tr');
        const idProductoEliminar = fila.find('td').eq(0).text().trim();

        /* productos_detalles = productos_detalles.filter(p => p.id_producto != idProductoEliminar); */

        fila.remove();

        recalcularTotalPagarDetalle();
    });



    /* console.log("PRODUCTOS DETALLES:: ", productos_detalles); */
    // Mostrar el modal
    $('#modalDetalleSolicitud').modal('show');
}

function calcularTotalPagar() {
    let total = 0;

    $('#tablaDetalleProductos tbody tr').each(function () {
        const subtotalText = $(this).find('td').eq(5).text().replace('$', '').trim(); // Columna 6
        const subtotal = parseFloat(subtotalText);
        if (!isNaN(subtotal)) {
            total += subtotal;
        }
    });

    $('#totalPagar').text(`$${total.toFixed(2)}`);
}

const inputTotal = document.getElementById('totalPagarDetalle');

// Formatear el valor a dos decimales al perder el foco (blur)
inputTotal.addEventListener('blur', () => {
    let val = inputTotal.value;

    // Eliminar cualquier carácter que no sea dígito o punto
    val = val.replace(/[^0-9.]/g, '');

    // Validar que solo haya un punto decimal
    const parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts.slice(1).join('');
    }

    // Limitar a dos decimales
    if (parts[1]) {
        parts[1] = parts[1].substring(0, 2);
        val = parts[0] + '.' + parts[1];
    }

    // Si está vacío o no es número válido, poner 0.00
    if (isNaN(val) || val === '') {
        val = '0.00';
    }

    // Formatear a dos decimales fijo
    val = parseFloat(val).toFixed(2);

    inputTotal.value = val;
});

// Evitar que se ingresen caracteres inválidos mientras escribe
inputTotal.addEventListener('input', () => {
    // Solo permitir dígitos y un punto decimal
    let val = inputTotal.value;
    val = val.replace(/[^0-9.]/g, '');

    // Validar que solo haya un punto decimal
    const parts = val.split('.');
    if (parts.length > 2) {
        val = parts[0] + '.' + parts.slice(1).join('');
    }

    inputTotal.value = val;
});

function eliminarFactura() {
    Swal.fire({
        title: "¿Estás seguro?",
        text: "Si eliminas esta solicitud, también se borrarán los movimientos y productos asociados.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "¡Sí, eliminar!",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            const idSolicitud = $('#detalleIdSolicitud').val();

            Swal.fire({
                title: 'Espere...',
                html: 'Eliminando datos...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: baseURL + 'eliminarSolicitudContado',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ id_solicitud: idSolicitud }),
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: "¡Eliminado!",
                            text: response.message,
                            icon: "success"
                        }).then(() => {
                            // Cerrar modals antes de recargar
                            $('.modal').modal('hide');
                            cargarSolicitudesVarias();
                        });
                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error"
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Error",
                        text: "Ocurrió un error al intentar eliminar la solicitud.",
                        icon: "error"
                    });
                }
            });
        }
    });
}




function generarFactura() {
    Swal.fire({
        title: 'Espere...',
        text: 'Verificando datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    var valorInputDetalle = parseFloat((document.getElementById('totalPagarDetalle').value || '0').replace('$', '').replace(',', '')) || 0;
    const esNumeroValido = /^[0-9]+(\.[0-9]{1,2})?$/.test(valorInputDetalle);

    var saldoAPagarDetalle = parseFloat(valorInputDetalle);

    if (!esNumeroValido || isNaN(saldoAPagarDetalle) || saldoAPagarDetalle <= 0) {
        Swal.close();
        toastr.error("El saldo a pagar no es válido. Verifique que sea un número mayor a cero con hasta dos decimales.", "Error en carga de datos");
        return;
    }

    // Construir array de productos desde la tabla
    const productos = [];
    let hayCamposInvalidos = false;

    $('#tablaDetalleProductos tbody tr').each(function () {
        const idProducto = $(this).find('td').eq(0).text().trim();
        const precioInput = $(this).find('input[id^="precioDetalle"]');
        const cantidadInput = $(this).find('input[id^="cantidadDetalle"]');

        const precio = parseFloat(precioInput.val());
        const cantidad = parseInt(cantidadInput.val());

        if (!precio || precio <= 0 || isNaN(precio)) {
            hayCamposInvalidos = true;
            precioInput.addClass('is-invalid');
        } else {
            precioInput.removeClass('is-invalid');
        }

        if (!cantidad || cantidad <= 0 || isNaN(cantidad)) {
            hayCamposInvalidos = true;
            cantidadInput.addClass('is-invalid');
        } else {
            cantidadInput.removeClass('is-invalid');
        }

        productos.push({
            id_producto: idProducto,
            precio: precio,
            cantidad: cantidad
        });
    });

    console.log("Respuesta de hayCamposInvalidos:::: ", hayCamposInvalidos);
    if (hayCamposInvalidos) {
        Swal.close();
        Swal.fire({
            icon: 'warning',
            title: 'Campos inválidos',
            text: 'Verifique que todos los productos tengan un precio y cantidad válidos mayores a 0.',
        });
        return;
    }


    if (productos.length === 0) {
        Swal.close();
        toastr.error("Debe seleccionar al menos un producto válido con precio y cantidad.", "Error en carga de datos");
        return;
    }

    const idSolicitud = $('#detalleIdSolicitud').val();
    const detalleNumeroSeries = document.getElementById('detalleNumeroSeries').value.trim();
    /* alert(detalleNumeroSeries); */

    if (!idSolicitud) {
        Swal.close();
        toastr.error("No se ha encontrado la solicitud.", "Error");
        return;
    }
    console.log("el valor de productos es::::: ", productos);

    // Enviar datos por AJAX
    $.ajax({
        url: baseURL + 'generarPagoContado',
        method: 'POST',
        data: JSON.stringify({
            idSolicitud: idSolicitud,
            productos: productos,
            saldoAPagar: saldoAPagarDetalle,
            detalleNumeroSeries: detalleNumeroSeries
        }),
        contentType: 'application/json',
        success: function (response) {
            Swal.close();

            if (response.status === 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    if (response.documento) {
                        Swal.showLoading();
                        descargarDocumento(response.documento);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Atención',
                    text: response.message,
                    icon: 'warning'
                });
            }
        },
        error: function () {
            Swal.close();
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un problema al procesar los pagos. Por favor, inténtalo de nuevo.',
                icon: 'error'
            });
        }
    });
}



/* function generarFactura() {
    Swal.fire({
        title: 'Espere...',
        text: 'Verificando datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    var valorInputDetalle = parseFloat((document.getElementById('totalPagarDetalle').value || '0').replace('$', '').replace(',', '')) || 0;
    const esNumeroValido = /^[0-9]+(\.[0-9]{1,2})?$/.test(valorInputDetalle);

    // Convertir a número
    var saldoAPagarDetalle = parseFloat(valorInputDetalle);

    // Validación
    if (!esNumeroValido || isNaN(saldoAPagarDetalle) || saldoAPagarDetalle <= 0) {
        Swal.close();
        toastr.error("El saldo a pagar no es válido. Verifique que sea un número mayor a cero con hasta dos decimales.", "Error en carga de datos");
        return;
    }

    console.log("antes de enviar el producto es:::: ", productos_detalles);
    // Validar que productos_detalles no esté vacío
    if (!Array.isArray(productos_detalles) || productos_detalles.length === 0) {
        Swal.close();
        toastr.error("Debe seleccionar un producto.", "Error en carga de datos");
        return;
    }

    // Obtener el valor del input detalleIdSolicitud
    const idSolicitud = $('#detalleIdSolicitud').val();

    // Validar que se haya obtenido un valor
    if (!idSolicitud) {
        Swal.close();
        toastr.error("No se ha encontrado la solicitud.", "Error");
        return;
    }

    // Enviar datos por AJAX
    $.ajax({
        url: baseURL + 'generarPagoContado',
        method: 'POST',
        data: JSON.stringify({
            idSolicitud: idSolicitud,
            productos: productos_detalles,
            saldoAPagar: saldoAPagarDetalle
        }),
        contentType: 'application/json',
        success: function (response) {
            Swal.close();

            if (response.status === 'success') {
                Swal.fire({
                    title: '¡Éxito!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    if (response.documento) {
                        Swal.showLoading();
                        descargarDocumento(response.documento); // Descargar documento si existe
                    }
                });
            } else {
                Swal.fire({
                    title: 'Atención',
                    text: response.message,
                    icon: 'warning'
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.close();
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un problema al procesar los pagos. Por favor, inténtalo de nuevo.',
                icon: 'error'
            });
        }
    });

} */
function descargarDocumento(ruta) {
    let countdown = 5; // Tiempo total de la cuenta regresiva
    const totalTime = countdown;

    Swal.fire({
        title: 'Descargando...',
        html: `
            <div>Esperando... <span id="countdownText">${countdown} segundos</span></div>
            <div class="progress" style="height: 25px;">
                <div id="progressBar" class="progress-bar bg-info" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        `,
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            const countdownText = document.getElementById('countdownText');
            const progressBar = document.getElementById('progressBar');

            const interval = setInterval(() => {
                countdown--;

                countdownText.textContent = `${countdown} segundos`;
                let progress = ((totalTime - countdown) / totalTime) * 100;
                progressBar.style.width = progress + '%';
                progressBar.setAttribute('aria-valuenow', progress);

                if (countdown === 0) {
                    clearInterval(interval);

                    // ⛔ Cerrar modal Bootstrap antes de descargar
                    $('#modalDetalleSolicitud').modal('hide');

                    // Descargar el archivo
                    const fileUrl = baseURL + ruta;
                    window.open(fileUrl, '_blank');

                    cargarSolicitudesVarias();
                    // Cerrar el modal actual
                    Swal.close();

                    // Mostrar mensaje final (opcional)
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Completado',
                            text: 'Procesando información...',
                            icon: 'success',
                            timer: 1000,
                            showConfirmButton: false
                        });
                    }, 500); // Un pequeño retardo para mejor UX
                }

            }, 1000);
        }
    });
}

function recalcularTotalPagarDetalle() {
    let total = 0;

    $('#tablaDetalleProductos tbody tr').each(function () {
        const subtotalTexto = $(this).find('td').eq(5).text().replace('$', '').trim();
        const subtotal = parseFloat(subtotalTexto) || 0;
        total += subtotal;
    });

    $('#totalPagarDetalle').val(total.toFixed(2));
}

function calcularSubtotal(precioUnitario, idCantidad, idSubtotal) {
    const cantidad = parseFloat($('#' + idCantidad).val()) || 0;
    const subtotal = precioUnitario * cantidad;

    $('#' + idSubtotal).text(`$${subtotal.toFixed(2)}`);

    recalcularTotalPagarDetalle();
}

function calcularSubtotalDesdeInputs(idPrecio, idCantidad, idSubtotal) {
    const precio = parseFloat($('#' + idPrecio).val()) || 0;
    const cantidad = parseFloat($('#' + idCantidad).val()) || 0;
    const subtotal = precio * cantidad;

    $('#' + idSubtotal).text(`$${subtotal.toFixed(2)}`);

    recalcularTotalPagarDetalle();
}

