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
    $query = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $idUsuario AND estado = 1");
    $result = mysqli_num_rows($query);
    if ($result > 0) {
        $data = mysqli_fetch_assoc($query);
    } else {
        header('Location: usuarios.php');
        exit;
    }
}
if (!empty($_POST)) {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];
    $turno = $_POST['turno'];
    $alert = "";

    if (empty($nombre) || empty($correo) || empty($rol) || empty($turno)) {
        $alert = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
    } else {
        if (empty($id)) {

            $pass = $_POST['pass'];
            if (empty($pass)) {
                $alert = '<div class="alert alert-warning">La contraseña es requerida.</div>';
            } else {
                $pass = md5($pass);
                $query = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo' AND estado = 1");
                if (mysqli_num_rows($query) > 0) {
                    $alert = '<div class="alert alert-warning">El correo ya existe.</div>';
                } else {
                    $query_insert = mysqli_query($conexion, "INSERT INTO usuarios (nombre, correo, rol, pass, turno) VALUES ('$nombre', '$correo', '$rol', '$pass', '$turno')");
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success">Usuario registrado exitosamente.</div>';
                    } else {
                        $alert = '<div class="alert alert-danger">Error al registrar el usuario.</div>';
                    }
                }
            }
        } else {
            $pass = $_POST['pass'] ?? null;
            if (!empty($pass)) {
                $pass = md5($pass);
                $sql_update = mysqli_query($conexion, "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', rol = '$rol', turno = '$turno', pass = '$pass' WHERE id = $id");
            } else {
                $sql_update = mysqli_query($conexion, "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', rol = '$rol', turno = '$turno' WHERE id = $id");
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
                <?php if (empty($data['id'])) { ?>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pass" class="font-weight-bold">Contraseña</label>
                            <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="pass"
                                id="pass">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <input type="submit" value="<?php echo empty($data['id']) ? 'Registrar' : 'Modificar'; ?>"
                class="btn btn-primary" style="background-color: #1E3A8A;">
            <a href="usuarios.php" class="btn btn-danger"> <i class="fas fa-trash-alt"></i>
            </a>
        </form>
    </div>
</div>

<div class="card shadow-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="border: 2px solid #1E3A8A;">
                <thead style="background-color: #1E3A8A; color: white;">
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
<?php include_once "includes/footer.php"; ?>