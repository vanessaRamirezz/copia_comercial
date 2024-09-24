document.addEventListener("DOMContentLoaded", function () {
    var actualizarPwd = document.getElementById("updateContra");

    var pwdActualId = document.getElementById("pwdActual");
    var pwdNuevaId = document.getElementById("pwdNueva");
    var pwdNuevaConfirmaId = document.getElementById("pwdNuevaConfirma");

    // Configurar las opciones de Toastr

    actualizarPwd.addEventListener("click", function () {
        let pwdActual = pwdActualId.value.trim();
        let pwdNueva = pwdNuevaId.value.trim();
        let pwdNuevaConfirma = pwdNuevaConfirmaId.value.trim();

        Swal.fire({
            title: 'Espere...',
            html: 'Verificando credenciales...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        if (pwdNueva === pwdNuevaConfirma) {
            $.ajax({
                type: 'POST',
                url: baseURL + 'cambio_password',
                data: { 
                    pwdNueva, 
                    pwdNuevaConfirma,
                    pwdActual
                },
                dataType: 'json',
                success: function (response) {
                    $('#updatePwd').modal('hide');
                    Swal.close();
                    if (response.success) {
                        toastr.success("Success", response.success);
                    }else if (response.error) {
                        toastr.error("Error", response.error);
                    }else if (response.info) {
                        toastr.info("Info", response.info);
                    }
                }
            });
        } else {
            Swal.close();
            toastr.error("ERROR", "Las contraseñas no coinciden");
        }
    });
});
