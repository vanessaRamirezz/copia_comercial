<?php
$session = \Config\Services::session();
$idSucursal = $session->get('sucursal');     // id_sucursal
$nombreSucursal = $session->get('sucursalN'); // Nombre de la sucursal
?>
<style>
    tr.entrada {
        background-color: #d4edda !important;
        /* verde claro */
    }

    tr.salida {
        background-color: #f8d7da !important;
        /* rojo claro */
    }
</style>
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Reporte de existencia de productos</h1>
    <p class="mb-4">En este apartado podrás obtener los datos de los productos según filtros aplicados.</p>

    <!-- Filtro de Productos por Sucursal y Código -->
    <!-- Búsqueda por nombre o código -->
    <form class="border p-4 rounded mb-4 bg-light">
        <div class="form-group">
            <label for="campoBusqueda">Buscar por nombre o código del producto:</label>
            <input type="text" class="form-control" id="campoBusqueda" placeholder="Escriba al menos 3 caracteres...">
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="nombreSucursal">Sucursal</label>
                <input type="text" class="form-control" id="nombreSucursal" value="<?= $nombreSucursal ?>" readonly>
                <input type="hidden" id="filtroSucursal" value="<?= $idSucursal ?>">
            </div>
            <div class="form-group col-md-4">
                <label for="codigoProducto">Código del Producto</label>
                <input type="text" readonly class="form-control bg-white" id="codigoProducto" placeholder="Código seleccionado automáticamente">
            </div>
            <div class="form-group col-md-4 d-flex align-items-end">
                <button type="button" class="btn btn-primary btn-block" id="btnFiltrar">
                    <i class="fas fa-search"></i> Filtrar
                </button>
            </div>
        </div>
    </form>

    <!-- Tabla -->
    <div class="table-responsive">
        <table id="tablaProductos" class="table table-bordered table-hover" style="width:100%">
            <thead class="table-light">
                <tr>
                    <th>Fecha</th>
                    <th>Tipo de Movimiento</th>
                    <th>No. Documento</th>
                    <th>Ingreso</th>
                    <th>Salida</th>
                    <th>Existencia</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos dinámicos aquí -->
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right">Totales:</th>
                    <th id="totalEntrada">0</th>
                    <th id="totalSalida">0</th>
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
<script src="<?= base_url('public/js/kardex.js') ?>"></script>