<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Transacciones Diarias</h1>
    <p class="mb-4">En este apartado podras obtener las transacciones diarias por la sucursal logueada.</p>
    <!-- Fechas y Total de Gastos -->
    <div class="row">
        <div class="col-md-2">
            <label for="fechaInicio">Fecha de inicio:</label>
            <input type="date" class="form-control" id="fechaInicio" onchange="validarFechas()">
        </div>

        <div class="col-md-2">
            <label for="fechaFin">Fecha fin:</label>
            <input type="date" class="form-control" id="fechaFin" onchange="validarFechas()">
        </div>

        <div class="col-md-3">
            <label for="totalGastosInput">Total de Gastos:</label>
            <input type="number" class="form-control" id="totalGastosInput" placeholder="Ingrese total de gastos">
        </div>
        <div class="col-md-3">
            <label for="otrosIngresosInput">Otros ingresos:</label>
            <input type="number" class="form-control" id="otrosIngresosInput" placeholder="Otros Ingresos">
        </div>

        <div class="col-md-2 d-flex align-items-end">
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
        <table class="table table-sm text-center" style="margin-top: 20px;">
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


        <div class="reporte-box">
    <div>TOTALES -------------------------------</div>
    <div>TOTAL ---------> FACTURA :</div>
    <div>TOTAL ---------> C.C.F. :</div>
    <div>Clientes Atendidos =====================</div>

    <div style="margin-top:5px;">I N G R E S O S</div>
    <div>Pagos de Cuota : $<span id="totalCuotas">0.00</span></div>
    <div>Pagos de Primas : $<span id="totalPrimas">0.00</span></div>
    <div>Ventas al Contado : $<span id="totalContado">0.00</span></div>
    <div>Pagos de Interes : $<span id="totalInteres">0.00</span></div>
    <div>Otros : $<span id="totalOtros">0.00</span></div>
    <div>--------------------------</div>
    <div>Total Ingresos : $<span id="totalGeneralIngresos">0.00</span></div>

    <div style="margin-top:5px;">E G R E S O S</div>
    <div>Total Gastos : $<span id="mostrarGastos">0.00</span></div>
    <div>--------------------------</div>
    <div>Total Egresos : $<span id="totalEgresos">0.00</span></div>

    <div style="margin-top:5px;">Balance Final: $<span id="balanceFinal">0.00</span></div>
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
        font-size: 9px;
        background: #f9f9f9;
        padding: 5px;
        white-space: normal;
        /* antes: pre-wrap */
    }

    /* Eliminar márgenes y reducir espacios entre bloques */
    .reporte-box div,
    .reporte-box p {
        margin-top: 0;
        margin-bottom: 0;
        line-height: 1.1;
        /* compactar texto verticalmente */
    }

    .reporte-box br {
        line-height: 0.9;
        /* hacer saltos de línea más bajos */
    }

    .reporte-title {
        text-align: center;
        font-weight: bold;
        font-size: 11px;
        margin-top: 2px;
        margin-bottom: 2px;
        line-height: 1;
    }

    .text-center {
        margin-top: 0px;
        margin-bottom: 0px;
    }

    .table th,
    .table td {
        border: none;
        padding: 2px 4px;
        /* menos padding aún para compactar */
        font-size: 9px;
    }

    .table {
        border-spacing: 0;
        border-collapse: collapse;
    }

    .thead-light th {
        background-color: rgb(255, 255, 255);
    }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/transDiarias.js') ?>"></script>