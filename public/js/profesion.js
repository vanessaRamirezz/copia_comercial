document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    cargarDatosTbl();
});

function guardarProfesion() {
    Swal.fire({
        title: 'Espere...',
        html: 'Guardando Datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    const profesionOficio = document.getElementById("txtProfesionOficio").value.trim();

    if (profesionOficio === "") {
        toastr.error("El campo 'Profesión u oficio' no puede estar vacío o contener solo espacios.", "ERROR");
        return;
    }

    $.ajax({
        type: "POST",
        url: baseURL +"guardarProfesion",
        data: {
            "profesionOficio": profesionOficio
        },
        dataType: "json",
        success: function (rsp) {
            if (rsp.success) {
                toastr.success(rsp.msg, "Success");
                cargarDatosTbl();

                document.getElementById("txtProfesionOficio").value = "";
            } else {
                Swal.close();
                toastr.error(rsp.msg, "ERROR");
            }
        },
        error: function () {
            Swal.close();
            toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
        }, complete: function () {
            $('#nuevaProfesion').modal('hide');
            setTimeout(function () {
                Swal.close();
            }, 2000);
        }
    });
}

function cargarDatosTbl() {
    var dataTableProfesiones = $('#dataTableProfesiones'); // Referencia a la tabla

    // Verificar si DataTable ya está inicializado para solo recargar si es necesario
    if ($.fn.DataTable.isDataTable(dataTableProfesiones)) {
        dataTableProfesiones.DataTable().ajax.reload(null, false); // Recargar sin reiniciar el estado de la tabla
        Swal.close(); // Cerrar Swal después de recargar
    } else {
        // Inicializar DataTable con configuración AJAX
        dataTableProfesiones.DataTable({
            ajax: {
                url: baseURL + 'obtenerProfesiones', // Endpoint del controlador
                dataType: 'JSON',
                dataSrc: function (data) {
                    console.log(data); // Verificar datos en consola
                    if (data.error) {
                        toastr.error(data.error, "Error al cargar datos");
                        return [];
                    }
                    return data; // Cargar data en caso de éxito
                },
                error: function (xhr, error, thrown) {
                    toastr.error("Error en la carga de datos: " + thrown, "Error");
                },
                complete: function () {
                    Swal.close(); // Cerrar Swal cuando los datos se hayan cargado
                }
            },
            columns: [
                { data: 'descripcion' }, // Columna de profesión u oficio
                {
                    data: null,
                    className: 'dt-center',
                    defaultContent: `
                        <button class="btn btn-info btn-sm" onclick="editarProfesion()">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="eliminarProfesion()">Eliminar</button>`,
                    orderable: false
                }
            ]
        });
    }
}
