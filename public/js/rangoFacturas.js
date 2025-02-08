document.addEventListener("DOMContentLoaded", function () {
    let table = $('#tblRangoFacturas').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "stateSave": false, // Asegura que no guarda el estado previo
        "initComplete": function () {
            let searchInput = $('.dataTables_filter input');
            searchInput.val('').trigger('input'); // Limpia el buscador y actualiza la tabla
        }
    });

    // Forzar que el input de búsqueda se limpie cada vez que se recarga la tabla
    setTimeout(function () {
        let searchInput = $('.dataTables_filter input');
        searchInput.val('').trigger('input');
    }, 500);

    $.ajax({
        url: baseURL + 'getRangoFacturas',  // URL de la función backend
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Limpiar la tabla antes de agregar nuevas filas
            table.clear();

            // Recorrer los datos obtenidos y agregarlos a la tabla
            $.each(response, function(index, rango) {
                table.row.add([
                    rango.numero_inicio,              // Número de inicio
                    rango.numero_fin,                 // Número final
                    rango.nombre_sucursal,            // Nombre de la sucursal
                    rango.nombre_usuario,             // Nombre del usuario creador
                    formatearFecha(rango.fecha_creacion),// Fecha de creación
                    rango.estado
                    // Aquí podrías agregar más operaciones como editar/eliminar si es necesario
                ]).draw(false);  // Añadir fila sin redibujar toda la tabla
            });
        },
        error: function() {
            Swal.fire('Error', 'No se pudieron cargar los datos', 'error');
        }
    });
});

function agregarRango() {
    showLoading('Cargando formularios...');

    // Cargar las sucursales y mostrar el modal
    cargarSucursales()
        .then(sucursales => {
            if (sucursales.length > 0) {
                mostrarFormulario(sucursales);
            } else {
                Swal.fire('Error', 'No hay sucursales disponibles', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudieron cargar las sucursales', 'error');
        });
}

// Mostrar el loading de SweetAlert
function showLoading(message) {
    Swal.fire({
        title: 'Espere...',
        html: message,
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// Cargar las sucursales mediante AJAX
function cargarSucursales() {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: baseURL + "getSucursales",
            type: "GET",
            dataType: "json",
            success: function (response) {
                resolve(response);
            },
            error: function () {
                reject();
            }
        });
    });
}

// Mostrar el formulario para agregar un rango
function mostrarFormulario(sucursales) {
    const opciones = generarOpcionesSucursales(sucursales);

    Swal.fire({
        title: 'Agregar Rango',
        html: generarFormulario(opciones),
        showCancelButton: true,
        confirmButtonText: 'Guardar',
        preConfirm: () => {
            return guardarRango();
        }
    });
}

// Generar las opciones para el selector de sucursales
function generarOpcionesSucursales(sucursales) {
    return sucursales.map(sucursal => 
        `<option value="${sucursal.id_sucursal}">${sucursal.sucursal}</option>`
    ).join('');
}

// Generar el formulario HTML
function generarFormulario(opciones) {
    return `
        <form id="formRango">
            <div class="form-group">
                <label>Número de inicio:</label>
                <input type="number" class="form-control" id="numeroInicio" required>
            </div>
            <div class="form-group">
                <label>Número final:</label>
                <input type="number" class="form-control" id="numeroFinal" required>
            </div>
            <div class="form-group">
                <label>Sucursal:</label>
                <select class="form-control" id="sucursal" required>
                    <option value="">Seleccione una sucursal</option>
                    ${opciones}
                </select>
            </div>
            <div class="form-group">
                <label>Usuario Creador:</label>
                <input type="text" class="form-control" id="usuarioCreador" value="${usuarioNombre}" readonly>
            </div>
            <input type="hidden" id="usuarioID" value="${usuarioID}">
        </form>
    `;
}

// Guardar el rango de facturas
function guardarRango() {
    const numeroInicio = $('#numeroInicio').val();
    const numeroFinal = $('#numeroFinal').val();
    const sucursal = $('#sucursal').val();
    const usuarioID = $('#usuarioID').val();

    if (!numeroInicio || !numeroFinal || !sucursal) {
        Swal.showValidationMessage('Todos los campos son obligatorios');
        return false;
    }

    // Mostrar loading de guardado
    showLoading('Guardando...');

    return new Promise((resolve, reject) => {
        $.ajax({
            url: baseURL + "rango_facturas",
            type: "POST",
            data: {
                numeroInicio: numeroInicio,
                numeroFinal: numeroFinal,
                sucursal: sucursal,
                usuarioID: usuarioID
            },
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Guardado exitosamente',
                        text: response.message
                    }).then(() => {
                        $('#tblRangoFacturas').DataTable().ajax.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
                resolve();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo guardar la información'
                });
                reject();
            }
        });
    });
}