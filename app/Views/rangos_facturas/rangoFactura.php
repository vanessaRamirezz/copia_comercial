<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Rangos de facturas</h1>
    <p class="mb-4">En este apartado podras agregar rango para los numeros de factura.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Rango de facturas por sucursal</h6>
        </div>
        <div class="card-body">
            <div class="row mb-2">
                <div class="container">
                    <div class="col-sm-6 d-flex justify-content-start">
                        <div class="form-inline my-2 my-lg-0">
                            <button class="btn btn-outline-primary my-2 my-sm-0" onclick="agregarRango()">Agregar rango</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="container mt-5">
                        <table id="tblRangoFacturas" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Numero de inicio</th>
                                    <th>Numero final</th>
                                    <th>Sucursal</th>
                                    <th>Usuario creador</th>
                                    <th>Creacion</th>
                                </tr>
                            </thead>
                            <tbody>
        <!-- Aquí se llenarán las filas dinámicamente con los datos de AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var usuarioNombre = "<?= session('nombres') . ' ' . session('apellidos') ?>";
    var usuarioID = "<?= session('id_usuario') ?>";
</script>
<script src="<?= base_url('public/js/rangoFacturas.js') ?>"></script>
