<style>
    body {
        font-size: 0.85rem;
    }

    .table-sm th,
    .table-sm td {
        padding: 0.25rem 0.5rem;
    }

    .form-label {
        margin-bottom: 0.2rem;
    }

    .table th,
    .table td {
        vertical-align: middle;
    }
</style>
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Consulta de existencias</h1>
    <p class="mb-4">En este apartado podrás obtener las exitencia del producto en todas las sucursales.</p>

    <form class="border p-3 mb-4">
        <div class="row mb-2">
            <div class="col-md-6">
                <label for="campoBusqueda" class="form-label">Busqueda por nombre o codigo de producto:</label>
                <input type="text" class="form-control" id="campoBusqueda">
            </div>
        </div>
    </form>

    <div class="container contenedor-reporte contenido-consulta-existencias">
        <h4 class="text-center mb-4">Reporte de existencia de productos</h4>

        <div id="detalle-producto" class="mb-4" style="display: none;">
            <h5 class="mb-3">Información del producto</h5>
            <div class="row">
                <div class="col-md-4"><strong>Código:</strong> <span id="codigoProducto"></span></div>
                <div class="col-md-4"><strong>Nombre:</strong> <span id="nombreProducto"></span></div>
                <div class="col-md-4"><strong>Precio:</strong> $<span id="precioProducto"></span></div>
            </div>
        </div>

        <table class="table table-sm table-bordered table-hover bg-white" id="tablaExistencias" style="display: none;">
            <thead class="table-light">
                <tr>
                    <th>Sucursal</th>
                    <th>Existencia</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaExistencias">
                <!-- Filas dinámicas -->
            </tbody>
        </table>

    </div>

    <div class="row">
        <div class="col-sm text-center">
            <button class="btn btn-primary" id="btnImprimir" onclick="imprimir()">Imprimir</button>
        </div>
    </div>
</div>
<style>
    #detalle-producto {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        padding: 1rem;
        border-radius: 0.5rem;
    }

    #tablaExistencias td.text-danger {
        color:rgb(215, 8, 29);
    }

    #tablaExistencias td.text-success {
        color:rgb(6, 186, 48);
    }
</style>



<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/consultaExitencias.js') ?>"></script>