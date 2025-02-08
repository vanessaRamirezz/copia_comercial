<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Cobros</h1>
    <p class="mb-4">En este apartado podras realizar los cobros por clientes.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Buscar cliente</h6>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="container">
                    <div class="col-sm-6 d-flex justify-content-start">
                        <div class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2 duiG" type="text" id="duiBuscarCliente" placeholder="Ingrese el DUI a buscar" aria-label="Search" autocomplete="off" value="">
                            <button class="btn btn-outline-primary my-2 my-sm-0" onclick="buscarClienteDeudas()">Buscar cliente</button>
                        </div>
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
                                    <th>Estado</th>
                                    <th>Usuario creador</th>
                                    <th>Monto a pagar</th>
                                    <th>Operaciones</th>
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
                                            <div class="row"><div class="col-sm">
                                            <div class="alert alert-general" role="alert" style="display: none;"></div>

                                            </div></div>
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
<script src="<?= base_url('public/js/cobros.js') ?>"></script>