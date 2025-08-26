<?php
$session = \Config\Services::session();
$idSucursal = $session->get('sucursal');     // id_sucursal
$nombreSucursal = $session->get('sucursalN'); // Nombre de la sucursal
?>
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Reporte de ingresos diarios</h1>
    <p class="mb-4">En este apartado podrás obtener los datos de ingresos diarios.</p>

    <!-- Filtro de Productos por Sucursal y Código -->
    <!-- Búsqueda por nombre o código -->
    <form class="border p-4 rounded mb-4 bg-light">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="fechaInicio">Fecha de inicio:</label>
                <input type="date" class="form-control" id="fechaInicio" onchange="validarFechas()">
            </div>
            <div class="form-group col-md-3">
                <label for="fechaFin">Fecha fin:</label>
                <input type="date" class="form-control" id="fechaFin" onchange="validarFechas()">
            </div>
            <div class="form-group col-md-3 d-flex align-items-end">
                <button type="button" class="btn btn-primary btn-block" id="btnFiltrar" onclick="buscarReporte()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table id="tblIngresosDiarios" class="table table-bordered table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Sucursal</th>
                    <th>Contado</th>
                    <th>Items Contado</th>
                    <th>Primas</th>
                    <th>Items Primas</th>
                    <th>Cuotas</th>
                    <th>Items Cuotas</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos dinámicos aquí -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" style="text-align:right">Total:</th>
                    <th id="totalDisponible">0</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="<?= base_url('public/js/rpIngresosDiarios.js') ?>"></script>