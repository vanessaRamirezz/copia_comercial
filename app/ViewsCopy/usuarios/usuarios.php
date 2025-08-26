<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Mantenimiento de usuarios</h1>
    <p class="mb-4">En este mantenimiento podras ver todos los usuarios asociados a tu sucursal asignada: <?= $_SESSION['sucursalN'] ?></p>
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" id="btnAgregarUsuarioModal" data-bs-toggle="modal" data-bs-target="#mntUsuarios">Agregar Usuario</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>DUI</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Correo</th>
                            <th>Telefono</th>
                            <th>Tipo perfil</th>
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
<div class="modal fade" id="mntUsuarios" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="mntUsuariosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mntUsuariosLabel">Opciones del usuario</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="user">
                    <div class="form-group row">
                        <div class="col-sm-4 mb-3 mb-sm-0">
                            <label for="duiNew">DUI:</label>
                            <input type="text" class="form-control form-control-user dui" placeholder="________-_" id="duiNew">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label for="nombreUsuarioMtn">Nombres:</label>
                            <input type="text" class="form-control form-control-user" id="nombreUsuarioMtn">
                        </div>
                        <div class="col-sm-6">
                            <label for="apellidosUsuarioMtn">Apellidos:</label>
                            <input type="text" class="form-control form-control-user" id="apellidosUsuarioMtn">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-7 mb-3 mb-sm-0">
                            <label for="emailUsuarioMtn">Email:</label>
                            <input type="email" class="form-control form-control-user" id="emailUsuarioMtn">
                        </div>
                        <div class="col-sm-5">
                            <label for="numTelefonoMtn">Número de teléfono:</label>
                            <input type="text" class="form-control form-control-user" id="numTelefonoMtn">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-7 mb-3 mb-sm-0">
                            <label for="perfilesCmb">Perfil:</label>
                            <select class="form-control form-control-user" id="perfilesCmb">

                                <?php
                                foreach ($perfiles as $perfil) {
                                    echo '<option value="' . $perfil['id_perfil'] . '">' . $perfil['tipo_perfil'] . '</option>';
                                }

                                ?>
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <label for="perfilesCmb">Sucursal:</label>
                            <select class="form-control form-control-user" id="perfilesCmb" disabled>
                                <option value="<?= $_SESSION['sucursal'] ?>"><?= $_SESSION['sucursalN'] ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group generador-pwd">
                        <label for="passwordUsuario">Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control form-control-user" id="passwordUsuario">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    👁️
                                </button>
                                <button class="btn btn-outline-primary" type="button" id="generatePassword">
                                    Generar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cerraMntUsuarios">Cerrar</button>
                <button type="button" class="modalEditarUsuario btn btn-warning" onclick="guardarNuevoUsuario('2')" id="actualizarDatosMntUsuario">Actualizar datos</button>
                <button type="button" class="modalGuardar btn btn-primary" onclick="guardarNuevoUsuario('1')" id="guardarRegistroUsuario">Guardar registro</button>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('public/js/usuarios.js') ?>"></script>