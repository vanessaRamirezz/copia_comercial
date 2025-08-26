<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">

  <title>Comercial TPH</title>

  <!-- Custom fonts for this template-->
  <link rel="stylesheet" href="<?= base_url('public/vendor/fontawesome-free/css/all.min.css') ?>">
  <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

  <!-- Custom styles for this template-->
  <link rel="stylesheet" href="<?= base_url('public/css/sb-admin-2.min.css') ?>">
  <script>
    var baseURL = '<?= base_url() ?>/';
  </script>
</head>

<body style="background: #e2e2e2">
  <div class="container-fluid vh-100">
    <div class="row h-100 d-flex justify-content-center align-items-center">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center">Iniciar Sesion</h3>
            <hr />
            <form>
              <div class="form-group">
                <label for="usuarioLg">Usuario</label>
                <input type="text" class="form-control" id="usuarioLg" placeholder="00000000-0">
              </div>
              <div class="form-group">
                <label for="pwdLg">Password</label>
                <input type="password" class="form-control" id="pwdLg" placeholder="Password">
              </div>
              <button type="button" class="btn btn-primary btn-block" id="btnLogin">INICIAR SESIÓN</button>

              <a href="#" class="btn btn-link" id="btnLoginRecuperarPass" data-toggle="modal" data-target="#forgotPasswordModal">
                ¿Se te olvidó tu contraseña?
              </a>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="forgotPasswordModalLabel">Recuperar Contraseña</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="form-group">
              <label for="dui" class="col-form-label">Documento unico de identidad:</label>
              <input type="text" class="form-control duiG" id="duiRecuperarPass">
            </div>
            <div class="form-group esconderInput">
              <label for="tokenRecuperacion" class="col-form-label">Ingrese Token:</label>
              <input type="text" class="form-control " id="tokenRecuperacion">
            </div>
            <div class="form-group esconderInput">
              <label for="nuevaContraseña" class="col-form-label">Nueva contraseña:</label>
              <input type="password" class="form-control " id="nuevaContraseña">
              <div class="input-group-append">
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="showPassword">
                  <label class="form-check-label" for="showPassword">Mostrar contraseña</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" id="recuperarPass">Enviar</button>
          <button type="button" class="btn btn-primary" id="updatePass" style="display:none">Actualizar contraseña</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="<?= base_url('public/vendor/jquery/jquery.min.js') ?>"></script>
  <script src="<?= base_url('public/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

  <!-- Core plugin JavaScript-->
  <script src="<?= base_url('public/vendor/jquery-easing/jquery.easing.min.js') ?>"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js" integrity="sha512-pHVGpX7F/27yZ0ISY+VVjyULApbDlD0/X0rgGbTqCE7WFW5MezNTWG/dnhtbBuICzsd0WQPgpE4REBLv+UqChw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Custom scripts for all pages-->
  <script src="<?= base_url('public/js/login.js') ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    $('.duiG').mask('00000000-0', {
      placeholder: "00000000-0"
    });
  </script>

</body>

</html>