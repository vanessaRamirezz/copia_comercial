document.addEventListener("DOMContentLoaded", function () {
    // Selecciona todos los elementos input y select dentro del contenedor con clase 'ver_datos_solicitud'
    const inputsAndSelects = document.querySelectorAll('.ver_datos_solicitud input, .ver_datos_solicitud select');

    // Deshabilita cada input y select encontrado
    inputsAndSelects.forEach(function(element) {
        element.disabled = true;
    });
});
