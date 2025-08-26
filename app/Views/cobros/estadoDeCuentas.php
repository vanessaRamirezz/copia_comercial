<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Estado de cuentas</h1>
    <p class="mb-4">En este apartado podras obtener el estado de cuenta por No solicitud/No de contrato.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Buscar No solicitud/No de contrato</h6>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="container">
                    <div class="col-sm-6 d-flex justify-content-start">
                        <div class="form-inline my-2 my-lg-0">
                        <input class="form-control mr-sm-2 " type="text" id="noSolicitudContrato" placeholder="Ingrese el No solicitud/contrato" aria-label="Search" autocomplete="off" value="">
                        <button class="btn btn-outline-primary align-self-center" onclick="buscarEstadoDecuenta()">Buscar</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="container mt-5">
                        <div class="contenido-estado-cuenta">
                            <!-- El contenido del estado de cuenta se cargará aquí dinámicamente -->
                        </div>
                        <div id="footerBoton" class="text-center d-none mt-3">
                            <button type="button" class="btn btn-primary" onclick="imprimirEstadoCuenta()">Imprimir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/js/estadoDeCuentas.js') ?>"></script>