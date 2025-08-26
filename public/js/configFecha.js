$(document).ready(function () {
    // Inicializa DataTable
    const table = $('#dataTableFechas').DataTable({
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
            cargarFechaConfig();
        }
    });

    // Cargar tabla con la fecha actual del sistema
    function cargarFechaConfig() {
        $.ajax({
            url: baseURL + 'getFechaConfig',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                table.clear();

                if (response.status === 'ok') {
                    const fecha = response.data;
                    table.row.add([
                        fecha.id,
                        fecha.fecha_virtual,
                        fecha.estado
                    ]).draw();
                    Swal.close();
                } else {
                    table.row.add([
                        '-', 'No hay fecha activa', '-'
                    ]).draw();
                    Swal.close();
                    toastr.warning("No hay fecha activa", "Atención");
                }

                Swal.close();
            },
            error: function () {
                table.clear();
                table.row.add([
                    '-', 'Error al cargar la fecha', '-'
                ]).draw();
                Swal.close();
                toastr.error("Error al cargar la fecha", "Error");
            }
        });
    }

    // Limita el input date para hoy y mañana
    const inputFecha = document.getElementById('fecha_virtual');
    const partes = fechaVirtual.split('-'); // ['2025', '06', '15']
    const hoy = new Date(partes[0], partes[1] - 1, partes[2]); // año, mes (0-based), día
    //const hoy = new Date(fechaVirtual);
    console.log("valor de hoy::: ",hoy);
    const manana = new Date();
    manana.setDate(hoy.getDate() + 1);

    const formatoFecha = (fecha) => fecha.toISOString().split('T')[0];

    inputFecha.min = formatoFecha(hoy);
    inputFecha.max = formatoFecha(manana);

    $('#btnGuardarFecha').on('click', function () {
        Swal.fire({
            title: 'Espere...',
            html: 'Guardando Datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        const fecha = $('#fecha_virtual').val();
    
        if (!fecha) {
            Swal.close();
            toastr.error('La fecha es requerida', 'Error');
            return;
        }

        // Convertimos la fecha ingresada a Date
        const partesInput = fecha.split('-');
        const fechaIngresada = new Date(partesInput[0], partesInput[1] - 1, partesInput[2]);

        // Validar que la nueva fecha sea mayor a la fecha actual (fechaVirtual)
        if (fechaIngresada <= hoy) {
            Swal.close();
            /* toastr.error('La nueva fecha debe ser mayor que la fecha actual', 'Error'); */
            toastr.error(`La nueva fecha debe ser mayor que la fecha actual (${formatoFecha(hoy)})`, 'Error');
            return;
        }

    
        $.ajax({
            url: baseURL + 'postFechaConfig',
            method: 'POST',
            data: { fecha_virtual: fecha },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'ok') {
                    $('#nuevaFechaModal').modal('hide');
                    cargarFechaConfig();
                    Swal.close();
                    toastr.success('Fecha guardada correctamente', 'Éxito');
                } else {
                    Swal.close();
                    toastr.error(response.message || 'No se pudo guardar la fecha', 'Error');
                }
            },
            error: function () {
                Swal.close();
                toastr.error('Ocurrió un error al guardar la fecha', 'Error');
            }
        });
    });
    
});
