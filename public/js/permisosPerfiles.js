document.addEventListener("DOMContentLoaded", function () {
    $('#PerfilesPermisos').DataTable({
        "language": {
            "url": baseURL + "public/js/es-ES.json"
        },
        "searching": false // Desactiva el buscador
    });

    datosRefresh();
});

function datosRefresh() {
    Swal.fire({
        title: 'Espere...',
        html: 'Cargando datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        type: "get",
        url: baseURL + "getPerfiles",
        dataType: "json",
        success: function (rsp) {
            if (rsp.success) {
                var perfiles = rsp.data;
                $("#PerfilesPermisos tbody").empty();

                perfiles.forEach(function (perfil) {
                    let permisos = '';

                    if (perfil.accesos && perfil.accesos.length > 0) {
                        perfil.accesos.forEach(function (acceso) {
                            permisos += acceso.acceso + '<br>';
                        });
                    } else {
                        permisos = 'No tiene permisos';
                    }

                    let fila = `<tr>
                        <td>${perfil.tipo_perfil}</td>
                        <td>${permisos}</td>
                        <td>
                            <button type="button" class="btn btn-primary" onclick="abrirModalPerfil('${encodeURIComponent(JSON.stringify(perfil))}')">
                                Editar
                            </button>
                        </td>
                    </tr>`;

                    $("#PerfilesPermisos tbody").append(fila);
                });
            } else {
                toastr.error("No se encontraron perfiles", "Error en carga de datos");
            }
        },
        error: function () {
            Swal.close();
            toastr.error("Ocurrió un error al cargar los datos", "Error en carga de datos");
        },
        complete: function () {
            setTimeout(function () {
                Swal.close();
            }, 2000);
        }
    });
}

function abrirModalPerfil(perfilJson) {
    try {
        Swal.fire({
            title: 'Espere...',
            html: 'Cargando datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        const perfil = JSON.parse(decodeURIComponent(perfilJson));
        document.getElementById('modalPerfilNombre').textContent = 'Perfil seleccionado: ' + perfil.tipo_perfil;
        $('#id_perfil').val(perfil.id_perfil);
        
        console.log(perfil);

        getDatAccesos(function (accesos) {
            $('#permisosContainer').empty();
            const perfilAccesos = Array.isArray(perfil.accesos) ? perfil.accesos : [];
            accesos.forEach(function (acceso) {
                let isChecked = perfilAccesos.some(pAcceso => pAcceso.id_acceso === acceso.id_acceso);

                let checkbox = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="${acceso.id_acceso}" id="permiso_${acceso.id_acceso}" ${isChecked ? 'checked' : ''}>
                        <label class="form-check-label" for="permiso_${acceso.id_acceso}">
                            ${acceso.acceso}
                        </label>
                    </div>
                `;

                $('#permisosContainer').append(checkbox);
            });

            $('#editarAccesosPerfil').modal('show');
            Swal.close();
        });
    } catch (error) {
        console.error("Error al abrir el modal: ", error);
        toastr.error("No se pudo abrir el modal", "Error");
        Swal.close();
    }
}

function getDatAccesos(callback) {
    $.ajax({
        type: "get",
        url: baseURL + "getAccesos",
        dataType: "json",
        success: function (rsp) {
            if (rsp.success) {
                callback(rsp.data);
            } else {
                toastr.error("No se encontraron accesos", "Error en carga de datos");
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

function editPerfilAcceso() {
    Swal.fire({
        title: 'Espere...',
        html: 'Actualizando datos...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    const idPerfil = $('#id_perfil').val();      
    let accesosSeleccionados = [];
    
    $('#permisosContainer input[type="checkbox"]').each(function() {
        if ($(this).is(':checked')) {
            
            accesosSeleccionados.push($(this).val());
        }
    });
    
    console.log('idPerfil:', idPerfil);
    console.log('Accesos seleccionados:', accesosSeleccionados);

    $.ajax({
        type: "POST",
        url: baseURL + "editPermisos", 
        data: {
            idPerfil: idPerfil,
            accesos: accesosSeleccionados
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                toastr.success("Los permisos del perfil se actualizaron correctamente.");
                $('#editarAccesosPerfil').modal('hide');
                datosRefresh();
            } else {
                toastr.error("Hubo un error al actualizar los permisos del perfil.");
            }
            Swal.close();
        },
        error: function() {

            toastr.error("Ocurrió un error en la solicitud. Por favor, inténtalo de nuevo.");
            Swal.close();
        }
    });
}
