<?php
$session = \Config\Services::session();
$idSucursal = $session->get('sucursal');     // id_sucursal
$nombreSucursal = $session->get('sucursalN'); // Nombre de la sucursal
?>
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Reporte de Movimientos de inventario</h1>
    <p class="mb-4"></p>

    <!-- Filtro de Productos por Sucursal y Código -->
    <!-- Búsqueda por nombre o código -->
    <form class="border p-4 rounded mb-4 bg-light">
        <div class="form-row">
            <!-- <div class="form-group col-md-3">
                <label for="nombreSucursal">Sucursal</label>
                <select class="form-control" id="nombreSucursal" name="nombreSucursal">
                    <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?= $sucursal['id_sucursal'] ?>">
                            <?= $sucursal['sucursal'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div> -->
            <div class="form-group col-md-3">
                <label for="fechaInicio">Desde:</label>
                <input type="date" class="form-control" id="fechaInicio" onchange="validarFechas()">
            </div>
            <div class="form-group col-md-3">
                <label for="fechaFin">Hasta:</label>
                <input type="date" class="form-control" id="fechaFin" onchange="validarFechas()">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary btn-block" id="btnFiltrar" onclick="buscarReporte()">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary btn-block" id="btnImprimir" disabled onclick="ImprimirReporte()">
                    <i class="fa-solid fa-print"></i> Imprimir
                </button>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive movimientos-inventario-rp">
        <table id="tblMovimientosInventario" class="table table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th style="width: 25%;">Codigo</th>
                    <th style="width: 10%;">Cant.</th>
                    <th style="width: 10%;">Precio</th>
                    <th style="width: 10%;">fecha</th>
                    <th style="width: 10%;">Clase</th>
                    <th style="width: 15%;">Suc Origen</th>
                    <th style="width: 15%;">Suc Destino</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos dinámicos aquí -->
            </tbody>
            
        </table>
    </div>
</div>
<style>
    .grupo-titulo td {
    background-color: #e9ecef;
    font-weight: bold;
    font-size: 14px;
    padding: 8px 10px;
    color: #333;
    text-transform: uppercase;
    border-top: 2px solid #dee2e6;
    border-bottom: 2px solid #dee2e6;
}

.detalle-fila td {
    background-color: #fff;
    padding: 6px 10px;
    vertical-align: middle;
}

#tblMovimientosInventario td {
    word-break: break-word;
    vertical-align: middle;
}

</style>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<!-- jQuery UI JS -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/rpMovimientosInvt.js') ?>"></script>