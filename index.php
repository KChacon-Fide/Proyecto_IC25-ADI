<?php
session_start();
if (!empty($_SESSION['active'])) {
  header('location: src/');
} else {
  if (!empty($_POST)) {
    $alert = '';
    if (empty($_POST['correo']) || empty($_POST['pass'])) {
      $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Ingrese correo y contraseña
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
      require_once "conexion.php";
      $user = $_POST['correo'];
      $pass = md5($_POST['pass']);

      $stmt = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ? AND pass = ? AND estado = 1");
      $stmt->bind_param("ss", $user, $pass);
      $stmt->execute();
      $query = $stmt->get_result();
      mysqli_close($conexion);

      $resultado = $query->num_rows;
      if ($resultado > 0) {
        $dato = $query->fetch_array();
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $dato['id'];
        $_SESSION['nombre'] = $dato['nombre'];
        $_SESSION['correo'] = $dato['correo'];
        $_SESSION['rol'] = $dato['rol'];
        if ($_SESSION['rol'] == 3) {
          header('Location: src/index.php');
        } else {
          header('Location: src/dashboard.php');
        }
      } else {
        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            Usuario o contraseña incorrectos
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
        session_destroy();
      }
    }
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="assets/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="assets/dist/css/adminlte.min.css">
  <link rel="icon" href="./assets/img/logo.png" type="image/png">
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="#"><b>SISTEMA </b>RSI</a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Inicia sesión para ingresar</p>

        <form action="" method="post" autocomplete="off">
          <?php echo (isset($alert)) ? $alert : ''; ?>
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="correo" placeholder="CORREO">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="pass" placeholder="CONTRASEÑA">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block">INICIAR </button>
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="assets/dist/js/adminlte.min.js"></script>
</body>

</html>