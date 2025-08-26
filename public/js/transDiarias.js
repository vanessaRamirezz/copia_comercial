document.addEventListener("DOMContentLoaded", function () {
    document.getElementById('totalGastosInput').addEventListener('input', function () {
        const valor = parseFloat(this.value);
        document.getElementById('mostrarGastos').textContent = isNaN(valor) ? '0.00' : valor.toFixed(2);
    });

    document.getElementById('otrosIngresosInput').addEventListener('input', function () {
        const valor = parseFloat(this.value);
        document.getElementById('totalOtros').textContent = isNaN(valor) ? '0.00' : valor.toFixed(2);
    });


    $('#btnBuscar').click(function () {
        let fechaInicio = $('#fechaInicio').val();
        let fechaFin = $('#fechaFin').val();

        if (!fechaInicio) {
            alertError('Debe seleccionar ambas fechas.');
            return;
        }

        if (!fechaFin) {
            alertError('Debe seleccionar ambas fechas.');
            return;
        }

        Swal.fire({
            title: 'Espere...',
            text: 'Consultando datos...',
            allowEscapeKey: false,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: baseURL + 'consultarTransDiarias',
            type: 'POST',
            dataType: 'json',
            data: {
                fechaInicio: fechaInicio,
                fechaFin: fechaFin
            },
            success: function (response) {
                console.log(response);
                llenarTabla(response.data);
                const inicioFormateado = formatearFecha(fechaInicio);
                const finFormateado = formatearFecha(fechaFin);
                document.getElementById('rangoFechas').innerHTML = `Desde ${inicioFormateado} &nbsp;&nbsp;&nbsp; Hasta ${finFormateado}`;
                Swal.close();
            },
            error: function (xhr, status, error) {
                console.error('Error en la petición:', error);
                Swal.close();
            }
        });
    });

    // Supongamos que ya recibiste la respuesta en la variable "response"
    function llenarTabla(data) {
        console.log(data);
        let tbody = document.getElementById("tablaTransacciones");
        tbody.innerHTML = ""; // Limpiar tabla antes de insertar

        // Inicializar totales de ingresos y egresos
        let totalCuotas = 0;
        let totalPrimas = 0;
        let totalContado = 0;
        let totalInteres = 0;
        let totalOtros = 0;
        let totalGeneral = 0;

        // Iterar sobre los datos
        data.forEach(item => {
            const concepto = (item.Concepto || '').toLowerCase();
            const abono = parseFloat(item.abono) || 0;
            const pago = parseFloat(item.pago) || 0;
            const contado = parseFloat(item.Contado) || 0;
            const interes = parseFloat(item.Interes) || 0;
            const prima = parseFloat(item.PrimaCa) || 0;

            if ((concepto.includes('cuota') && parseInt(item.prima) === 0) || concepto.includes('pronto pago')) {
                totalCuotas += abono + pago + contado;
            } else if ((concepto.includes('prima') || parseInt(item.prima) === 1)) {
                totalPrimas += prima;
            } else if (item.Tipo == 'CONTADO') {
                totalContado += abono + pago + contado;
            }

            if (item.Interes != 0) {
                totalInteres += interes;
            }

            let fila = `
            <tr>
                <td>${item.Codigo}</td>
                <td>${item.Concepto}</td>
                <td>${item.Docum}</td>
                <td>${item.Tipo}</td>
                <td>${item.Fecha}</td>
                <td>${formatoDosDecimales(item.PrimaCa)}</td>
                <td>${formatoDosDecimales(item.abono)}</td>
                <td>${formatoDosDecimales(item.pago)}</td>
                <td></td>
                <td>${formatoDosDecimales(item.Interes)}</td>
                <td>${formatoDosDecimales(item.Contado)}</td>
                <td>${calcularTotal(item)}</td>
            </tr>
        `;
            totalGeneral += parseFloat(calcularTotal(item));
            tbody.insertAdjacentHTML('beforeend', fila);
        });

        // Agregar fila de total general en la tabla
        let filaTotal = `
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="11" class="text-end">Total General</td>
            <td>${totalGeneral.toFixed(2)}</td>
        </tr>
    `;
        tbody.insertAdjacentHTML('beforeend', filaTotal);

        // Mostrar los totales de ingresos
        document.getElementById('totalCuotas').textContent = totalCuotas.toFixed(2);
        document.getElementById('totalPrimas').textContent = totalPrimas.toFixed(2);
        document.getElementById('totalContado').textContent = totalContado.toFixed(2);
        document.getElementById('totalInteres').textContent = totalInteres.toFixed(2);
        

        // Leer otros ingresos
        const otrosIngresos = parseFloat(document.getElementById('otrosIngresosInput')?.value) || 0;
        document.getElementById('totalOtros').textContent = otrosIngresos.toFixed(2);
        // Calcular el total de ingresos incluyendo otros ingresos
        let totalGeneralIngresos = (
            totalCuotas +
            totalPrimas +
            totalContado +
            totalInteres +
            totalOtros +
            otrosIngresos
        );

        document.getElementById('totalGeneralIngresos').textContent = totalGeneralIngresos.toFixed(2);

        // Mostrar el total de egresos
        const totalGastos = parseFloat(document.getElementById('totalGastosInput').value) || 0;
        document.getElementById('mostrarGastos').textContent = totalGastos.toFixed(2);
        document.getElementById('totalEgresos').textContent = totalGastos.toFixed(2);

        // Calcular Balance Final
        let balanceFinal = (totalGeneralIngresos - totalGastos).toFixed(2);
        document.getElementById('balanceFinal').textContent = balanceFinal;
    }

    function formatoDosDecimales(valor) {
        const num = parseFloat(valor);
        if (isNaN(num) || num === 0) {
            return '';
        }
        return num.toFixed(2);
    }



    // Función para sumar totales por fila
    function calcularTotal(item) {
        const prima = parseFloat(item.PrimaCa) || 0;
        const abono = parseFloat(item.abono) || 0;
        const pago = parseFloat(item.pago) || 0;
        const contado = parseFloat(item.Contado) || 0;
        const interes = parseFloat(item.Interes) || 0;
        return (abono + pago + contado + interes+prima).toFixed(2);
    }


    function alertError(text) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: text
        });
    }

    function formatearFecha(fecha) {
        const partes = fecha.split('-');
        return `${partes[2]}/${partes[1]}/${partes[0]}`;
    }
});
function validarFechas() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFin = document.getElementById('fechaFin').value;

    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        alert('La fecha fin no puede ser menor que la fecha de inicio.');
        document.getElementById('fechaFin').value = '';
    }
}


function imprimirEstadoCuenta() {
    Swal.fire({
        title: 'Espere...',
        text: 'Generando PDF...',
        allowEscapeKey: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    const elemento = document.querySelector('.contenido-reporte-trans-diario');
    const opciones = {
        margin: 0.2,
        filename: 'reporte_transacciones_diario.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };

    html2pdf().set(opciones).from(elemento).save();
    Swal.close();
}