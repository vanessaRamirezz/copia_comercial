
document.addEventListener("DOMContentLoaded", function () {
    // Inicialización de elementos del DOM
    const usuarioInput = document.getElementById("usuarioLg");
    const pwdInput = document.getElementById("pwdLg");
    const btnLogin = document.getElementById("btnLogin");
    const btnLoginRecuperarPass = document.getElementById('btnLoginRecuperarPass');
    const recuperarPassBtn = document.getElementById("recuperarPass");
    const updatePassBtn = document.getElementById("updatePass");
    const showPassword = document.querySelector('#showPassword');
    const passwordInput = document.querySelector('#nuevaContraseña');
    const duiRecuperarPass = document.getElementById('duiRecuperarPass');
    const esconderInputs = document.querySelectorAll('.esconderInput');

    document.getElementById('updatePass').addEventListener('click', updatePass);

    // Inicializa el campo DUI con formato
    $('#usuarioLg').mask('00000000-0');

    // Manejadores de eventos
    usuarioInput.addEventListener("input", verificarCamposLlenos);
    pwdInput.addEventListener("input", verificarCamposLlenos);
    showPassword.addEventListener('change', togglePasswordVisibility);
    btnLogin.addEventListener("click", handleLogin);
    recuperarPassBtn.addEventListener("click", handleRecuperarPass);
    btnLoginRecuperarPass.addEventListener('click', resetForm);

    // Verifica si los campos están llenos para habilitar el botón de login
    function verificarCamposLlenos() {
        btnLogin.disabled = !(usuarioInput.value.trim() && pwdInput.value.trim());
    }

    // Alterna la visibilidad de la contraseña
    function togglePasswordVisibility() {
        passwordInput.setAttribute('type', showPassword.checked ? 'text' : 'password');
    }

    // Muestra un mensaje de error usando SweetAlert
    function alertError(text) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: text
        });
    }

    // Maneja el clic en el botón de login
    function handleLogin() {
        const user = usuarioInput.value.trim();
        const pwd = pwdInput.value.trim();

        if (!user) {
            showFieldError(usuarioInput, 'Campos vacíos');
            return;
        }

        if (!pwd) {
            showFieldError(pwdInput, 'Campos vacíos');
            return;
        }

        Swal.fire({
            title: 'Espere...',
            text: 'Verificando credenciales...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        

        $.ajax({
            type: 'POST',
            url: baseURL + 'validateUser',
            data: { user, pwd },
            dataType: 'json',
            success: handleLoginResponse
        });
    }

    // Maneja la respuesta del login
    function handleLoginResponse(response) {
        Swal.close();
        if (response.error) {
            alertError(response.error);
        } else if (response.redirect) {
            window.location.href = response.redirect;
        }
    }

    // Maneja el clic en el botón de recuperación de contraseña
    function handleRecuperarPass() {
        const duiRecuperar = duiRecuperarPass.value.trim();

        if (!duiRecuperar) {
            showFieldError(duiRecuperarPass, 'Campos vacíos');
            return;
        }

        Swal.fire({
            title: 'Espere...',
            text: 'Verificando datos para recuperar contraseña...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        

        $.ajax({
            type: 'POST',
            url: baseURL + 'recuperarPassword',
            data: { duiRecuperar },
            dataType: 'json',
            success: handleRecuperarPassResponse
        });
    }

    // Maneja la respuesta de la recuperación de contraseña
    function handleRecuperarPassResponse(response) {
        Swal.close();
        if (response.error) {
            alertError(response.error);
        } else if (response.success) {
            duiRecuperarPass.disabled = true;
            toggleEsconderInputs('');
            recuperarPassBtn.style.display = 'none';
            updatePassBtn.style.display = '';
            Swal.fire({
                icon: 'success',
                title: 'Token Enviado',
                text: 'Verifique el correo registrado',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    // Actualiza la contraseña
    function updatePass() {
        const dui = duiRecuperarPass.value.trim();
        const token = document.getElementById("tokenRecuperacion").value.trim();
        const contrasena = passwordInput.value.trim();

        if (!dui) {
            alertError('El DUI es requerido');
            return;
        }

        if (!token) {
            alertError('El token es requerido');
            return;
        }

        if (contrasena.length < 8) {
            alertError('La longitud de la contraseña tiene que ser mayor o igual a 8');
            return;
        }

        Swal.fire({
            title: 'Espere...',
            text: 'Verificando datos para actualizar contraseña...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        

        $.ajax({
            type: 'POST',
            url: baseURL + 'actualizarPwdReset',
            data: { dui, token, contrasena },
            dataType: 'json',
            success: handleUpdatePassResponse
        });
    }

    // Maneja la respuesta de la actualización de la contraseña
    function handleUpdatePassResponse(response) {
        Swal.close();
        if (response.error) {
            alertError(response.error);
        } else if (response.success) {
            resetForm();
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                text: 'Su contraseña se actualizó correctamente',
                confirmButtonText: 'Aceptar'
            });
        }
    }

    // Limpia y restablece el formulario
    function resetForm() {
        duiRecuperarPass.disabled = false;
        duiRecuperarPass.value = '';
        toggleEsconderInputs('none');
        recuperarPassBtn.style.display = '';
        updatePassBtn.style.display = 'none';
    }

    // Alterna la visibilidad de los campos adicionales
    function toggleEsconderInputs(displayStyle) {
        esconderInputs.forEach(element => {
            element.style.display = displayStyle;
            element.querySelector('input').value = ''; // Limpiar valor de los campos de entrada
        });
    }

    // Muestra un borde rojo en el campo y enfoca el campo
    function showFieldError(field, message) {
        field.style.borderColor = 'red';
        field.focus();
        alertError(message);
    }
});

