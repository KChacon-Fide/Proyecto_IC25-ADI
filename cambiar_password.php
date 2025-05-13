<?php
session_start();
// Si no viene de login forzado, impedir acceso
if (empty($_SESSION['must_change_pass'])) {
    header('Location: index.php');
    exit;
}
include "conexion.php";
$alert = '';
if (!empty($_POST)) {
    $temp = $_POST['temp_pass'] ?? '';
    $new  = $_POST['new_pass']  ?? '';
    $conf = $_POST['conf_pass'] ?? '';
    // Validar contraseña temporal
    $storedHash = '';
    $res = mysqli_query($conexion, "SELECT pass FROM usuarios WHERE id = " . intval($_SESSION['idUser']));
    if ($row = mysqli_fetch_assoc($res)) {
        $storedHash = $row['pass'];
    }
    if (md5($temp) !== $storedHash) {
        $alert = 'La contraseña temporal no coincide.';
    }
    // Validar requisitos
    elseif ($new === $temp) {
        $alert = 'La nueva contraseña no puede ser igual a la temporal.';
    }
    elseif (strlen($new) < 12) {
        $alert = 'La contraseña debe tener al menos 12 caracteres.';
    } elseif (!preg_match_all('/\d/', $new, $m) || count($m[0]) < 2) {
        $alert = 'La contraseña debe tener al menos 2 números.';
    } elseif (!preg_match_all('/[!@#$%^&*()_+\-=[\]{};:"\\|,.<>\/?]/', $new, $m2) || count($m2[0]) < 2) {
        $alert = 'La contraseña debe tener al menos 2 caracteres especiales.';
    } elseif (!preg_match('/[a-z]/', $new)) {
        $alert = 'La contraseña debe tener al menos una letra minúscula.';
    } elseif (!preg_match('/[A-Z]/', $new)) {
        $alert = 'La contraseña debe tener al menos una letra mayúscula.';
    } elseif ($new !== $conf) {
        $alert = 'Las contraseñas no coinciden.';
    } else {
        // Actualizar contraseña
        $hash = md5($new);
        $id   = intval($_SESSION['idUser']);
        $sql  = "UPDATE usuarios SET pass = '$hash', pass_temp = 0 WHERE id = $id";
        if (mysqli_query($conexion, $sql)) {
            unset($_SESSION['must_change_pass']);
            session_destroy();
            header('Location: index.php?changed=1');
            exit;
        } else {
            $alert = 'Error al actualizar la contraseña.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Cambiar Contraseña - Sistema RSI</title>
  <link rel="stylesheet" href="assets/plugins/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/plugins/fontawesome-free/css/all.min.css">
  <style>
    body, html { margin:0; padding:0; height:100%; font-family:'Inter',sans-serif; }
    .background { position:absolute; width:100%; height:100%; background:url('assets/img/Login/fondo.jpg') center/cover no-repeat; z-index:-2; }
    .overlay    { position:absolute; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:-1; }
    .container  { display:flex; align-items:center; justify-content:center; height:100vh; padding:15px; }
    .card-pass  {
      background:rgba(255,255,255,0.05); backdrop-filter:blur(12px);
      border:1px solid rgba(255,255,255,0.1); border-radius:1.5rem;
      box-shadow:0 8px 32px rgba(0,0,0,0.3); padding:2rem; width:100%; max-width:400px; color:#fff;
    }
    .card-pass h2 { text-align:center; color:#fff; font-weight:600; margin-bottom:1rem; }
    .form-control {
      background:rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.2); color:#fff;
    }
    .form-control::placeholder { color:#cbd5e1; }
    .divider { border-top:1px solid rgba(255,255,255,0.3); margin:1.5rem 0; }
    .btn-primary { background:#1E3A8A; border:none; }
    .btn-primary:disabled { background:#555; }
    .requirements { list-style:none; padding:0; font-size:0.875rem; }
    .requirements li { color:#e74c3c; }
    .requirements li.valid { color:#2ecc71; }
  </style>
</head>
<body>
  <div class="background"></div>
  <div class="overlay"></div>
  <div class="container">
    <div class="card-pass">
      <h2>Cambiar Contraseña</h2>
      <?php if($alert): ?>
        <div class="alert alert-warning"><?php echo $alert; ?></div>
      <?php endif; ?>
      <form method="post" id="formPass">
        <!-- Contraseña temporal actual -->
        <div class="mb-3">
          <input type="password" id="temp_pass" name="temp_pass" class="form-control" placeholder="Contraseña temporal" required>
        </div>
        <div class="divider"></div>
        <!-- Nueva contraseña y confirmación -->
        <div class="mb-3">
          <input type="password" id="new_pass" name="new_pass" class="form-control" placeholder="Nueva contraseña" required>
        </div>
        <div class="mb-3">
          <input type="password" id="conf_pass" name="conf_pass" class="form-control" placeholder="Confirmar contraseña" required>
        </div>
        <ul class="requirements mb-3">
          <li id="req-length">Mínimo 12 caracteres</li>
          <li id="req-number">Mínimo 2 números</li>
          <li id="req-special">Mínimo 2 caracteres especiales</li>
          <li id="req-lower">Al menos una minúscula</li>
          <li id="req-upper">Al menos una mayúscula</li>
        </ul>
        <button type="submit" class="btn btn-primary w-100" id="btnSubmit" disabled>Guardar contraseña</button>
      </form>
    </div>
  </div>

  <script>
    const newPass = document.getElementById('new_pass');
    const btn = document.getElementById('btnSubmit');
    const reqs = {
      length: document.getElementById('req-length'),
      number: document.getElementById('req-number'),
      special: document.getElementById('req-special'),
      lower: document.getElementById('req-lower'),
      upper: document.getElementById('req-upper')
    };

    newPass.addEventListener('input', () => {
      const val = newPass.value;
      const len = val.length >= 12;
      const num = (val.match(/\d/g) || []).length >= 2;
      const spec = (val.match(/[!@#$%^&*()_+\-=[\]{};:'"\\|,.<>\/?]/g) || []).length >= 2;
      const low = /[a-z]/.test(val);
      const up = /[A-Z]/.test(val);

      reqs.length.classList.toggle('valid', len);
      reqs.number.classList.toggle('valid', num);
      reqs.special.classList.toggle('valid', spec);
      reqs.lower.classList.toggle('valid', low);
      reqs.upper.classList.toggle('valid', up);

      btn.disabled = !(len && num && spec && low && up);
    });
  </script>
</body>
</html>
