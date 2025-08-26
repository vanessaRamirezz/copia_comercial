document.addEventListener("DOMContentLoaded", function () {
    console.log("Iniciando carga de datos...");

    function obtenerTextoPorId(id) {
        const texto = $(`#${id} option:selected`).text();
        // Compara correctamente el texto con las opciones que deseas
        return (texto === "Seleccione..." || texto === "Seleccione una sucursal" || !texto) ? "N/A" : texto;
    }


    document.getElementById('btnBuscar').addEventListener('click', function () {
        const sucursal = $('select[name="sucursal"]').val();

        const resumen = `<strong>Sucursal:</strong> ${obtenerTextoPorId("sucursal")}`;
        const contenedorResumen = document.getElementById('texto-resumen');
        if (contenedorResumen) {
            contenedorResumen.innerHTML = resumen;
        }

        const filtros = { sucursal };

        $.ajax({
            type: 'POST',
            url: baseURL + 'getDataExisteciaProd',
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
                }, 20000);
            },
            success: function (response) {
                Swal.close();
            
                const tbody = document.querySelector('table tbody');
                tbody.innerHTML = ''; // Limpiar la tabla antes de pintar nuevos resultados
            
                if (response.success && response.success.length > 0) {
                    response.success.forEach(producto => {
                        const fila = `
                            <tr>
                                <td>${producto.codigo_producto}</td>
                                <td>${producto.nombre}</td>
                                <td>${producto.disponibilidad}</td>
                            </tr>
                        `;
                        tbody.innerHTML += fila;
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center">No se encontraron resultados.</td>
                        </tr>
                    `;
                }
            },
            error: function (xhr, status, error) {
                console.error(status, error);
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

async function imprimirReporteCliente() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const elemento = document.querySelector('.contenido-reporte-cliente');
    const opciones = {
        margin: 0.5,
        filename: 'reporte_clientes.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    await html2pdf().set(opciones).from(elemento).save();

    Swal.close();
}
