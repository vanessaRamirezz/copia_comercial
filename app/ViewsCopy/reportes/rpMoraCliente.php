<style>
    #tabla-reporte {
        border-collapse: collapse;
        width: 100%;
        font-family: monospace;
        font-size: 14px;
    }

    #tabla-reporte thead th {
        border: 1px solid #000;
        /* Bordes solo en encabezado */
        background-color: #f2f2f2;
        text-align: left;
        padding: 5px;
    }

    #tabla-reporte tbody td {
        border: none;
        padding: 5px;
    }

    #tabla-reporte,
    #tabla-reporte thead,
    #tabla-reporte tbody {
        border: none;
        /* Eliminar borde externo */
    }

    .custom-table, 
.custom-table tr, 
.custom-table td, 
.custom-table th {
    page-break-inside: avoid;
}
</style>



<div class="container-fluid mt-4">
    <h1 class="h3 mb-2 text-gray-800">Estado de cuentas</h1>
    <p class="mb-4">En este apartado podras obtener el estado de cuenta por No solicitud/No de contrato.</p>
    <!-- Fechas y Total de Gastos -->
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
            <div class="form-group col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" id="btnBuscar">Buscar</button>
            </div>
        </div>
    </form>

    <hr class="my-4">

    <!-- Reporte Estilo -->
    <div class="reporte-box contenido-reporte-mora-clientes">

        <h4 class="text-center mb-4">Reporte de Mora Clientes</h4>

        <div class="mb-3 p-2 border bg-light" id="resumen-filtros">
            <strong>Filtros aplicados:</strong>
            <p id="texto-resumen">Aún no se han aplicado filtros.</p>
        </div>
        <table id="tabla-reporte" class="custom-table" border="1" cellspacing="0" cellpadding="5" style="font-family: monospace; font-size: 14px; width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #f2f2f2; text-align: left;">
                    <th>Código</th>
                    <th>Nombre del Cliente</th>
                    <th>Sin / Ven</th>
                    <th>1 - 30</th>
                    <th>31 - 60</th>
                    <th>61 - 90</th>
                    <th>91 - 120</th>
                    <th>121 - 150</th>
                    <th>> 150</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody id="cuerpo-tabla">
                <!-- JS insertará las filas aquí -->
            </tbody>
        </table>

    </div>
    <div class="row mt-3">
        <div class="col-sm text-center">
            <button class="btn btn-primary" id="btnImprimir" onclick="imprimirMoraCliente()">Imprimir</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script src="<?= base_url('public/js/rpMoraClientes.js') ?>"></script>