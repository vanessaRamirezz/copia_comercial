<style>
    /* Añadir más espacio entre las filas */
    .dataTables_wrapper .dataTable th,
    .dataTables_wrapper .dataTable td {
        padding: 12px 15px;
        /* Ajustar según sea necesario */
    }

    .modal-body {
        padding: 20px;
        /* Ajustar según sea necesario */
    }
</style>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Formulario para salidas de producto</h1>
    <div class="card shadow mb-4">
        <div class="card-body ingreso_x_compra">
            <div class="form-row justify-content-end">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalVerDocumentos">
                    Ver Documentos
                </button>

            </div>
            <div class="form-row">
                <div class="form-group col-md-2">
                    <label for="fecha" class="form-label">Fecha:</label>
                    <input type="date" class="form-control form-control-sm" id="fecha" name="fecha" readonly>
                </div>
                <div class="form-group col-md-2">
                    <label for="estado" class="form-label">Estado:</label>
                    <select class="form-control form-control-sm" id="estado" name="estado" readonly disabled>
                        <option value="-1">Seleccione...</option>
                        <option value="Procesado" selected>Procesado</option>
                        <option value="Anulado">Anulado</option>
                        <option value="Cerrado">Cerrado</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="tipoMovimiento" class="form-label">Tipo movimiento:</label>
                    <select class="form-control form-control-sm" id="tipoMovimiento" name="tipo_Movimiento">
                        <option value="-1" data-tipo-mov="-1">Seleccione...</option>
                        <?php foreach ($tiposMovimientos as $tipoMovimiento) : ?>
                            <?php if ($tipoMovimiento['id_tipo_movimiento'] == 5 || ($tipoMovimiento['id_tipo_movimiento'] >= 7 && $tipoMovimiento['id_tipo_movimiento'] <= 10)) : ?>
                                <option value="<?php echo esc($tipoMovimiento['id_tipo_movimiento']); ?>" data-tipo-mov="<?php echo esc($tipoMovimiento['tipo_mov']); ?>"><?php echo esc($tipoMovimiento['descripcion']); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3" id="sucursalOrigenGroup" style="display: none;">
                    <label for="sucursal_origen" class="form-label">Sucursal Origen:</label>
                    <select class="form-control form-control-sm" id="sucursal_origen" name="sucursal_origen" disabled>
                        <option value="-1">Seleccione...</option>
                        <?php foreach ($sucursales as $sucursal) : ?>
                            <option value="<?= htmlspecialchars($sucursal['id_sucursal']); ?>">
                                <?= htmlspecialchars($sucursal['sucursal']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group col-md-3">
                    <label for="correlativo" class="form-label">Correlativo:</label>
                    <input type="text" class="form-control form-control-sm" id="correlativo" name="correlativo">
                </div>
                <div class="form-group col-md-3">
                    <label for="documento" class="form-label">No. Documento:</label>
                    <input type="text" class="form-control form-control-sm" id="documento" name="noDocumento">
                </div>

            </div>
            <div class="form-row">
                <div class="form-group col-md">
                    <label for="observacion" class="form-label">Observación:</label>
                    <textarea class="form-control form-control-sm" id="observacion" name="observacion" rows="2"></textarea>
                </div>
            </div>
            <!-- <div class="form-row"> -->
                <!-- <div class="container mb-4"> -->
                <div class="row mb-2">
                        <!-- Filtro de tipo de búsqueda -->
                        <!-- <div class="col-md-3">
                            <label for="tipoBusqueda" class="form-label">Buscar Cliente por:</label>
                            <select class="form-control" id="tipoBusqueda">
                                <option selected disabled>Seleccione filtro</option>
                                <option value="codpro">Codigo producto</option>
                                <option value="nombre">Nombre</option>
                            </select>
                        </div> -->

                        <!-- Campo de búsqueda -->
                        <div class="col-md-6">
                            <label for="campoBusqueda" class="form-label">Busqueda por nombre o codigo de producto:</label>
                            <input type="text" class="form-control" id="campoBusqueda">
                        </div>

                        <!-- Botón separado del input -->
                        <!-- <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-primary w-100" id="btnBuscar" style="display: none;">Buscar</button>
                        </div> -->
                    </div>
                <!-- </div> -->
                <!-- <div class="form-group col-md-3">
                    <input type="text" class="form-control" id="buscar_producto" placeholder="Buscar producto">
                </div>
                <div class="form-group col-md-2">
                    <button type="button" class="btn btn-primary" onclick="addProduct()">Buscar</button>
                </div> -->

            <div class="table-responsive" id="tablaContainer">

            </div>
            <div class="form-row" id="validaGranTotal">
                <div class="form-group col-md-12 text-right">
                    <label for="total">TOTAL:</label>
                    <input type="text" class="form-control form-control-sm d-inline-block w-auto" id="total" name="total" readonly>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <button type="button" class="btn btn-primary mb-2 btn-validarDatosIXC">Guardar datos</button>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalVerDocumentos" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modalVerDocumentos" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalVerDocumentosLabel">Ver Documentos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-5">
                        <label for="filtrotipoMovimiento" class="form-label">Filto por tipo de movimiento:</label>
                        <select class="form-control form-control-sm" id="filtrotipoMovimiento" name="filtro_tipo_Movimiento">
                            <option value="-1">Seleccione...</option>
                            <?php foreach ($tiposMovimientosM as $tiposMovimientos) : ?>
                                <?php if ($tiposMovimientos['id_tipo_movimiento'] != 1) : ?>
                                    <option value="<?php echo esc($tiposMovimientos['id_tipo_movimiento']); ?>"><?php echo esc($tiposMovimientos['descripcion']); ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm">
                        <!-- Tabla para mostrar los documentos -->
                        <table id="documentosTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Usuario Creación</th>
                                    <th>Sucursal</th>
                                    <th>Proveedor</th>
                                    <th>Tipo de movimiento</th>
                                    <th>Estado</th>
                                    <th>No Documento</th>
                                    <th>Correlativo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Los datos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('public/js/formato_salida.js') ?>"></script>