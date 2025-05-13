<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
include "../conexion.php";

$data = null;
if (!empty($_GET['id'])) {
    $idUsuario = $_GET['id'];
    $query     = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $idUsuario AND estado = 1");
    $result    = mysqli_num_rows($query);
    if ($result > 0) {
        $data = mysqli_fetch_assoc($query);
    } else {
        header('Location: usuarios.php');
        exit;
    }
}

if (!empty($_POST)) {
    $id     = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol    = $_POST['rol'];
    $turno  = $_POST['turno'];
    $alert  = "";

    if (empty($nombre) || empty($correo) || empty($rol) || empty($turno)) {
        $alert = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
    } else {
        // NUEVO USUARIO
        if (empty($id)) {
            $pass = $_POST['pass'];
            if (empty($pass)) {
                $alert = '<div class="alert alert-warning">La contraseña es requerida.</div>';
            } else {
                $hash = md5($pass);
                $q    = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo' AND estado = 1");
                if (mysqli_num_rows($q) > 0) {
                    $alert = '<div class="alert alert-warning">El correo ya existe.</div>';
                } else {
                    $query_insert = mysqli_query($conexion,
                        "INSERT INTO usuarios (nombre, correo, rol, pass, turno, pass_temp)
                         VALUES ('$nombre', '$correo', '$rol', '$hash', '$turno', 1)"
                    );
                    if ($query_insert) {
                        require_once 'correo/Mail-Sent.php';
                        enviarCorreoBienvenida($correo, $nombre, $pass);
                        $alert = '<div class="alert alert-success">Usuario registrado exitosamente y correo enviado.</div>';
                    } else {
                        $alert = '<div class="alert alert-danger">Error al registrar el usuario.</div>';
                    }
                }
            }
        }
        // ACTUALIZAR USUARIO
        else {
            $pass = $_POST['pass'] ?? null;
            if (!empty($pass)) {
                $hash = md5($pass);
                // Marcamos contraseña como temporal y reenviamos correo
                $sql_update = mysqli_query($conexion,
                    "UPDATE usuarios
                     SET nombre = '$nombre',
                         correo = '$correo',
                         rol    = '$rol',
                         turno  = '$turno',
                         pass   = '$hash',
                         pass_temp = 1
                     WHERE id = $id"
                );
                if ($sql_update) {
                    require_once 'correo/Mail-Sent.php';
                    enviarCorreoBienvenida($correo, $nombre, $pass);
                }
            } else {
                $sql_update = mysqli_query($conexion,
                    "UPDATE usuarios
                     SET nombre = '$nombre',
                         correo = '$correo',
                         rol    = '$rol',
                         turno  = '$turno'
                     WHERE id = $id"
                );
            }

            if ($sql_update) {
                $alert = '<div class="alert alert-success">Usuario modificado exitosamente.</div>';
            } else {
                $alert = '<div class="alert alert-danger">Error al modificar el usuario.</div>';
            }
        }
    }
}
include "includes/header.php";
?>

<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-user"></i> Gestión de Usuarios</h4>
    </div>
    <div class="card-body">
        <form action="" method="post" autocomplete="off">
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <input type="hidden" name="id" value="<?php echo $data['id'] ?? ''; ?>">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nombre" class="font-weight-bold">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre"
                            value="<?php echo $data['nombre'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="correo" class="font-weight-bold">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese correo Electrónico" name="correo"
                            id="correo" value="<?php echo $data['correo'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rol" class="font-weight-bold">Rol</label>
                        <select id="rol" class="form-control" name="rol">
                            <option value="1" <?php echo (isset($data['rol']) && $data['rol'] == 1) ? 'selected' : ''; ?>>
                                Administrador</option>
                            <option value="2" <?php echo (isset($data['rol']) && $data['rol'] == 2) ? 'selected' : ''; ?>>
                                Cocinero</option>
                            <option value="3" <?php echo (isset($data['rol']) && $data['rol'] == 3) ? 'selected' : ''; ?>>
                                Mesero</option>
                            <option value="4" <?php echo (isset($data['rol']) && $data['rol'] == 4) ? 'selected' : ''; ?>>
                                Bartender</option>
                            <option value="5" <?php echo (isset($data['rol']) && $data['rol'] == 5) ? 'selected' : ''; ?>>
                                Cajero</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="turno" class="font-weight-bold">Turno</label>
                        <select id="turno" class="form-control" name="turno">
                            <option value="diurno" <?php echo (isset($data['turno']) && $data['turno'] == 'diurno') ? 'selected' : ''; ?>>Diurno</option>
                            <option value="nocturno" <?php echo (isset($data['turno']) && $data['turno'] == 'nocturno') ? 'selected' : ''; ?>>Nocturno</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pass" class="font-weight-bold">Contraseña</label>
                        <div class="input-group">
                            <button class="btn btn-primary" type="button" onclick="generarPassword()"
                                style="margin-right: 4px; background-color: #1E3A8A;">
                                <i class="fas fa-random"></i>
                            </button>
                            <input type="password" class="form-control px-2" placeholder="Ingrese Contraseña"
                                name="pass" id="pass">
                            <button class="btn btn-primary" type="button" onclick="togglePassword()"
                                style="margin-left: 4px;">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex mt-3" style="gap: 10px;">
                        <input type="submit" id="btnRegistrar"
                            value="<?php echo empty($data['id']) ? 'Registrar' : 'Modificar'; ?>"
                            class="btn btn-primary" style="background-color: #1E3A8A; display: none;">
                        <a href="usuarios.php" class="btn btn-danger"> <i class="fas fa-trash-alt"></i></a>
                    </div>

                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="d-block font-weight-bold" style="visibility: hidden;">Validaciones</label>
                        <ul id="password-requisitos" class="small mb-0 pl-3" style="margin-top: 0.5px;">
                            <li id="longitud" class="text-danger">Mínimo 12 caracteres</li>
                            <li id="minuscula" class="text-danger">Una letra minúscula (a-z)</li>
                            <li id="mayuscula" class="text-danger">Una letra mayúscula (A-Z)</li>
                            <li id="numero" class="text-danger">Un número (0-9)</li>
                            <li id="especial" class="text-danger">Un carácter especial (!@#$...)</li>
                        </ul>
                    </div>
                </div>


            </div>

        </form>
    </div>
</div>

<div class="card shadow-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead style="background-color: #1E3A8A; color: white;border: 2px solid #1E3A8A;">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Turno</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conexion, "SELECT * FROM usuarios WHERE estado = 1");
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?php echo $data['id']; ?></td>
                            <td><?php echo $data['nombre']; ?></td>
                            <td><?php echo $data['correo']; ?></td>
                            <td><?php echo ucfirst($data['turno']); ?></td>
                            <td><?php echo ucfirst($data['rol']); ?></td>
                            <td>
                                <a href="usuarios.php?id=<?php echo $data['id']; ?>" class="btn btn-warning"><i
                                        class="fas fa-edit"></i></a>
                                <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=usuarios" method="post"
                                    class="d-inline">
                                    <button class="btn btn-danger" type="submit">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
    .table tbody {
        background-color: rgba(77, 100, 165, 0.1);
    }

    .table th {
        border: 0.5px solid #1E3A8A;
    }

    .table tbody tr:hover {
        background: rgba(30, 58, 138, 0.1);
    }

    .table td {
        font-size: 14px;
        border: none;
    }
</style>
<script>
    function generarContrasenaSegura(longitud = 16) {
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+{}[]:;<>,.?~';
        let contrasena = '';
        while (true) {
            contrasena = Array.from({ length: longitud }, () =>
                caracteres.charAt(Math.floor(Math.random() * caracteres.length))
            ).join('');

            if (validarTodosLosRequisitos(contrasena)) {
                break;
            }
        }
        return contrasena;
    }

    function togglePassword() {
        const passField = document.getElementById("pass");
        const icon = document.getElementById("toggleIcon");
        if (passField.type === "password") {
            passField.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passField.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    function generarPassword() {
        const passField = document.getElementById("pass");
        if (passField) {
            const nueva = generarContrasenaSegura();
            passField.value = nueva;
            validarRequisitos(nueva);
            actualizarVisibilidadBoton(nueva, true);
        }
    }

    // Requisitos visuales en la lista
    const requisitos = {
        longitud: document.getElementById("longitud"),
        minuscula: document.getElementById("minuscula"),
        mayuscula: document.getElementById("mayuscula"),
        numero: document.getElementById("numero"),
        especial: document.getElementById("especial")
    };

    function validarRequisitos(password) {
        requisitos.longitud.classList.toggle("text-success", password.length >= 12);
        requisitos.minuscula.classList.toggle("text-success", /[a-z]/.test(password));
        requisitos.mayuscula.classList.toggle("text-success", /[A-Z]/.test(password));
        requisitos.numero.classList.toggle("text-success", /\d/.test(password));
        requisitos.especial.classList.toggle("text-success", /[!@#$%^&*()_+{}\[\]:;<>,.?~]/.test(password));

        for (let key in requisitos) {
            if (requisitos[key].classList.contains("text-success")) {
                requisitos[key].classList.remove("text-danger");
            } else {
                requisitos[key].classList.add("text-danger");
            }
        }
    }

    function validarTodosLosRequisitos(password) {
        return (
            password.length >= 12 &&
            /[a-z]/.test(password) &&
            /[A-Z]/.test(password) &&
            /\d/.test(password) &&
            /[!@#$%^&*()_+{}\[\]:;<>,.?~]/.test(password)
        );
    }

    function actualizarVisibilidadBoton(password, generadoAutomaticamente = false) {
        const boton = document.getElementById("btnRegistrar");

        if (!boton) return;

        if (generadoAutomaticamente) {
            boton.style.display = "inline-block";
            return;
        }

        if (validarTodosLosRequisitos(password)) {
            boton.style.display = "inline-block";
        } else {
            boton.style.display = "none";
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        const passField = document.getElementById("pass");
        const boton = document.getElementById("btnRegistrar");

        if (passField) {
            validarRequisitos(passField.value);

            // Mostrar el botón si ya tiene valor (modo edición)
            if (passField.value !== "") {
                boton.style.display = "inline-block";
            } else {
                boton.style.display = "none";
            }

            // Validar en cada cambio
            passField.addEventListener("input", function () {
                validarRequisitos(this.value);
                actualizarVisibilidadBoton(this.value, false);
            });
        }
    });
</script>



<?php include_once "includes/footer.php"; ?>