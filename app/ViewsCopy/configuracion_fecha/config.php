<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Configuración de Fecha</h1>
    <p class="mb-4">Este mantenimiento te permite establecer una fecha global para todo el sistema. Podrás adelantar solo un día respecto a la fecha actual. Una vez que ese día pase, el sistema volverá automáticamente a usar la fecha actual real.</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" id="nuevaFechaBtn" data-toggle="modal" data-target="#nuevaFechaModal">
                Agregar fecha
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableFechas" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Fecha Global</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal para nueva fecha -->
<div class="modal fade" id="nuevaFechaModal" tabindex="-1" role="dialog" aria-labelledby="nuevaFechaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <!-- <form id="formNuevaFecha"> -->
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar Nueva Fecha</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="fecha_virtual">Selecciona la fecha:</label>
                    <input type="date" class="form-control" id="fecha_virtual" name="fecha_virtual" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="btnGuardarFecha">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        <!-- </form> -->
    </div>
</div>

<script src="<?= base_url('public/js/configFecha.js') ?>"></script>