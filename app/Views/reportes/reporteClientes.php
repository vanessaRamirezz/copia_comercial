<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Reporte de clientes</h1>
    <p class="mb-4">En este apartado podrás obtener los datos de los clientes.</p>

    <form class="border p-3 mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="departamento">Departamento</label>
                <select id="departamento" class="form-control" data-depto-seleccionado="-1">
                    <option value="-1" selected>Seleccione...</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="municipio">Municipio</label>
                <select id="municipio" class="form-control" data-municipio-seleccionado="-1">
                    <option value="-1" selected>Seleccione...</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="distrito">Distrito</label>
                <select id="distrito" class="form-control" data-distrito-seleccionado="-1">
                    <option value="-1" selected>Seleccione...</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="colonia">Colonia</label>
                <select id="colonia" class="form-control" data-colonia-seleccionada="-1">
                    <option value="-1" selected>Seleccione...</option>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Sucursal</label>
                <select class="form-control" name="sucursal" id="sucursal">
                    <option value="">Seleccione una sucursal</option>
                    <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?= $sucursal['id_sucursal'] ?>"><?= $sucursal['sucursal'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Estado</label>
                <select class="form-control" name="estado" id="estado">
                    <option value="Activos">Activos</option>
                    <option value="Cancelados">Cancelados</option>
                </select>
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="btnBuscar">Buscar</button>
            </div>
        </div>
    </form>

    <div class="container contenedor-reporte contenido-reporte-cliente">
        <h4 class="text-center mb-4">Reporte de Clientes</h4>

        <div class="mb-3 p-2 border bg-light" id="resumen-filtros">
            <strong>Filtros aplicados:</strong>
            <p id="texto-resumen">Aún no se han aplicado filtros.</p>
        </div>

        <table class="table custom-table">
            <thead>
                <tr>
                    <th>DUI</th>
                    <th>Nombre</th>
                    <th>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <!-- Datos cargados dinámicamente -->
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-sm text-center">
            <button class="btn btn-primary" id="btnImprimir" onclick="imprimirReporteCliente()">Imprimir</button>
        </div>
    </div>
</div>

<style>
    .custom-table {
        border-collapse: collapse;
        width: 100%;
        font-size: 0.75rem; /* Tamaño compacto para impresión */
    }

    .custom-table thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 0.4rem;
    }

    .custom-table td {
        vertical-align: middle;
        border-top: none;
        border-bottom: 1px solid #dee2e6;
        padding: 0.4rem;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .sub-row {
        font-size: 0.7rem;
        padding-left: 20px;
        color: #555;
    }

    .total-row {
        font-weight: bold;
        background-color: #f1f1f1;
    }

    .custom-table, 
.custom-table tr, 
.custom-table td, 
.custom-table th {
    page-break-inside: avoid;
}

</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/rpClientes.js') ?>"></script>
