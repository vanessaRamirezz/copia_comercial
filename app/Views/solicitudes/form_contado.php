<?php
$session = \Config\Services::session();
$sucursal = $session->get('sucursalN');
$vendedor = $session->get('nombres') . ' ' . $session->get('apellidos');
$id_sucursal = $session->get('sucursal');
$id_vendedor = $session->get('id_usuario');
$id_perfil = $session->get('id_perfil');
?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Ventas de contado</h1>
    <p class="mb-4">Registro de ventas de contado</p>

    <div class="accordion" id="accordionExample">
        <div class="card">
            <div class="card-header" id="headingOne">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        Registros de compras de contado
                    </button>
                </h2>
            </div>

            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="dataTableSolVariasTab" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">No Solicitud</th>
                                    <th class="text-nowrap">DUI</th>
                                    <th class="text-nowrap">Cliente</th>
                                    <th class="text-nowrap">Fecha creación</th>
                                    <th class="text-nowrap">Estado</th>
                                    <th class="text-nowrap">Usuario creador</th>
                                    <th class="text-nowrap">Operaciones</th>
                                </tr>
                            </thead>
                            <tbody id="dataTableSolVarias">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header" id="headingTwo">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Nueva venta de contado
                    </button>
                </h2>
            </div>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                <div class="card-body">

                    <div class="container">
                        <div class="card">
                            <div class="card-header text-center">
                                <h5>FACTURACIÓN DE CONTADO</h5>
                            </div>
                            <div class="card-body">
                                <!-- Encabezado -->
                                <!-- Selección del método de búsqueda -->
                                <div class="row mb-3 alert alert-primary">
                                    <div class="col-md-6">
                                        <label for="tipoBusqueda" class="form-label">Buscar Cliente por:</label>
                                        <select class="form-control" id="tipoBusqueda">
                                            <option selected>Seleccione forma de busqueda</option>
                                            <option value="dui">DUI</option>
                                            <option value="nombre">Nombre</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="campoBusqueda" class="form-label">Ingrese el dato:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="campoBusqueda" placeholder="Ingrese DUI o Nombre">
                                            <button class="btn btn-primary" id="btnBuscar" style="display: none;">Buscar</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lista de clientes (se muestra solo si se busca por nombre) -->
                                <div class="row mt-3" id="contenedorListaClientes" style="display: none;">
                                    <div class="col-md-12">
                                        <label for="listaClientes" class="form-label">Seleccione un Cliente:</label>
                                        <select class="form-control" id="listaClientes" size="5"></select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="noDoc" class="form-label">No. Doc:</label>
                                        <input type="text" class="form-control" id="noDoc" disabled placeholder="Número de documento">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha" class="form-label">Fecha:</label>
                                        <input type="date" class="form-control" id="fecha" disabled value="<?= isset($fechaVirtual) ? esc($fechaVirtual) : '' ?>">
                                        <!-- <input type="date" class="form-control" id="fecha"> -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" disabled hidden class="form-control" id="id_cliente" value="" autocomplete="FALSE">
                                        <input type="text" disabled hidden class="form-control" id="id_vendedor" value="<?= $id_vendedor ?>" autocomplete="FALSE">
                                        <label for="duiCliente" class="form-label">DUI:</label>
                                        <input type="text" class="form-control duiG" id="duiCliente" placeholder="Documento único de identidad">
                                    </div>
                                    <!-- <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="noDuiCheckbox">
                                            <label class="form-check-label" for="noDuiCheckbox">El cliente no tiene DUI</label>
                                        </div>
                                    </div> -->
                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-group w-100">
                                            <label for="noDuiCheckbox">Tipo de cliente</label>
                                            <select class="form-control" id="noDuiCheckbox">
                                                <option value="" selected>Seleccione...</option>
                                                <option value="noDui">El cliente no tiene DUI</option>
                                                <option value="clienteRapido">Cliente rápido</option>
                                            </select>
                                        </div>
                                    </div>


                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="nombreCliente" class="form-label">Nombre:</label>
                                        <input type="text" class="form-control" id="nombreCliente" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="direccionCliente" class="form-label">Dirección:</label>
                                        <input type="text" class="form-control" id="direccionCliente" disabled>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="telefonoCliente" class="form-label">Teléfono:</label>
                                        <input type="text" class="form-control" id="telefonoCliente" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="correoCliente" class="form-label">Correo:</label>
                                        <input type="email" class="form-control" id="correoCliente" disabled>
                                    </div>
                                </div>
                                <div class="row mt-3 mb-3">
                                    <div class="col-md-6">
                                        <label for="usuarioVendedor" class="form-label">Seleccione el vendedor:</label>
                                        <select name="usuarioVendedor" id="usuarioVendedor" class="form-control">
                                            <option value="">Seleccione usuario</option>
                                            <?php foreach ($usuarioSucursal as $usuario): ?>
                                                <option value="<?= $usuario->id_usuario ?>">
                                                    <?= strtoupper($usuario->nombres . ' ' . $usuario->apellidos) ?> - <?= $usuario->dui ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4 p-3">
                                    <button type="button" class="btn btn-primary" id="btnAgregarProductoMdl" data-toggle="modal" data-target="#agregarProductoTemp">
                                        Agregar producto
                                    </button>
                                </div>
                                <!-- Tabla de productos -->
                                <div class="row align-items-center justify-content-center mb-4">
                                    <div class="col-md-10"> <!-- Ajustar el ancho del contenido -->
                                        <table class="table table-sm table-bordered" id="productosSeleccionadosTbl">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Cod</th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col" class="text-end">Precio unidad</th>
                                                    <th scope="col" class="text-center">Cantidad</th>
                                                    <th scope="col" class="text-end">Precio total</th>
                                                    <th scope="col" class="text-center">Opción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Aquí van las filas dinámicas de productos -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="4" class="text-end">TOTAL</th>
                                                    <th class="text-end">
                                                        <input type="text" id="saldoAPagar" class="form-control text-end" placeholder="Total General" readonly>
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>


                                <div class="row mb-4">
                                    <div class="col-sm">
                                        <label for="">Sucursal: <b><?= htmlspecialchars($sucursal); ?></b></label>
                                    </div>
                                    <div class="col-sm">
                                        <!-- <label for="">Vendedor: <b><?= htmlspecialchars($vendedor); ?></b></label> -->
                                        <label for="">Vendedor: <b id="labelVendedor"><?= htmlspecialchars($vendedor); ?></b></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="form-group">
                                            <label for="numeroSerie">Ingrese numeros de series:</label>
                                            <textarea class="form-control" id="numeroSerie" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-success" onclick="guardarContado()">Guardar</button>
                                    <button class="btn btn-danger">Cancelar</button>
                                    <input type="text" class="form-control" hidden value="<?= htmlspecialchars($id_sucursal); ?>" disabled>
                                    <input type="text" class="form-control" hidden value="<?= htmlspecialchars($id_vendedor); ?>" disabled>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Vertically centered scrollable modal -->
<div class="modal fade" id="agregarProductoTemp" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="agregarProductoTempLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="agregarProductoTempLabel">Agregar productos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-4 mt-2">
                    <div class="col-sm">
                        <input type="text" class="form-control w-100" id="buscar_producto" placeholder="Buscar producto: Puede realizar la busqueda por nombre o por codigo de producto">
                    </div>
                </div>

                <div class="row">
                    <div class="col sm">
                        <b>Productos agregados: <p id="prodAgregadosCant"></p></b>
                    </div>
                </div>

                <div class="form-inline">
                    <table class="table" id="dataTableBusquedaProducto">
                        <thead>
                            <tr>
                                <th scope="col">Codigo</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Marca</th>
                                <th scope="col">Modelo</th>
                                <th scope="col">Color</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Disponible</th>
                                <th scope="col">Cantidad solicitada</th>
                                <th scope="col">Opcion</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="agregarProdASolicitud" onclick="confirmarAgregarProducto()">Agregar productos a la solicitud</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalle Solicitud -->
<div class="modal fade" id="modalDetalleSolicitud" tabindex="-1" role="dialog" aria-labelledby="tituloModalDetalleSolicitud" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tituloModalDetalleSolicitud">Detalle de la Solicitud</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Información de la solicitud -->
                <input type="text" id="detalleIdSolicitud" hidden>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Solicitud:</strong> <span id="detalleNumeroSolicitud"></span></div>
                    <div class="col-md-4"><strong>Fecha:</strong> <span id="detalleFechaSolicitud"></span></div>
                    <div class="col-md-3"><strong>Tipo:</strong> <span id="detalleTipoSolicitud"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>DUI:</strong> <span id="detalleDUI"></span></div>
                    <div class="col-md-4"><strong>Cliente:</strong> <span id="detalleNombreCliente"></span></div>
                    <div class="col-md-4"><strong>Usuario:</strong> <span id="detalleUsuarioCreador"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="numeroSerie">Detalle N° series:</label>
                            <textarea class="form-control" id="detalleNumeroSeries" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm">
                        <input type="text" class="form-control w-100" id="buscar_producto2" placeholder="Buscar producto: Puede realizar la busqueda por nombre o por codigo de producto">
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col sm">
                        <b>Productos agregados: <p id="prodAgregadosCant"></p></b>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="tablaDetalleProductos">
                        <thead class="thead-light">
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th>Opcion</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">Total a Pagar:</th>
                                <th colspan="1" style="width: 20px;">
                                    <input type="text" class="form-control" style="width: 120px;" readonly id="totalPagarDetalle" value="0.00">
                                </th>

                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="eliminarFactura()">Eliminar Factura</button>
                <button class="btn btn-primary" onclick="generarFactura()">Procesar Factura</button>
                <button class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    const idPerfil = <?= isset($id_perfil) && is_numeric($id_perfil) ? $id_perfil : 0 ?>;
</script>
<script src="<?= base_url('public/js/contado.js') ?>"></script>