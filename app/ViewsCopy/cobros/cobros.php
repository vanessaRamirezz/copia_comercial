<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Cobros</h1>
    <p class="mb-4">En este apartado podras realizar los cobros por clientes.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Buscar cliente</h6>
        </div>
        <div class="card-body">
            <div class="container mb-4">
                <div class="row mb-2">
                    <!-- Filtro de tipo de búsqueda -->
                    <div class="col-md-3">
                        <label for="tipoBusqueda" class="form-label">Buscar Cliente por:</label>
                        <select class="form-control" id="tipoBusqueda">
                            <option selected disabled>Seleccione filtro</option>
                            <option value="dui">DUI</option>
                            <option value="nombre">Nombre</option>
                        </select>
                    </div>

                    <!-- Campo de búsqueda -->
                    <div class="col-md-6">
                        <label for="campoBusqueda" class="form-label">Ingrese el dato:</label>
                        <input type="text" class="form-control" id="campoBusqueda" placeholder="Ingrese DUI o Nombre">
                    </div>

                    <!-- Botón separado del input -->
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="btnBuscar" style="display: none;">Buscar</button>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="container mt-5">
                        <table id="tablaSolicitudes" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No Solicitud</th>
                                    <th>DUI</th>
                                    <th>Cliente</th>
                                    <th>Fecha creación</th>
                                    <th>Cod productos</th>
                                    <th>Sucursal</th>
                                    <th>Saldo</th>
                                    <th>Estado</th>
                                    <th>Tipo</th>
                                    <th>Operaciones</th>
                                    <th style="display: none;">Prodcuto</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoSolicitudes">
                                <!-- Aquí se insertarán las solicitudes dinámicamente -->
                            </tbody>
                        </table>

                        <!-- Modal de selección de cobros -->
                        <div id="modalCobros" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCobrosLabel" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="modalCobrosLabel">Seleccionar Cobros</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div id="contenedorCodigosProductos" class="mb-3">
                                            <strong>Códigos de productos:</strong>
                                            <span id="codigosProductosTexto" class="text-primary"></span>
                                        </div>
                                        <form id="paymentSelectionForm">
                                            <input type="hidden" disabled id="cuotasCubiertas">
                                            <input type="hidden" disabled id="saldoRestanteAFavor">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <thead class="thead-dark">
                                                        <tr>
                                                            <th>Select</i></th>
                                                            <th>Número de Cuota</th>
                                                            <th>Fecha de Vencimiento</th>
                                                            <th>Fecha de Pago</th>
                                                            <th>Monto</th>
                                                            <th>Abono</th>
                                                            <th>Saldo restante</th>
                                                            <th>Estado</th>
                                                            <th style="width: 17%;">Mora generada</th>
                                                            <th style="display: none;">Id</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="cobrosList">
                                                        <!-- Aquí se agregarán las filas dinámicamente -->
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm">
                                                    <div class="alert alert-general" role="alert" style="display: none;"></div>

                                                </div>
                                            </div>
                                            <button type="button" id="btnValidarCuotas" class="btn btn-primary">Validar cobro de Cuotas</button>

                                            <button type="button" class="btn btn-primary mt-3" id="procesarCuotas" onclick="submitSelectedPayments()">Procesar Pagos Seleccionados</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalEstadoCuenta" tabindex="-1" aria-labelledby="modalEstadoCuentaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="margin: 0; max-width: 100%; height: 100vh;">

        <div class="modal-content" style="height: 100vh; display: flex; flex-direction: column; border-radius: 0;">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEstadoCuentaLabel">Estado de Cuenta</h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button> -->
            </div>

            <div class="modal-body" style="flex: 1; overflow-y: auto;">
                <div class="contenido-estado-cuenta">
                    <!-- Aquí se carga el estado de cuenta -->
                </div>
            </div>

            <div class="modal-footer justify-content-center">
                <div id="footerBoton" class="d-none">
                    <button type="button" class="btn btn-primary" onclick="imprimirEstadoCuenta()">Imprimir</button>
                </div>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>


<script src="<?= base_url('public/js/cobros.js') ?>"></script>
<script src="<?= base_url('public/js/estadoDeCuentas.js') ?>"></script>