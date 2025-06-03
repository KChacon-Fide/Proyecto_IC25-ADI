<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// En producción apuntamos directo al src
$base = __DIR__; // /home/.../public_html/src

// Incluimos primero el envío de correo
$base = dirname(__DIR__);

// Clases de PHPMailer
require_once $base . '/mailer/PHPMailer.php';
require_once $base . '/mailer/SMTP.php';
require_once $base . '/mailer/Exception.php';

function enviarCorreoBienvenida($correoDestino, $nombreUsuario, $contrasenaTemporal)
{
  $mail = new PHPMailer(true);
  try {
    // Sin debug en producción
    $mail->SMTPDebug = 0;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'restaurantesanisidro7@gmail.com';
    $mail->Password = 'kitowcaddldjmjkv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Remitente
    $mail->setFrom('restaurantesanisidro7@gmail.com', 'Sistema RSI');
    $mail->addAddress($correoDestino, $nombreUsuario);

    // Incrustar imágenes (CID)
    global $base;
    $logoPath = dirname($base) . '/assets/img/logo.png';
    $perfilPath = dirname($base) . '/assets/img/logo.png';
    $mail->addEmbeddedImage($logoPath, 'logo_cid');
    $mail->addEmbeddedImage($perfilPath, 'perfil_cid');

    // Contenido
    $mail->isHTML(true);
    $mail->Subject = 'Bienvenido a Restaurante San Isidro';
    $mail->Body = '
        <div style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px;margin:0">
          <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="center">
                <!-- Tarjeta -->
                <div style="max-width:600px;background:#ffffff;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);overflow:hidden">
                  
                  <!-- Cabecera con logo -->
                  <div style="background:#1E3A8A;padding:15px;text-align:center">
                    <img src="cid:logo_cid" width="120" alt="Logo RSI" style="display:block;margin:0 auto">
                  </div>
                  
                  <!-- Cuerpo -->
                  <div style="padding:30px;text-align:center;color:#333">
                    
                    <!-- Imagen de perfil circular -->
                    <img src="cid:perfil_cid" width="80" height="80" style="border-radius:50%;margin-bottom:15px" alt="RSI">

                    <h2 style="margin:0 0 15px;color:#1E3A8A;font-size:24px">
                      ¡Hola ' . htmlspecialchars($nombreUsuario) . '!
                    </h2>
                    <p style="font-size:16px;line-height:1.5;margin:0 0 20px">
                      Te damos la bienvenida al sistema de administración del <strong>Restaurante San Isidro</strong>.
                    </p>
                    <p style="font-size:18px;margin:0 0 30px">
                      Tu contraseña temporal es:<br>
                      <strong style="background:#eef2ff;padding:5px 10px;border-radius:5px">
                        ' . htmlspecialchars($contrasenaTemporal) . '
                      </strong>
                    </p>

                    <!-- Botón de Acción -->
                    <a href="https://restaurantesanisidrocr.com/" 
                       style="display:inline-block;padding:12px 25px;background:#1E3A8A;color:#fff;text-decoration:none;border-radius:5px;font-weight:bold">
                      Cambiar mi contraseña
                    </a>

                  </div>
                  
                  <!-- Pie de página -->
                  <div style="background:#f0f4f8;padding:15px;text-align:center;color:#666;font-size:12px">
                    © ' . date('Y') . ' Restaurante San Isidro. Todos los derechos reservados.
                  </div>
                </div>
              </td>
            </tr>
          </table>
        </div>';

    $mail->send();
    return true;
  } catch (Exception $e) {
    error_log("Mailer Error: {$mail->ErrorInfo}");
    return false;
  }
}
