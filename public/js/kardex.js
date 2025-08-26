$(document).ready(function () {
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
                data: { search: request.term }, // Asegurar que se envía correctamente
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
            campoBusqueda.value = ui.item.value;
            $('#codigoProducto').val(ui.item.codPro);
        }
    });
    var tabla = $('#tablaProductos').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy',
            {
                extend: 'excelHtml5',
                title: 'KARDEX',
                messageTop: '', // vacío al inicio, lo ponemos dinámicamente
                action: function (e, dt, button, config) {
                    var nombreExcel = $('#codigoProducto').val() - $('#campoBusqueda').val();
                    // Obtener el código producto cuando se clickea el botón
                    var codigo = nombreExcel || 'SIN CÓDIGO';

                    // Cambiar el mensaje top dinámicamente
                    config.messageTop = 'CODIGO PRODUCTO: ' + codigo;

                    // Ejecutar la acción original para exportar
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                }
            },
            'pdf',
            'print'
        ],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
        },
        searching: false
    });

    $('#codigoProducto').on('input', function () {
        this.value = this.value.toUpperCase();
    });

    $('#btnFiltrar').on('click', function () {
        var codigo = $('#codigoProducto').val().trim();

        if (codigo === '') {
            alert('Ingrese un código de producto.');
            return;
        }

        $.ajax({
            url: baseURL + 'getKardex',
            type: 'POST',
            data: { codigo_producto: codigo },
            dataType: 'json',
            success: function (response) {
                console.log(response);
                tabla.clear();

                // Variables para sumas
                let sumaEntrada = 0;
                let sumaSalida = 0;
                let sumaTotal = 0;

                $.each(response, function (i, item) {
                    let entrada = parseFloat(item.entrada);
                    let salida = parseFloat(item.salida);
                    let cantidad = entrada > 0 ? entrada : salida;
                    let costoUnitario = parseFloat(item.costo_unitario);
                    let total = (cantidad * costoUnitario).toFixed(2);

                    // Acumular sumas
                    sumaEntrada += entrada;
                    sumaSalida += salida;
                    sumaTotal += parseFloat(total);

                    let rowNode = tabla.row.add([
                        item.fecha,
                        item.detalle,
                        item.noDocumento ?? '-',
                        item.entrada,
                        item.salida,
                        item.existencia,
                    ]).node();

                    if (entrada > 0) {
                        $(rowNode).addClass('entrada');
                    } else if (salida > 0) {
                        $(rowNode).addClass('salida');
                    }
                });

                tabla.draw();

                let disponible = sumaEntrada - sumaSalida;

                // Mostrar los resultados
                $('#totalEntrada').text(sumaEntrada);
                $('#totalSalida').text(sumaSalida);
                $('#totalDisponible').text(disponible);
                $('#totalGeneral').text('');
            },

            error: function (xhr) {
                console.error("Error:", xhr.responseText);
                alert('Ocurrió un error al obtener los datos del kardex.');
            }
        });

    });
});
