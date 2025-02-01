<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800"><b>DATOS DEL USUARIO:</b> <?= $_SESSION['duiUsuario'] ?></h1>
    <p class="mb-4">Manten tus datos actualizados.</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hola <?= $_SESSION['nombres'] ?></h6>
        </div>
        <div class="card-body d-flex justify-content-center align-items-center">
            <div class="col-lg-7">
                <div class="user">
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label for="nombresUsuario">Nombres:</label>
                            <input type="text" class="form-control form-control-user" value="<?= $_SESSION['nombres'] ?>" id="nombresUsuario">
                        </div>
                        <div class="col-sm-6">
                            <label for="apellidosUsuario">Apellidos:</label>
                            <input type="text" class="form-control form-control-user" value="<?= $_SESSION['apellidos'] ?>" id="apellidosUsuario">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <label for="emailUsuario">Email:</label>
                            <input type="email" class="form-control form-control-user" value="<?= $_SESSION['correo'] ?>" id="emailUsuario">
                        </div>
                        <div class="col-sm-6">
                            <label for="numTelefono">Número de teléfono:</label>
                            <input type="text" class="form-control form-control-user" value="<?= $_SESSION['telefono'] ?>" id="numTelefono">
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" id="updateUsuario">GUARDAR CAMBIOS</button>
                </div>

            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->