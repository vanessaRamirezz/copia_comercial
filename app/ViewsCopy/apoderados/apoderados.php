<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Mantenimiento de apoderados</h1>
    <p class="mb-4">En este mantenimiento podras ver, editar y agregar un nuevo apoderado</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" id="btnAgregarApoderadoModal" data-bs-toggle="modal" onclick="openModal(1)" data-bs-target="#mntApoderados">Agregar Apoderado</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre apoderado</th>
                            <th>DUI apoderado</th>
                            <th>Nombre representante legal</th>
                            <th>DUI representante legal</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="mntApoderados" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="mntApoderadosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mntApoderadosLabel">Opciones del apoderado</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user">
                    <div class="form-group row d-flex align-items-end">
                        <div class="col-sm mb-3 mb-sm-0">
                            <label for="duiApoderado">DUI apoderado:</label>
                            <input type="text" class="form-control form-control-user duiG" id="duiApoderado">
                        </div>
                        <div class="col-sm mb-3 mb-sm-0">
                            <label for="fecha_nacimiento_apoderado">Fecha de nacimiento apoderado:</label>
                            <input type="date" class="form-control form-control-user" id="fecha_nacimiento_apoderado">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 mb-3 mb-sm-0">
                            <label for="nombre_apoderado">Nombre apoderado:</label>
                            <input type="text" class="form-control form-control-user" id="nombre_apoderado">
                        </div>
                    </div>
                    <hr>
                    <div class="form-group row d-flex align-items-end">
                        <div class="col-sm mb-3 mb-sm-0">
                            <label for="dui_representante_legal">DUI representante legal:</label>
                            <input type="text" class="form-control form-control-user duiG" id="dui_representante_legal">
                        </div>
                        <div class="col-sm mb-3 mb-sm-0">
                            <label for="fecha_nacimiento_rLegal">Fecha de nacimiento representante legal:</label>
                            <input type="date" class="form-control form-control-user" id="fecha_nacimiento_rLegal">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 mb-3 mb-sm-0">
                            <label for="nombre_representante_legal">Nombre representante legal:</label>
                            <input type="text" class="form-control form-control-user" id="nombre_representante_legal">
                        </div>
                    </div>
                    <input type="text" hidden disabled id="idapoderado">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerraModal()">Cerrar</button>
                <button type="button" class="modalEditarApoderado btn btn-warning" onclick="generateOptions('2')" id="actualizarDatosMntApoderado">Actualizar datos</button>
                <button type="button" class="modalGuardar btn btn-primary" onclick="generateOptions('1')" id="guardarRegistroApoderado">Guardar registro</button>
            </div>
        </div>
    </div>
</div>
<style>
    .dataTables_filter {
        display: none;
    }
</style>
<script src="<?= base_url('public/js/apoderados.js') ?>"></script>