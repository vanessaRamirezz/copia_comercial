document.addEventListener("DOMContentLoaded", function () {
    var actualizarInfoPerfil = document.getElementById("updateUsuario");

    var nombresUsuario = document.getElementById("nombresUsuario");
    var apellidosUsuario = document.getElementById("apellidosUsuario");
    var emailUsuario = document.getElementById("emailUsuario");
    var numTelefono = document.getElementById("numTelefono");

    const nameRegex = /^[a-zA-Z\s]+$/;
    const emailRegex = /^\S+@\S+\.\S+$/;
    const numberRegex = /^\d+$/;

    if (actualizarInfoPerfil) {
        
        actualizarInfoPerfil.addEventListener("click", function () {
            var flagValidate = false;

            if (nombresUsuario.value.trim() == "") {
                toastr.info("El nombre es requerido", "Campo incompleto");
                flagValidate = true;
                return;
            } else if (!nameRegex.test(nombresUsuario.value.trim())) {
                toastr.info("El nombre solo debe contener letras", "Entrada inválida");
                flagValidate = true;
                return;
            } else if (apellidosUsuario.value.trim() == "") {
                toastr.info("El apellido es requerido", "Campo incompleto");
                flagValidate = true;
                return;
            } else if (!nameRegex.test(apellidosUsuario.value.trim())) {
                toastr.info("El apellido solo debe contener letras", "Entrada inválida");
                flagValidate = true;
                return;
            } else if (emailUsuario.value.trim() == "") {
                toastr.info("El correo es requerido", "Campo incompleto");
                flagValidate = true;
                return;
            } else if (!emailRegex.test(emailUsuario.value.trim())) {
                toastr.info("Por favor ingrese un correo válido", "Entrada inválida");
                flagValidate = true;
                return;
            } else if (numTelefono.value.trim() == "") {
                toastr.info("El numero de telefono es requerido", "Campo incompleto");
                flagValidate = true;
                return;
            } else if (!numberRegex.test(numTelefono.value.trim())) {
                toastr.info("El número de teléfono solo debe contener dígitos", "Entrada inválida");
                flagValidate = true;
                return;
            }

            Swal.fire({
                title: 'Espere...',
                html: 'Verificando credenciales...',
                allowEscapeKey: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            if (!flagValidate) {
                $.ajax({
                    type: 'POST',
                    url: baseURL + 'actualizar_informacion',
                    data: {
                        "nombresUsuario": nombresUsuario.value,
                        "apellidosUsuario": apellidosUsuario.value,
                        "emailUsuario": emailUsuario.value,
                        "numTelefono": numTelefono.value
                    },
                    dataType: 'json',
                    success: function (rsp) {
                        Swal.close();
                        if (rsp.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Los datos se actualizaron exitosamente",
                                showDenyButton: false,
                                showCancelButton: false,
                                confirmButtonText: "Actualizar pagina",
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else if (rsp.error) {
                            toastr.error(rsp.error, "ERROR al actualizar los datos");
                        }
                    }
                });
            } else {
                Swal.close();
                toastr.info("Verifique los campos requeridos", "Campos vacios");
            }
        });
    }
});