document.addEventListener("DOMContentLoaded", function () {
    setTimeout(() => {
        document.getElementById("campoBusqueda").value = "";
    }, 400);

    const campoBusqueda = document.getElementById("campoBusqueda");

    $(campoBusqueda).autocomplete({
        source: function (request, response) {
            // Mostrar opción de "Buscando resultados..."
            response([{ label: "Buscando resultados...", value: "" }]);
            $.ajax({
                url: baseURL + "getProducts",
                type: "POST",
                dataType: "json",
                data: { search: request.term}, // Asegurar que se envía correctamente
                success: function (data) {
                    console.log("Respuesta del servidor:", data);

                    // Asegurar que la respuesta sea un array de objetos
                    if (Array.isArray(data.success)) {
                        response(data.success.slice(0, 10).map(item => ({
                            label: item.nombre + " - " + item.codigo_producto,
                            value: item.nombre,
                            codPro: item.codigo_producto
                        })));
                    } else {
                        // Si no es un array, retornar mensaje amigable
                        response([{ label: "No se encontraron resultados", value: "" }]);
                    }
                },
                error: function () {
                    console.error("Error en la búsqueda de clientes");
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            campoBusqueda.value = ui.item.value; // Mostrar el nombre en el input
            //duiBuscarCliente(ui.item.dui); // Pasar el DUI como parámetro
            addProduct(ui.item.codPro);
        }
    });

    function addProduct(inputProduct) {
        var search = inputProduct;
    
        Swal.fire({
            title: 'Espere...',
            html: 'Procesando solicitud...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            type: "POST",
            url: baseURL + 'consultaExistenciaAllsuc',
            data: {
                search: search
            },
            dataType: "json",
            success: function (rsp) {
                console.log(rsp);
            
                if (rsp.data) {
                    const producto = rsp.data;
            
                    // Mostrar resumen de filtros
                    $('#texto-resumen').text(`Producto consultado: ${producto.nombre} (${producto.codigo})`);
            
                    // Mostrar detalles del producto
                    $('#codigoProducto').text(producto.codigo);
                    $('#nombreProducto').text(producto.nombre);
                    $('#precioProducto').text(parseFloat(producto.precio).toFixed(2));
                    $('#detalle-producto').show();
            
                    // Limpiar tabla anterior
                    $('#cuerpoTablaExistencias').empty();
            
                    // Agregar filas
                    producto.existencias.forEach(item => {
                        $('#cuerpoTablaExistencias').append(`
                            <tr>
                                <td>${item.sucursal}</td>
                                <td class="${item.existencia == 0 ? 'text-danger' : 'text-success'} fw-bold">${item.existencia}</td>
                            </tr>
                        `);
                    });
            
                    // Mostrar tabla
                    $('#tablaExistencias').show();
                } else {
                    toastr.warning("No se encontraron datos del producto.");
                    $('#detalle-producto, #tablaExistencias').hide();
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
})


async function imprimir() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    const elemento = document.querySelector('.contenido-consulta-existencias');
    const opciones = {
        margin: 0.5,
        filename: 'consultaExistencias.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    await html2pdf().set(opciones).from(elemento).save();

    Swal.close();
}
