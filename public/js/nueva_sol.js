document.addEventListener('DOMContentLoaded', function () {
    $('.next-btn').click(function () {
        var currentCollapse = $(this).closest('.collapse');
        var nextCollapse = currentCollapse.closest('.card').next('.card').find('.collapse');

        currentCollapse.collapse('hide');
        nextCollapse.collapse('show');
    });

    $('.prev-btn').click(function () {
        var currentCollapse = $(this).closest('.collapse');
        var prevCollapse = currentCollapse.closest('.card').prev('.card').find('.collapse');

        currentCollapse.collapse('hide');
        prevCollapse.collapse('show');
    });

    $('#btnAgregarProductoMdl').click(function () {
        limpiarTabla();
    });

    /* **************************************************************valida los input y select  */
    const viveEnCasaPropiaSelect = document.getElementById('COpropiaCN');
    const promesaVentaSelect = document.getElementById('COpromesaVenta');
    const alquiladaSelect = document.getElementById('COalquilada');
    const estadoCivilSelect = document.getElementById('COestadoCivil');
    const nombreConyugueInput = document.getElementById('nombreConyugueCN');

    function updateFields() {
        const viveEnCasaPropia = viveEnCasaPropiaSelect.value === 'SI';
        promesaVentaSelect.disabled = viveEnCasaPropia;
        alquiladaSelect.disabled = viveEnCasaPropia;

        if (viveEnCasaPropia) {
            if (promesaVentaSelect.disabled) {
                promesaVentaSelect.value = 'NO';
            }
            if (alquiladaSelect.disabled) {
                alquiladaSelect.value = 'NO';
            }
        }

        const estadoCivil = estadoCivilSelect.value;
        nombreConyugueInput.disabled = estadoCivil === 'Soltera/o';

        if (nombreConyugueInput.disabled && estadoCivil === 'Soltera/o') {
            nombreConyugueInput.value = ''; // Limpia el campo si está deshabilitado
        }
    }

    viveEnCasaPropiaSelect.addEventListener('change', updateFields);
    estadoCivilSelect.addEventListener('change', updateFields);

    // Inicializa el estado de los campos al cargar la página
    updateFields();
    /* **************************************************************************************** */
    const valorPagoPrimaInput = document.getElementById('valorPagoPrima');
    const saldoAPagarInput = document.getElementById('saldoAPagar');

    const montoCuotaInput = document.getElementById('montoCuota');
    const valorArticuloInput = document.getElementById('valorArticulo');
    const cantidadCuotasSelect = document.getElementById('cantidadCuotas');
    const montoTotalPagarInput = document.getElementById('montoTotalPagar');
    const valorPrimaInput = document.getElementById('valorPagoPrima');

    valorPagoPrimaInput.addEventListener('input', actualizarSaldoAPagar);
    cantidadCuotasSelect.addEventListener('change', actualizarMontoCuota);

    function actualizarSaldoAPagar() {
        const valorArticulo = parseFloat(valorArticuloInput.value) || 0;
        const valorPagoPrima = parseFloat(valorPagoPrimaInput.value) || 0;
        const saldoAPagar = valorArticulo - valorPagoPrima;

        const cant_meses = parseInt(cantidadCuotasSelect.cant_meses) || 0;
        saldoAPagarInput.value = saldoAPagar.toFixed(2);

        if (cant_meses > 0) {
            actualizarMontoCuota();
        }
    }

    function actualizarMontoCuota() {
        const saldoAPagar = parseFloat(saldoAPagarInput.value) || 0;
        const valor_porcentual = parseFloat(cantidadCuotasSelect.value) || 0;
        const opcionSeleccionada = cantidadCuotasSelect.options[cantidadCuotasSelect.selectedIndex];
        const cant_meses = parseInt(opcionSeleccionada.text) || 0;

        const valorCuota = cant_meses > 0 ? saldoAPagar * valor_porcentual : 0;
        montoCuotaInput.value = valorCuota.toFixed(2);
        const valorTotalAPagar = (parseFloat(valorCuota) * cant_meses) + parseFloat(valorPrimaInput.value);
        montoTotalPagarInput.value = valorTotalAPagar.toFixed(2);
    }

    cargarDepartamentos();
    cargarComisiones();
    var today = new Date().toISOString().split('T')[0];
    document.getElementById('creacionDocumento').value = today;

    document.getElementById('guardar_solicitud').addEventListener('click', function () {
        var accordion = document.getElementById('accordionSolicitud');
        var cards = accordion.querySelectorAll('.card');
        var data = {
            datos_personales: {},
            referencias_laborales: {},
            referencias_familiares: {},
            referencias_personas_no_familiar: {},
            referencias_crediticias: {},
            analisis_socioeconomico: {},
            co_deudor: {},
            plan_de_pago: {},
            productosSolicitud: []
        };
        var errorFound = false; // Flag to track if an error was found

        cards.forEach(function (card) {
            if (errorFound) return; // Exit the loop if an error has been found
            var cardClass = card.classList[1];
            if (data[cardClass] !== undefined) {
                console.log('cardClass:', cardClass);
                if (cardClass === 'datos_personales') {
                    // Obtener específicamente el id_cliente
                    var idClienteElement = card.querySelector('input[name="id_cliente"]');
                    if (idClienteElement) {
                        data.datos_personales.id_cliente = idClienteElement.value;
                    }
                } else if (cardClass === 'referencias_familiares') {
                    var references = [];
                    var referenceInputs = card.querySelectorAll('input[name],select[name]');
                    var referenceCount = referenceInputs.length / 6;
                    var fullReferencesCount = 0;
                    for (var i = 0; i < referenceCount; i++) {
                        var reference = {};
                        var startIndex = i * 6;
                        var filledFields = 0;
                        for (var j = startIndex; j < startIndex + 6; j++) {
                            if (referenceInputs[j].value.trim() !== '') {
                                filledFields++;
                            }
                        }
                        if (filledFields === 6) {
                            reference.nombre = referenceInputs[startIndex].value;
                            reference.parentesco = referenceInputs[startIndex + 1].value;
                            reference.direccion = referenceInputs[startIndex + 2].value;
                            reference.telefono = referenceInputs[startIndex + 3].value;
                            reference.lugar_trabajo = referenceInputs[startIndex + 4].value;
                            reference.telefono_trabajo = referenceInputs[startIndex + 5].value;
                            references.push(reference);
                        }

                        if (filledFields === 6) {
                            fullReferencesCount++;
                        }
                    }
                    if (fullReferencesCount >= 1) {
                        data[cardClass] = references;
                    } else {
                        toastr.error("Campos requeridos", "Al menos una referencia familiar debe estar completamente llena.");
                        errorFound = true; // Set the flag to true to indicate an error was found
                        return;
                    }
                } else if (cardClass === 'referencias_personas_no_familiar') {
                    var references = [];
                    var referenceInputs = card.querySelectorAll('input[name]');
                    var referenceCount = referenceInputs.length / 5;
                    var atLeastOneComplete = false; // Flag to check if at least one reference is complete

                    for (var i = 0; i < referenceCount; i++) {
                        var reference = {};
                        var startIndex = i * 5;
                        var filledFields = 0;

                        for (var j = startIndex; j < startIndex + 5; j++) {
                            if (referenceInputs[j].value.trim() !== '') {
                                filledFields++;
                            }
                        }

                        if (filledFields >= 5) {
                            reference.nombre = referenceInputs[startIndex].value;
                            reference.direccion = referenceInputs[startIndex + 1].value;
                            reference.telefono = referenceInputs[startIndex + 2].value;
                            reference.lugar_trabajo = referenceInputs[startIndex + 3].value;
                            reference.telefono_trabajo = referenceInputs[startIndex + 4].value;
                            references.push(reference);
                            atLeastOneComplete = true; // Set flag to true if a complete reference is found
                        }
                    }

                    if (atLeastOneComplete) {
                        data[cardClass] = references;
                    } else {
                        toastr.error("Campos requeridos", "Al menos una referencia no familiar debe estar completamente llena.");
                        errorFound = true; // Set the flag to true to indicate an error was found
                        return;
                    }
                } else if (cardClass === 'referencias_crediticias') {
                    console.log('Entrando en referencias_crediticias');
                    var references = [];
                    var referenceRows = card.querySelectorAll('tbody tr');
                    referenceRows.forEach(function (row) {
                        var reference = {};
                        var cells = row.querySelectorAll('td input[name], td select[name]');
                        console.log('Celdas encontradas:', cells.length);
                        if (cells.length > 0) {
                            var filledFields = 0;
                            for (var i = 0; i < cells.length; i++) {
                                if (cells[i].value.trim() !== '') {
                                    filledFields++;
                                }
                            }
                            console.log('Campos llenos en esta fila:', filledFields);
                            if (filledFields >= 3) {
                                reference.nombre = cells[0].value;
                                reference.telefono = cells[1].value;
                                reference.monto_credito = cells[2].value;
                                reference.periodos = cells[3].value;
                                reference.plazo = cells[4].value;
                                reference.estado = cells[5].value;
                                references.push(reference);
                            }
                        }
                    });
                    data[cardClass] = references;
                } else if (cardClass == 'analisis_socioeconomico') {
                    var analisisSocioeconomicoData = {};

                    // Obtiene todos los inputs y selects dentro del card, excepto los del div con name="plan_de_pago"
                    var elements = card.querySelectorAll('input[name], select[name]');
                    elements.forEach(function (element) {
                        var name = element.getAttribute('name');
                        var value = element.value;

                        if (!element.closest('div[name="plan_de_pago"]')) {
                            analisisSocioeconomicoData[name] = value;
                        }
                    });

                    var estadoLabel = document.getElementById('estado-label').textContent;
                    var estadoValue = estadoLabel.split(':')[1]?.trim() || '';
                    analisisSocioeconomicoData['estado_label'] = estadoValue;
                    data['analisis_socioeconomico'] = analisisSocioeconomicoData;
                    //-------------------------------------------------------------------------------------//
                    // Seleccionamos el div con id "plan_de_pago"
                    const planDePagoDiv = document.querySelector('#plan_de_pago');
                    const planDePagoData = {};

                    planDePagoDiv.querySelectorAll('input, select').forEach(element => {
                        const id = element.id;
                        let value = element.value.trim();

                        // Verificar si el elemento es el select "cantidadCuotas"
                        if (id === 'cantidadCuotas') {
                            // Recuperar el atributo "cant_meses" del option seleccionado en lugar del value
                            const selectedOption = element.options[element.selectedIndex];
                            value = selectedOption.getAttribute('cant_meses') || '';
                        }

                        if (value !== '') {
                            planDePagoData[id] = value;
                        }
                    });

                    data['plan_de_pago'] = planDePagoData;
                    console.log('Datos del plan de pago:', data['plan_de_pago']);
                } else if (cardClass === 'co_deudor') {
                    const coDeudorDiv = document.querySelector('#co_deudor');

                    const coDeudorData = {
                        referencias: []
                    };

                    coDeudorDiv.querySelectorAll('input, select').forEach(element => {
                        if (!element.closest('#referenciasCodeudor')) {
                            coDeudorData[element.id] = element.value;
                        }
                    });

                    function todosCamposLlenos(prefix) {
                        const campos = ['COnombreRef', 'COparentescoRef', 'COdireccionRef', 'COtelRef'];
                        return campos.every(campo => {
                            const input = document.querySelector(`#${prefix}`);
                            console.log(`Verificando campo: #${prefix} - Valor: ${input ? input.value.trim() : 'No encontrado'}`);
                            return input && input.value.trim() !== '';
                        });
                    }

                    [1, 2, 3].forEach(i => {
                        const prefix = `CO`;
                        if (todosCamposLlenos(`COnombreRef${i}`)) {
                            const referencia = {
                                nombre: document.querySelector(`#COnombreRef${i}`).value,
                                parentesco: document.querySelector(`#COparentescoRef${i}`).value,
                                direccion: document.querySelector(`#COdireccionRef${i}`).value,
                                telefono: document.querySelector(`#COtelRef${i}`).value
                            };
                            console.log(`Añadiendo referencia:`, referencia);
                            coDeudorData.referencias.push(referencia);
                        }
                    });

                    data.co_deudor = coDeudorData;
                } else if (cardClass === 'referencias_laborales') {
                    var card = document.querySelector('.referencias_laborales');
                    var referenciasLaboralesData = {};

                    var elements = card.querySelectorAll('input[name], select[name]');
                    elements.forEach(function (element) {
                        var name = element.getAttribute('name');
                        var value = element.value;
                        referenciasLaboralesData[name] = value;
                    });

                    data.referencias_laborales = referenciasLaboralesData;
                }
            }
        });

        if (errorFound) return;

        if (productosAgregados.length <= 0) {
            toastr.error("Agregue por lo menos un producto", "Error");
            return;
        } else {
            data.productosSolicitud = productosAgregados;
        }

        validarClienteAgregado(data.datos_personales);
        validarRef_laborales(data.referencias_laborales);
        Swal.fire({
            title: 'Creando solicitud espere un momento...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        fetch(baseURL + 'procesar_nueva_sol', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())  // Convertir la respuesta a JSON
            .then(responseData => {
                if (responseData.success) {
                    console.log('Datos enviados con éxito:', responseData);
                    toastr.success("Solicitud guardada", responseData.message);
                } else {
                    console.error('Error en la respuesta:', responseData.message);
                    toastr.error("Error", responseData.message);
                }
            })
            .catch(error => {
                console.error('Error al enviar datos:', error);
                toastr.error("Error al guardar", "Hubo un problema al guardar la solicitud.");
            })
            .finally(() => {
                Swal.close(); // Cerrar el loading independientemente del resultado
            });

    });

    $('.ingresos, .egresos').on('input', function () {
        calculateSums();
    });


    calculateSums();
});

function validarClienteAgregado(datoCliente) {
    console.log(datoCliente);
    if (!datoCliente.id_cliente || datoCliente.id_cliente.trim() === '') {
        toastr.error("Campos requeridos", "Debe agregar un cliente.");
        return;
    }
}
function validarRef_laborales(datos) {
    var referenciaLaboralLlena = Object.values(datos).every(function (value) {
        return value.trim() !== '';
    });
    if (!referenciaLaboralLlena) {
        toastr.error("Campos requeridos", "Todos los campos de las referencias laborales deben estar llenos.");
        return;
    }
}

function cargarDepartamentos() {
    $.ajax({
        type: 'GET',
        url: baseURL + 'departamentos',
        dataType: 'json',
        success: function (response) {
            var select = $('#deptoCliente');
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

function cargarMunicipios(deptoId, municipioId) {
    $.ajax({
        type: 'POST',
        url: baseURL + 'municipios',
        data: { departamento_id: deptoId },
        dataType: 'json',
        success: function (data) {
            var selectMunicipios = $('#muniCliente');
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


                if (municipio.id == municipioId) {
                    option.prop('selected', true);
                }
            });
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar los municipios:", status, error);
        }
    });
}

function calculateSums() {
    var totalIngresos = 0;
    var totalEgresos = 0;

    // Suma los valores de los campos de ingresos
    $('.ingresos').each(function () {
        var valorEntero = $(this).val().replace(/,/g, '');
        console.log(valorEntero)
        var value = parseFloat(valorEntero) || 0;
        totalIngresos += value;
    });

    // Suma los valores de los campos de egresos
    $('.egresos').each(function () {
        var value = parseFloat($(this).val().replace(/,/g, '')) || 0;
        totalEgresos += value;
    });

    // Actualiza los campos de total de ingresos, egresos y diferencia
    $('#totalIngresos').val(totalIngresos.toLocaleString());
    $('#totalEgresos').val(totalEgresos.toLocaleString());
    $('#diferencia').val((totalIngresos - totalEgresos).toLocaleString());
}

function buscarCliente() {
    var dui = document.getElementById('duiBuscarCliente').value;

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
                Swal.close();
                console.log("error", data.error);
                toastr.error(data.error);
                throw new Error(data.error);
            }
            var cliente = data;
            console.log(cliente);
            document.getElementById('id_cliente').value = cliente.id_cliente;
            document.getElementById('nombrePersonal').value = cliente.nombre_completo;
            document.getElementById('duiPersonal').value = cliente.dui;
            document.getElementById('fechaNacimiento').value = cliente.fecha_nacimiento;
            document.getElementById('direccionActual').value = cliente.direccion;
            document.getElementById('telefono').value = cliente.telefono;
            document.getElementById('estadoCivil').value = cliente.estado_civil;
            document.getElementById('nombreConyugue').value = cliente.nombre_conyugue;
            document.getElementById('dirTrabajoConyugue').value = cliente.direccion_trabajo_conyugue;
            document.getElementById('telTrabajoConyugue').value = cliente.telefono_trabajo_conyugue;
            document.getElementById('nombresPadres').value = cliente.nombre_padres;
            document.getElementById('direccionDeLosPadres').value = cliente.direccion_padres;
            document.getElementById('telPadres').value = cliente.telefono_padres;

            document.getElementById('Cpropia').value = cliente.CpropiaCN;
            document.getElementById('CpromesaVenta').value = cliente.CpromesaVentaCN;
            document.getElementById('Calquilada').value = cliente.CalquiladaCN;
            document.getElementById('aQuienPertenece').value = cliente.aQuienPerteneceCN;
            document.getElementById('telPropietario').value = cliente.telPropietarioCN;
            document.getElementById('tiempoDeVivirDomicilio').value = cliente.tiempoDeVivirDomicilioCN;


            var deptoSelect = document.getElementById('deptoCliente');
            for (var i = 0; i < deptoSelect.options.length; i++) {
                if (deptoSelect.options[i].value == cliente.departamento) {
                    deptoSelect.selectedIndex = i;
                    break;
                }
            }


            cargarMunicipios(cliente.departamento, cliente.municipio);
            Swal.close();
        })
        .catch(error => {
            console.log(error.message);
            Swal.close();
        });
}

function isClienteCompleto(cliente) {
    for (let key in cliente) {
        if (cliente.hasOwnProperty(key) && (cliente[key] === "" || cliente[key] === null || cliente[key] === undefined)) {
            return false;
        }
    }
    return true;
}

var productosAgregados = [];


function buscarProducto() {
    var search = $('#buscar_producto').val();
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
        url: baseURL + 'getProducts',
        data: { search: search },
        dataType: "json",
        success: function (rsp) {
            console.log(rsp);
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

function limpiarTabla() {
    $('#dataTableBusquedaProducto tbody').empty();
}


function pintarResultados(data) {
    var tbody = $('#dataTableBusquedaProducto tbody');
    tbody.empty();

    data.forEach(function (producto, index) {
        console.log(producto);

        var productoAgregado = productosAgregados.find(function (prod) {
            return prod.codigo_producto === producto.codigo_producto;
        });


        var btnAgregar = productoAgregado ? 'style="display:none;"' : '';
        var btnEliminar = productoAgregado ? '' : 'style="display:none;"';

        var cantidad = productoAgregado ? productoAgregado.cantidad : '';
        var disabled = cantidad > 0 ? 'disabled' : '';

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
                <button id="agregarBtn${index}" class="btn btn-primary" ${btnAgregar} onclick='agregarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')},"cantidad${index}", "agregarBtn${index}" ,"eliminarBtn${index}")'>Agregar</button>
                <button id="eliminarBtn${index}" class="btn btn-danger" ${btnEliminar} onclick='eliminarProducto(${JSON.stringify(producto).replace(/"/g, '&quot;')}, "agregarBtn${index}", "eliminarBtn${index}")'>
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        tbody.append(row);
    });
}

function actualizarContadorProductosAgregados() {
    var cantidadProductosAgregados = productosAgregados.length;
    document.getElementById('prodAgregadosCant').textContent = cantidadProductosAgregados;
}

function agregarProducto(producto, cantidadId, agregarBtnId, eliminarBtnId) {
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



function eliminarProducto(producto, agregarBtnId, eliminarBtnId) {

    productosAgregados = productosAgregados.filter(p => p.codigo_producto !== producto.codigo_producto);
    console.log('Producto eliminado:', producto);

    var agregarBtn = document.getElementById(agregarBtnId);
    var eliminarBtn = document.getElementById(eliminarBtnId);
    agregarBtn.style.display = 'inline-block';
    eliminarBtn.style.display = 'none';

    var cantidadInputId = agregarBtnId.replace('agregarBtn', 'cantidad');
    var cantidadInput = document.getElementById(cantidadInputId);
    cantidadInput.removeAttribute('disabled');
    actualizarContadorProductosAgregados();
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
        title: "Estas agregando los productos temporalmente a la solicitud",
        text: "¡Los productos se agregan definitivamente al finalizar la solicitud!",
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
                text: "Se han agregado productos temporalmente a la solicitud.",
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


function cargarProductosSeleccionado(productosAgregados) {
    var tbody = $('#productosSeleccionadosTbl tbody');

    tbody.empty();
    var sumaTotalProductos = 0;
    productosAgregados.forEach(function (producto, index) {
        var precio_total = (producto.precio * producto.cantidad).toFixed(2);
        sumaTotalProductos += parseFloat(precio_total);

        var row = `<tr>
            <td>${producto.codigo_producto}</td>
            <td>${producto.nombre}</td>
            <td>${producto.precio}</td>
            <td>${producto.cantidad}</td>
            <td>${precio_total}</td>
            <td>
                <button id="eliminarBtnS${index}" style="display: block;" class="btn btn-danger" onclick='eliminarProductosSeleccionados(${JSON.stringify(producto).replace(/"/g, '&quot;')})'>
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        </tr>`;
        tbody.append(row);
    });

    document.getElementById("valorArticulo").value = sumaTotalProductos.toFixed(2);
}

function limpiarTablaProdSeleccionados() {
    $('#productosSeleccionadosTbl tbody').empty();
}


function eliminarProductosSeleccionados(producto) {
    productosAgregados = productosAgregados.filter(p => p.codigo_producto !== producto.codigo_producto);
    console.log('Producto eliminado:', producto);
    console.log('Productos agregados:', productosAgregados);

    limpiarTablaProdSeleccionados();
}

document.addEventListener('DOMContentLoaded', function () {
    function calcularEstado() {
        var ingresoMensual = parseFloat(document.getElementById('ingresoMensual').value) || 0;
        var salario = parseFloat(document.getElementById('salarioIng').value) || 0;
        var otrosIngresos = parseFloat(document.getElementById('otrosIngresos').value) || 0;

        var egresoMensual = parseFloat(document.getElementById('egresoMensual').value) || 0;
        var pagoCasa = parseFloat(document.getElementById('pagoCasa').value) || 0;
        var gastosVida = parseFloat(document.getElementById('gastosVida').value) || 0;
        var otrosEgresos = parseFloat(document.getElementById('otrosEgresos').value) || 0;

        var totalIngresos = ingresoMensual + salario + otrosIngresos;
        var totalEgresos = egresoMensual + pagoCasa + gastosVida + otrosEgresos;
        var diferencia = totalIngresos - totalEgresos;

        document.getElementById('totalIngresos').value = totalIngresos.toFixed(2);
        document.getElementById('totalEgresos').value = totalEgresos.toFixed(2);
        document.getElementById('diferencia').value = diferencia.toFixed(2);

        var estado;
        var porcentaje;
        var colorClass;
        var ratio = (diferencia / totalIngresos) * 100;

        if (ratio <= 5) {
            estado = "En Riesgo Alto";
            porcentaje = 20;
            colorClass = "alert-rojo";  // Clase genérica para alerta roja
        } else if (ratio <= 15) {
            estado = "En Alerta";
            porcentaje = 40;
            colorClass = "alert-naranja";  // Clase genérica para alerta naranja
        } else if (ratio <= 25) {
            estado = "Moderado";
            porcentaje = 60;
            colorClass = "alert-amarillo";  // Clase genérica para alerta amarilla
        } else if (ratio <= 40) {
            estado = "Medio Sano";
            porcentaje = 80;
            colorClass = "alert-verde";  // Clase genérica para alerta verde
        } else {
            estado = "Sano";
            porcentaje = 100;
            colorClass = "alert-azul";  // Clase genérica para alerta azul
        }


        var progressBar = document.getElementById('progress-bar');
        if (progressBar) {
            progressBar.style.width = porcentaje + '%';
            progressBar.setAttribute('aria-valuenow', porcentaje);
            progressBar.className = 'progress-bar ' + colorClass;
        }

        var estadoLabel = document.getElementById('estado-label');
        if (estadoLabel) {
            estadoLabel.innerText = 'Estado Socioeconómico: ' + estado;
        }
    }

    document.querySelectorAll('div[name="socioeconomico"] input').forEach(input => {
        input.addEventListener('input', calcularEstado);
    });
});

function cargarComisiones() {
    $.ajax({
        type: 'GET',
        url: baseURL + 'comisiones',
        dataType: 'json',
        success: function (response) {
            var select = $('#cantidadCuotas');
            select.empty();


            select.append($('<option>', {
                value: -1,
                text: 'Seleccione...'
            }));


            response.comisiones.forEach(function (comisiones) {
                var option = $('<option></option>')
                    .attr('value', comisiones.valor)
                    .attr('cant_meses', comisiones.cantidad_meses)
                    .text(comisiones.cantidad_meses);
                select.append(option);
            });


            select.trigger('change');
        },
        error: function (xhr, status, error) {
            console.error("Error al cargar las comisiones:", status, error);
        }
    });
}
