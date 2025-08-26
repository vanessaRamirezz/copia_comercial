$(document).ready(function () {
    // Inicializa DataTable
    const table = $('#dataTableSuc').DataTable({
        destroy: true,
        paging: true,
        searching: false,
        info: false,
        ordering: false,
        language: {
            emptyTable: "No hay datos disponibles en la tabla",
            zeroRecords: "No se encontraron registros",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            paginate: {
                first: "Primero",
                last: "Último",
                next: "Siguiente",
                previous: "Anterior"
            }
        }
    });

    Swal.fire({
        title: 'Espere...',
        html: 'Cargando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            cargarSucAll();
        }
    });
    // Evento al abrir el modal
    let modoEdicion = false; // variable global para controlar

    // Al hacer clic en "Agregar"
    $('#nuevaFechaBtn').on('click', function () {
        Swal.fire({
            title: 'Espere...',
            html: 'Cargando modal...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        modoEdicion = false;
        $('#nuevaSucursal').modal('show');
    });

    // Evento al abrir modal
    $('#nuevaSucursal').on('shown.bs.modal', function () {

        if (!modoEdicion) {
            // Solo limpia si es modo agregar
            $('#sucursal').val('');
            $('#idsucursal').val('');
            $('#deptoSuc').val(-1).removeAttr('data-depto-seleccionado');
            $('#muniSuc').empty().append('<option value="-1" selected>Seleccione...</option>');
            $('#distritoSuc').empty().append('<option value="-1" selected>Seleccione...</option>');
            $('#coloniaSuc').empty().append('<option value="-1" selected>Seleccione...</option>');
            cargarDepartamentos();
        }
        Swal.close();
    });

    $('#deptoSuc').on('change', function () {
        var deptoId = $(this).val();
        if (deptoId != -1) {
            cargarMunicipios(deptoId);
        } else {
            $('#muniSuc').empty().append('<option value="-1">Seleccione...</option>');
        }
    });

    $('#muniSuc').on('change', function () {
        var muniId = $(this).val();
        if (muniId != -1) {
            cargarDistrito(muniId);
        } else {
            $('#muniSuc').empty().append('<option value="-1">Seleccione...</option>');
        }
    });

    $('#distritoSuc').on('change', function () {
        var distritoId = $(this).val();
        if (distritoId != -1) {
            cargarColonias(distritoId);
        } else {
            $('#muniSuc').empty().append('<option value="-1">Seleccione...</option>');
        }
    });

    $('#btnGuardarSuc').on('click', function () {
        Swal.fire({
            title: 'Espere...',
            html: 'Guardando Datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Capturamos valores de los campos
        const sucursal = $('#sucursal').val().trim();
        const depto = $('#deptoSuc').val();
        const muni = $('#muniSuc').val();
        const distrito = $('#distritoSuc').val();
        const colonia = $('#coloniaSuc').val();
        const idsucursal = $('#idsucursal').val();

        // Validación de todos los campos
        if (!sucursal || depto === "-1" || muni === "-1" || distrito === "-1" || colonia === "-1") {
            Swal.close();
            toastr.error('Todos los campos son obligatorios', 'Error');
            return;
        }

        // Si todo está bien, se envía la petición
        $.ajax({
            url: baseURL + 'saveSuc',
            method: 'POST',
            data: {
                sucursal: sucursal,
                depto: depto,
                muni: muni,
                distrito: distrito,
                colonia: colonia,
                idsucursal: idsucursal
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    $('#nuevaSucursal').modal('hide');
                    cargarSucAll();
                    Swal.close();
                    toastr.success(response.message, 'Éxito');
                    $('#nuevaSucursal').modal('hide');
                } else {
                    Swal.close();
                    toastr.error(response.message || 'No se pudo guardar la información', 'Error');
                    $('#nuevaSucursal').modal('hide');
                }
            },
            error: function () {
                Swal.close();
                toastr.error('Ocurrió un error al guardar la información', 'Error');
                $('#nuevaSucursal').modal('hide');
            }
        });
    });


    // FUNCIONES

    function cargarSucAll() {
        $.ajax({
            url: baseURL + 'getSucursalesDes',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                table.clear();

                if (Array.isArray(response) && response.length > 0) {
                    response.forEach(function (sucursal) {
                        table.row.add([
                            sucursal.sucursal,
                            sucursal.nombre_distrito,
                            sucursal.nombre_departamento,
                            sucursal.nombre_municipio,
                            sucursal.nombre_colonia,
                            `<button class="btn btn-sm btn-primary btn-editar-suc" 
                            data-idsucursal="${sucursal.id_sucursal}"
                                data-sucursal="${sucursal.sucursal}"
                                data-depto="${sucursal.id_departamento}"
                                data-muni="${sucursal.id_municipio}"
                                data-distrito="${sucursal.id_distrito}"
                                data-colonia="${sucursal.id_colonia}">
                                Editar
                            </button>`
                        ]);
                    });
                    table.draw();
                    Swal.close();
                } else {
                    table.row.add(['-', 'No hay sucursales', '-', '-', '-', '-']).draw();
                    Swal.close();
                    toastr.warning("No hay sucursales", "Atención");
                }
            },
            error: function () {
                table.clear();
                table.row.add(['-', 'Error al cargar sucursales', '-', '-', '-', '-']).draw();
                Swal.close();
                toastr.error("Error al cargar sucursales", "Error");
            }
        });
    }

    function cargarDepartamentos() {
        $.ajax({
            type: 'GET',
            url: baseURL + 'departamentos',
            dataType: 'json',
            success: function (response) {
                var select = $('#deptoSuc');
                select.empty().append('<option value="-1">Seleccione...</option>');

                response.departamentos.forEach(function (depto) {
                    select.append($('<option>', {
                        value: depto.id,
                        text: depto.nombre
                    }));
                });
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar los departamentos:", status, error);
            }
        });
    }

    function cargarMunicipios(deptoId) {
        Swal.fire({
            title: 'Espere...',
            html: 'Cargando municipios...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: 'POST',
            url: baseURL + 'municipios',
            data: { departamento_id: deptoId },
            dataType: 'json',
            success: function (data) {
                var select = $('#muniSuc');
                select.empty().append('<option value="-1">Seleccione...</option>');

                data.forEach(function (municipio) {
                    select.append($('<option>', {
                        value: municipio.id,
                        text: municipio.nombre
                    }));
                });
                Swal.close();
            },
            error: function (xhr, status, error) {
                Swal.close();
                console.error("Error al cargar los municipios:", status, error);
            }
        });
    }

    function cargarDistrito(municipio_id) {
        Swal.fire({
            title: 'Espere...',
            html: 'Cargando distritos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: 'POST',
            url: baseURL + 'distritos',
            data: { municipio_id: municipio_id },
            dataType: 'json',
            success: function (data) {
                var select = $('#distritoSuc');
                select.empty().append('<option value="-1">Seleccione...</option>');

                data.forEach(function (distrito) {
                    select.append($('<option>', {
                        value: distrito.id_distrito,
                        text: distrito.nombre
                    }));
                });
                Swal.close();
            },
            error: function (xhr, status, error) {
                Swal.close();
                console.error("Error al cargar los distritos:", status, error);
            }
        });
    }

    function cargarColonias(id_distrito) {
        Swal.fire({
            title: 'Espere...',
            html: 'Cargando colonias...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            type: 'POST',
            url: baseURL + 'getColonias',
            data: { id_distrito: id_distrito },
            dataType: 'json',
            success: function (data) {
                var select = $('#coloniaSuc');
                select.empty().append('<option value="-1">Seleccione...</option>');

                data.forEach(function (colonia) {
                    select.append($('<option>', {
                        value: colonia.id,
                        text: colonia.nombre
                    }));
                });
                Swal.close();
            },
            error: function (xhr, status, error) {
                Swal.close();
                console.error("Error al cargar las colonias:", status, error);
            }
        });
    }

    $('#dataTableSuc').on('click', '.btn-editar-suc', async function () {
        modoEdicion = true;

        const idsucursal = $(this).data('idsucursal');
        const sucursal = $(this).data('sucursal');
        const depto = $(this).data('depto');
        const muni = $(this).data('muni');
        const distrito = $(this).data('distrito');
        const colonia = $(this).data('colonia');

        try {
            $('#idsucursal').val(idsucursal);
            $('#sucursal').val(sucursal);
            await cargarDepartamentos();
            let existe = await esperarOpcion('#deptoSuc', depto);
            $('#deptoSuc').val(existe ? depto : '-1');

            await cargarMunicipios(depto);
            existe = await esperarOpcion('#muniSuc', muni);
            $('#muniSuc').val(existe ? muni : '-1');

            await cargarDistrito(muni);
            existe = await esperarOpcion('#distritoSuc', distrito);
            $('#distritoSuc').val(existe ? distrito : '-1');

            await cargarColonias(distrito);
            existe = await esperarOpcion('#coloniaSuc', colonia);
            $('#coloniaSuc').val(existe ? colonia : '-1');

            $('#nuevaSucursal').modal('show');
        } catch (error) {
            console.error("Error en la carga en cascada para edición:", error);
        }
    });


    // Función que espera hasta que una opción con valor 'val' exista en el select dado
    function esperarOpcion(selector, val, timeout = 5000) {
        return new Promise((resolve, reject) => {
            const start = Date.now();

            (function buscar() {
                if ($(selector + ' option[value="' + val + '"]').length > 0) {
                    resolve(true);
                } else if (Date.now() - start > timeout) {
                    // Timeout, no existe la opción
                    resolve(false);
                } else {
                    setTimeout(buscar, 50);
                }
            })();
        });
    }


});