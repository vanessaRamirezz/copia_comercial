<style>
    body {
      font-size: 0.85rem;
    }
    .table-sm th, .table-sm td {
      padding: 0.25rem 0.5rem;
    }
    .form-label {
      margin-bottom: 0.2rem;
    }
    .table th, .table td {
      vertical-align: middle;
    }
  </style>
<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Reporte de existencia de productos</h1>
    <p class="mb-4">En este apartado podrás obtener los datos de los clientes.</p>

    <form class="border p-3 mb-4">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Sucursal</label>
                <select class="form-control" name="sucursal" id="sucursal">
                    <option value="">Seleccione una sucursal</option>
                    <?php foreach ($sucursales as $sucursal): ?>
                        <option value="<?= $sucursal['id_sucursal'] ?>"><?= $sucursal['sucursal'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="btnBuscar">Buscar</button>
            </div>
        </div>
    </form>

    <div class="container contenedor-reporte contenido-reporte-cliente">
        <h4 class="text-center mb-4">Reporte de existencia de productos</h4>

        <div class="mb-3 p-2 border bg-light" id="resumen-filtros">
            <strong>Filtros aplicados:</strong>
            <p id="texto-resumen">Aún no se han aplicado filtros.</p>
        </div>

        <table class="table table-sm table-bordered table-hover bg-white">
            <thead class="table-light">
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <div class="row">
        <div class="col-sm text-center">
            <button class="btn btn-primary" id="btnImprimir" onclick="imprimirReporteCliente()">Imprimir</button>
        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/rpExistenciaXsucursal.js') ?>"></script>