<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Estado de cuentas</h1>
    <p class="mb-4">En este apartado podras obtener el estado de cuenta por No solicitud/No de contrato.</p>
    <!-- Fechas y Total de Gastos -->
    <div class="row">
        <div class="col-md-3">
            <label for="fechaInicio">Fecha de inicio:</label>
            <input type="date" class="form-control" id="fechaInicio" onchange="validarFechas()">
        </div>

        <div class="col-md-3">
            <label for="fechaFin">Fecha fin:</label>
            <input type="date" class="form-control" id="fechaFin" onchange="validarFechas()">
        </div>

        <div class="col-md-3">
            <label for="totalGastosInput">Total de Gastos:</label>
            <input type="number" class="form-control" id="totalGastosInput" placeholder="Ingrese total de gastos">
        </div>

        <div class="col-md-3 d-flex align-items-end">
            <button type="button" class="btn btn-primary w-100" id="btnBuscar">Buscar</button>
        </div>
    </div>

    <hr class="my-4">

    <!-- Reporte Estilo -->
    <div class="reporte-box contenido-reporte-trans-diario">
        <div class="reporte-title">COMERCIAL TODO PARA EL HOGAR S.A. DE C.V.</div>
        <div class="reporte-title">Listado de Transacciones Diarias</div>
        <div class="text-center" id="rangoFechas">Desde 00/00/0000 &nbsp;&nbsp;&nbsp; Hasta 00/00/0000</div>

        <!-- Tabla de Transacciones -->
        <table class="table table-sm text-center" style="margin-top: -20px;">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Concepto</th>
                    <th>Docum</th>
                    <th>TIPO</th>
                    <th>Fecha</th>
                    <th>Cuota Ini</th>
                    <th>Abono Cuo</th>
                    <th>Pago Cuot</th>
                    <th>Cuota Reb</th>
                    <th>Intereses</th>
                    <th>Contado</th>
                    <th>Totales</th>
                </tr>
            </thead>
            <tbody id="tablaTransacciones">
                <!-- Aquí se insertan las filas -->
            </tbody>
        </table>


        <!-- Texto estilo reporte -->
        <div class="mt-1">
            TOTALES -------------------------------<br>
            TOTAL ---------> FACTURA :<br>
            TOTAL ---------> C.C.F. :<br>
            Clientes Atendidos =====================
        </div>

        <div class="mt-1">
            I N G R E S O S<br>
            Pagos de Cuota : $<span id="totalCuotas">0.00</span><br>
            Pagos de Primas : $<span id="totalPrimas">0.00</span><br>
            Ventas Al Contado : $<span id="totalContado">0.00</span><br>
            Pagos de Interes : $<span id="totalInteres">0.00</span><br>
            Otros : $<span id="totalOtros">0.00</span><br>
            --------------------------<br>
            Total General : $<span id="totalGeneralIngresos">0.00</span>
        </div>

        <div class="mt-1">
            E G R E S O S<br>
            Total Gastos : $<span id="mostrarGastos">0.00</span><br>
            --------------------------<br>
            <!--             $<span id="totalEgresos">0.00</span><br>
            --------------------------<br> -->
            Total Efectivo : $<span id="totalEgresos">0.00</span>
        </div>

        <div>
            Balance Final: $<span id="balanceFinal">0.00</span>
        </div>

    </div>
    <div class="row">
        <div class="col-sm text-center">
            <button class="btn btn-primary" id="btnImprimir" onclick="imprimirEstadoCuenta()">Imprimir</button>
        </div>
    </div>
</div>


<style>
    .reporte-box {
        font-family: 'Courier New', monospace;
        font-size: 11px;
        white-space: pre-wrap;
        background: #f9f9f9;
        padding: 10px;
    }

    .reporte-title {
        text-align: center;
        font-weight: bold;
        font-size: 16px;
        margin-top: 2px;
        /* Reducir margen superior */
        margin-bottom: 2px;
        /* Reducir margen inferior */
        line-height: 0.1;
        /* Hacer los títulos más compactos */
    }

    .text-center {
        margin-top: 0px;
        /* Eliminar espacio extra antes del texto */
        margin-bottom: 0px;
        /* Eliminar espacio extra después del texto */
    }

    .table th,
    .table td {
        border: none;
        /* Eliminar bordes */
        padding: 4px;
        /* Ajustar el espaciado en las celdas */
    }
    .table th,
    .table td {
        border: none; /* Eliminar bordes */
        padding: 4px; /* Reducir el espaciado en las celdas */
        font-size: 10px; /* Reducir el tamaño de la fuente */
    }

    .table {
        border-spacing: 0; /* Eliminar espacio entre celdas */
        border-collapse: collapse; /* Colapsar bordes */
    }

    .thead-light th {
        background-color:rgb(255, 255, 255);
        /* Color claro para los encabezados */
    }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/transDiarias.js') ?>"></script>