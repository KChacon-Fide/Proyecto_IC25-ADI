<?php
session_start();
if (!empty($_SESSION['active'])) {
  header('location: src/');
  exit;
}

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

      // ** NUEVO: bloqueo si aún usa contraseña temporal **
      if ($dato['pass_temp'] == 1) {
        $_SESSION['must_change_pass'] = true;
        $_SESSION['idUser'] = $dato['id'];
        $_SESSION['nombre'] = $dato['nombre'];
        $_SESSION['correo'] = $dato['correo'];
        $_SESSION['rol'] = $dato['rol'];
        header('Location: cambiar_password.php');
        exit;
      }

      // Si ya actualizó su contraseña, sigue el flujo normal
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
      exit;
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - Sistema RSI</title>
  <link rel="icon" href="./assets/img/logo.png" type="image/png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap">
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
  <style>
    body,
    html {
      margin: 0;
      padding: 0;
      height: 100%;
      font-family: 'Inter', sans-serif;
    }

    .background {
      position: absolute;
      width: 100%;
      height: 100%;
      background: url('assets/img/Login/fondo.jpg') no-repeat center center;
      background-size: cover;
      z-index: -2;
    }

    .overlay {
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: -1;
    }

    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      padding: 15px;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      padding: 2rem;
      width: 100%;
      max-width: 400px;
      color: white;
    }

    .login-card h1 {
      text-align: center;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .login-card p {
      text-align: center;
      color: #cbd5e1;
      margin-bottom: 30px;
    }

    .login-card .form-control {
      background-color: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: white;
    }

    .login-card .form-control::placeholder {
      color: #cbd5e1;
    }

    .login-card button {
      background-color: #1E3A8A;
      border: none;
      transition: 0.3s;
    }

    .login-card button:hover {
      background-color: #172B4D;
    }

    .login-footer {
      margin-top: 2rem;
      font-size: 0.75rem;
      text-align: center;
      color: #aaa;
    }
  </style>
</head>

<body>
  <div class="background"></div>
  <div class="overlay"></div>
  <div class="login-container">
    <div class="login-card">
      <div class="text-center mb-3">
        <div class="d-inline-flex justify-content-center align-items-center bg-primary rounded-circle shadow"
          style="width: 50px; height: 50px;">
          <i class="fas fa-plus text-white"></i>
        </div>
      </div>
      <h1>SISTEMA RSI</h1>
      <p>Panel de ingreso gastronómica</p>
      <?php echo (isset($alert)) ? $alert : ''; ?>
      <form action="" method="post" autocomplete="off">
        <div class="mb-3">
          <label for="correo" class="form-label">Correo electrónico</label>
          <input type="email" class="form-control" name="correo" id="correo" placeholder="nombre@restaurante.com"
            required>
        </div>
        <div class="mb-4">
          <label for="pass" class="form-label">Contraseña</label>
          <input type="password" class="form-control" name="pass" id="pass" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Ingresar al sistema</button>
      </form>
      <div class="login-footer">
        Copyright &copy; <?php echo date('Y'); ?> Restaurante San Isidro. Todos los sabores reservados.
      </div>
    </div>
  </div>
  <script src="assets/plugins/jquery/jquery.min.js"></script>
  <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>