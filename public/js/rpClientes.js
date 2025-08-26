document.addEventListener("DOMContentLoaded", function () {
    console.log("Iniciando carga de datos...");
    cargarDepartamentos();

    // Al cambiar DEPARTAMENTO → cargar municipios
    $('#departamento').on('change', function () {
        const deptoId = $(this).val();
        if (deptoId !== '-1') {
            cargarMunicipios(deptoId);
        } else {
            limpiarSelect($('#municipio'));
            limpiarSelect($('#distrito'));
            limpiarSelect($('#colonia'));
        }
    });

    // Al cambiar MUNICIPIO → cargar distritos
    $('#municipio').on('change', function () {
        const municipioId = $(this).val();
        if (municipioId !== '-1') {
            cargarDistrito(municipioId);
        } else {
            limpiarSelect($('#distrito'));
            limpiarSelect($('#colonia'));
        }
    });

    // Al cambiar DISTRITO → cargar colonias
    $('#distrito').on('change', function () {
        const distritoId = $(this).val();
        if (distritoId !== '-1') {
            cargarColonias(distritoId);
        } else {
            limpiarSelect($('#colonia'));
        }
    });

    function limpiarSelect(select) {
        select.empty().append($('<option>', {
            value: -1,
            text: 'Seleccione...'
        }));
    }

    function cargarDepartamentos() {
        $.ajax({
            type: 'GET',
            url: baseURL + 'departamentos',
            dataType: 'json',
            success: function (response) {
                const select = $('#departamento');
                limpiarSelect(select);

                response.departamentos.forEach(function (depto) {
                    select.append($('<option>', {
                        value: depto.id,
                        text: depto.nombre
                    }));
                });

                const deptoSeleccionado = select.data('depto-seleccionado');
                if (deptoSeleccionado && deptoSeleccionado !== -1) {
                    select.val(deptoSeleccionado);
                    cargarMunicipios(deptoSeleccionado);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar los departamentos:", status, error);
            }
        });
    }

    function cargarMunicipios(deptoId) {
        Swal.fire({ title: 'Espere...', html: 'Cargando municipios...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            type: 'POST',
            url: baseURL + 'municipios',
            data: { departamento_id: deptoId },
            dataType: 'json',
            success: function (data) {
                const select = $('#municipio');
                limpiarSelect(select);

                data.forEach(function (municipio) {
                    select.append($('<option>', {
                        value: municipio.id,
                        text: municipio.nombre
                    }));
                });

                Swal.close();

                const municipioSeleccionado = select.data('municipio-seleccionado');
                if (municipioSeleccionado && municipioSeleccionado !== -1) {
                    select.val(municipioSeleccionado);
                    cargarDistrito(municipioSeleccionado);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar los municipios:", status, error);
                Swal.close();
            }
        });
    }

    function cargarDistrito(municipioId) {
        Swal.fire({ title: 'Espere...', html: 'Cargando distritos...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            type: 'POST',
            url: baseURL + 'distritos',
            data: { municipio_id: municipioId },
            dataType: 'json',
            success: function (data) {
                const select = $('#distrito');
                limpiarSelect(select);

                data.forEach(function (distrito) {
                    select.append($('<option>', {
                        value: distrito.id_distrito,
                        text: distrito.nombre
                    }));
                });

                Swal.close();

                const distritoSeleccionado = select.data('distrito-seleccionado');
                if (distritoSeleccionado && distritoSeleccionado !== -1) {
                    select.val(distritoSeleccionado);
                    cargarColonias(distritoSeleccionado);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar los distritos:", status, error);
                Swal.close();
            }
        });
    }

    function cargarColonias(idDistrito) {
        Swal.fire({ title: 'Espere...', html: 'Cargando colonias...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            type: 'POST',
            url: baseURL + 'getColonias',
            data: { id_distrito: idDistrito },
            dataType: 'json',
            success: function (data) {
                const select = $('#colonia');
                limpiarSelect(select);

                data.forEach(function (colonia) {
                    select.append($('<option>', {
                        value: colonia.id,
                        text: colonia.nombre
                    }));
                });

                Swal.close();

                const coloniaSeleccionada = select.data('colonia-seleccionada');
                if (coloniaSeleccionada && coloniaSeleccionada !== -1) {
                    select.val(coloniaSeleccionada);
                }
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar las colonias:", status, error);
                Swal.close();
            }
        });
    }

    function obtenerTextoPorId(id) {
        const texto = $(`#${id} option:selected`).text();
        // Compara correctamente el texto con las opciones que deseas
        return (texto === "Seleccione..." || texto === "Seleccione una sucursal" || !texto) ? "N/A" : texto;
    }
    
    
    document.getElementById('btnBuscar').addEventListener('click', function () {
        const departamento = $('#departamento').val();
        const municipio = $('#municipio').val();
        const distrito = $('#distrito').val();
        const colonia = $('#colonia').val();
        const sucursal = $('select[name="sucursal"]').val();
        const estado = $('select[name="estado"]').val(); 

        const resumen = `
            <strong>Departamento:</strong> ${obtenerTextoPorId("departamento")} / 
            <strong>Municipio:</strong> ${obtenerTextoPorId("municipio")} / 
            <strong>Distrito:</strong> ${obtenerTextoPorId("distrito")} / 
            <strong>Colonia:</strong> ${obtenerTextoPorId("colonia")} / 
            <strong>Sucursal:</strong> ${obtenerTextoPorId("sucursal")} / 
            <strong>Estado:</strong> ${obtenerTextoPorId("estado")}
        `;

        const contenedorResumen = document.getElementById('texto-resumen');
    if (contenedorResumen) {
        contenedorResumen.innerHTML = resumen;
    }


    
        const filtros = {
            departamento,
            municipio,
            distrito,
            colonia,
            sucursal,
            estado
        };
    
        $.ajax({
            type: 'POST',
            url: baseURL + 'getDataRpClientes', // Cambia a la ruta real en tu backend
            data: filtros,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Buscando...',
                    html: 'Por favor espera...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    showConfirmButton: false // 🔴 No muestra el botón de confirmación
                });
                
                // Cambiar el mensaje si la petición tarda más de 10 segundos
                setTimeout(function () {
                    // Cambiar el contenido de la ventana
                    Swal.update({
                        html: 'La carga es lenta debido a la cantidad de datos. Por favor, espera...',
                        showConfirmButton: false,
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading(),
                    });
                }, 20000); // 20000ms = 20 segundos
            },
            success: function (response) {
                console.log(response);
                const tbody = $('table tbody');
                tbody.empty();
                
                if (response.status === 'ok' && response.data.length > 0) {
                    let totalGlobal = 0;
        
                    response.data.forEach(cliente => {
                        let fila = `
                            <tr>
                                <td>${cliente.dui}</td>
                                <td>
                                    ${cliente.nombre_cliente}
                                    ${cliente.solicitudes.map(s => `
                                        <div class="sub-row">Solicitud No. ${s.numero_solicitud} - Fecha: ${s.fecha_creacion}</div>
                                    `).join('')}
                                    <div class="sub-row font-weight-bold">Total cliente: $${parseFloat(cliente.total_monto).toFixed(2)}</div>
                                </td>
                                <td>
                                    ${cliente.solicitudes.map(s => `
                                        <div class="sub-row">$${parseFloat(s.montoApagar).toFixed(2)}</div>
                                    `).join('')}
                                    <div class="sub-row font-weight-bold">$${parseFloat(cliente.total_monto).toFixed(2)}</div>
                                </td>
                            </tr>
                        `;
                        tbody.append(fila);
                        totalGlobal += parseFloat(cliente.total_monto);
                    });
        
                    // Agregar fila total global
                    tbody.append(`
                        <tr class="total-row font-weight-bold">
                            <td colspan="2">TOTAL GENERAL</td>
                            <td>$${totalGlobal.toFixed(2)}</td>
                        </tr>
                    `);
                } else {
                    tbody.append('<tr><td colspan="3" class="text-center">No se encontraron resultados.</td></tr>');
                }
        
                Swal.close();
            },
            error: function (xhr, status, error) {
                console.log(status, error);
                Swal.close();
                Swal.fire({
                    title: 'Error',
                    text: 'Hubo un problema al cargar los datos. Intenta nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });        
    });
    
});

async function imprimirReporteCliente() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const elemento = document.querySelector('.contenido-reporte-cliente');
    const opciones = {
        margin: 0.5,
        filename: 'reporte_clientes.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    await html2pdf().set(opciones).from(elemento).save();

    Swal.close();
}
