<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Permisos por usuarios</h1>
    <p class="mb-4">Se visualizaran todos los usuarios con los permisos asignados.</p>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-sm" id="PerfilesPermisos" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-nowrap">Perfil</th>
                            <th class="text-nowrap">Permisos</th>
                            <th class="text-nowrap" style="width: 10%;">Operaciones</th>
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
<div class="modal fade" id="editarAccesosPerfil" tabindex="-1" aria-labelledby="editarAccesosPerfil" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPerfilNombre">Perfil seleccionado: </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" hidden id="id_perfil">
                <div id="permisosContainer"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="editPerfilAcceso()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('public/js/permisosPerfiles.js') ?>"></script>