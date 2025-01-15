<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Mantenimiento de Profesiones u Oficios</h1>
    <p class="mb-4">En este mantenimiento, podra agregar las profesiones u oficios para ser seleccionadas en las solicitud</p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#nuevaProfesion">
                Nueva profesion
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTableProfesiones" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Profesion u Oficio</th>
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
<div class="modal fade" id="nuevaProfesion" tabindex="-1" role="dialog" aria-labelledby="nuevaProfesionLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nuevaProfesionLabel">Profesion u Oficio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="txtProfesionOficio">Profesion u oficio</label>
                            <input type="text" class="form-control" value="" autocomplete="off" id="txtProfesionOficio">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="guardarProfesion()">Guardar profesion</button>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('public/js/profesion.js') ?>"></script>