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

        const resumen = `
            <strong>Departamento:</strong> ${obtenerTextoPorId("departamento")} / 
            <strong>Municipio:</strong> ${obtenerTextoPorId("municipio")} / 
            <strong>Distrito:</strong> ${obtenerTextoPorId("distrito")} / 
            <strong>Colonia:</strong> ${obtenerTextoPorId("colonia")} / 
            <strong>Sucursal:</strong> ${obtenerTextoPorId("sucursal")}
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
            sucursal
        };

        $.ajax({
            type: 'POST',
            url: baseURL + 'dataMoraCliente',
            data: filtros,
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: 'Buscando...',
                    html: 'Por favor espera...',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading(),
                    showConfirmButton: false
                });

                setTimeout(function () {
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
                const cuerpo = document.getElementById('cuerpo-tabla');
                cuerpo.innerHTML = '';

                if (Array.isArray(response) && response.length > 0) {
                    response.forEach((item, index) => {
                        // Detectar si es la fila de totales
                        if (item.total === 'Totales') {
                            const filaTotales = document.createElement('tr');
                            filaTotales.style.fontWeight = 'bold';
                            filaTotales.style.backgroundColor = '#f0f0f0';
                            filaTotales.innerHTML = `
                    <td colspan="2">Totales</td>
                    <td>${parseFloat(item.sin_vencer).toFixed(2)}</td>
                    <td>${parseFloat(item.d1_30).toFixed(2)}</td>
                    <td>${parseFloat(item.d31_60).toFixed(2)}</td>
                    <td>${parseFloat(item.d61_90).toFixed(2)}</td>
                    <td>${parseFloat(item.d91_120).toFixed(2)}</td>
                    <td>${parseFloat(item.d121_150).toFixed(2)}</td>
                    <td>${parseFloat(item.d_mas_150).toFixed(2)}</td>
                    <td>${parseFloat(item.total_general).toFixed(2)}</td>
                `;
                            cuerpo.appendChild(filaTotales);
                        } else {
                            // Fila principal
                            const filaPrincipal = document.createElement('tr');
                            filaPrincipal.innerHTML = `
                    <td>${item.codigo}</td>
                    <td>${item.nombre}</td>
                    <td>${parseFloat(item.sin_vencer).toFixed(2)}</td>
                    <td>${parseFloat(item.d1_30).toFixed(2)}</td>
                    <td>${parseFloat(item.d31_60).toFixed(2)}</td>
                    <td>${parseFloat(item.d61_90).toFixed(2)}</td>
                    <td>${parseFloat(item.d91_120).toFixed(2)}</td>
                    <td>${parseFloat(item.d121_150).toFixed(2)}</td>
                    <td>${parseFloat(item.d_mas_150).toFixed(2)}</td>
                    <td>${parseFloat(item.total).toFixed(2)}</td>
                `;

                            const filaDetalle = document.createElement('tr');
                            filaDetalle.innerHTML = `
                    <td colspan="10" style="font-style: italic; color: #555;">
                        Contrato: ${item.contrato} 
                        Fecha Compra: ${item.fecha_compra} 
                        Fecha Últ. P.: ${item.fecha_ultimo_pago || 'N/A'} 
                        Por: ${item.por || 'N/A'} 
                        Teléf: ${item.telefono}
                    </td>
                `;

                            cuerpo.appendChild(filaPrincipal);
                            cuerpo.appendChild(filaDetalle);
                        }
                    });
                } else {
                    const filaVacía = document.createElement('tr');
                    filaVacía.innerHTML = `<td colspan="10" style="text-align:center;">No hay datos disponibles.</td>`;
                    cuerpo.appendChild(filaVacía);
                }

                Swal.close();
            }
            ,
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
async function imprimirMoraCliente() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const elemento = document.querySelector('.contenido-reporte-mora-clientes');
    const opciones = {
        margin: 0.5,
        filename: 'rpMoraClientes.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    await html2pdf().set(opciones).from(elemento).save();

    Swal.close();
}
