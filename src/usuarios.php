<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
include "../conexion.php";

// Recuperar datos del usuario para edición
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
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    Todo los campos son obligatorio
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
    } else {
        if (empty($id)) {
            $pass = $_POST['pass'];
            if (empty($pass)) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    La contraseña es requerido
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            } else {
                $pass = md5($_POST['pass']);
                $query = mysqli_query($conexion, "SELECT * FROM usuarios WHERE correo = '$correo' AND estado = 1");
                $result = mysqli_fetch_array($query);
                if ($result > 0) {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    El correo ya existe
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                } else {
                    $query_insert = mysqli_query($conexion, "INSERT INTO usuarios (nombre, correo, rol, pass, turno) VALUES ('$nombre', '$correo', '$rol', '$pass', '$turno')");
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Usuario Registrado
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                    } else {
                        $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error al registrar
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
                    }
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE usuarios SET nombre = '$nombre', correo = '$correo', rol = '$rol', turno = '$turno' WHERE id = $id");
            if ($sql_update) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    Usuario Modificado
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error al modificar
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }
        }
    }
}
include "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <form action="" method="post" autocomplete="off" id="formulario">
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <input type="hidden" name="id" value="<?php echo $data['id'] ?? ''; ?>">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" placeholder="Ingrese Nombre" name="nombre" id="nombre"
                            value="<?php echo $data['nombre'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="correo">Correo</label>
                        <input type="email" class="form-control" placeholder="Ingrese correo Electrónico" name="correo"
                            id="correo" value="<?php echo $data['correo'] ?? ''; ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rol">Rol</label>
                        <select id="rol" class="form-control" name="rol">
                            <option value="1" <?php echo (isset($data['rol']) && $data['rol'] == 1) ? 'selected' : ''; ?>>
                                Administrador</option>
                            <option value="2" <?php echo (isset($data['rol']) && $data['rol'] == 2) ? 'selected' : ''; ?>>
                                Cocinero</option>
                            <option value="3" <?php echo (isset($data['rol']) && $data['rol'] == 3) ? 'selected' : ''; ?>>
                                Mozo</option>
                            <option value="4" <?php echo (isset($data['rol']) && $data['rol'] == 4) ? 'selected' : ''; ?>>
                                Bartender</option>
                            <option value="5" <?php echo (isset($data['rol']) && $data['rol'] == 5) ? 'selected' : ''; ?>>
                                Cajero</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="turno">Turno</label>
                        <select id="turno" class="form-control" name="turno">
                            <option value="diurno" <?php echo (isset($data['turno']) && $data['turno'] == 'diurno') ? 'selected' : ''; ?>>Diurno</option>
                            <option value="nocturno" <?php echo (isset($data['turno']) && $data['turno'] == 'nocturno') ? 'selected' : ''; ?>>Nocturno</option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="submit" value="<?php echo empty($data['id']) ? 'Registrar' : 'Modificar'; ?>"
                class="btn btn-primary">
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tbl">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Turno</th>
                <th>Rol</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query = mysqli_query($conexion, "SELECT * FROM usuarios WHERE estado = 1");
            $result = mysqli_num_rows($query);
            if ($result > 0) {
                while ($data = mysqli_fetch_assoc($query)) {
                    if ($data['rol'] == 1) {
                        $rol = '<span class="badge badge-success">Administrador</span>';
                    } elseif ($data['rol'] == 2) {
                        $rol = '<span class="badge badge-info">Cocinero</span>';
                    } elseif ($data['rol'] == 3) {
                        $rol = '<span class="badge badge-warning">Mozo</span>';
                    } elseif ($data['rol'] == 4) {
                        $rol = '<span class="badge badge-primary">Bartender</span>';
                    } elseif ($data['rol'] == 5) {
                        $rol = '<span class="badge badge-secondary">Cajero</span>';
                    }
                    ?>
                    <tr>
                        <td><?php echo $data['id']; ?></td>
                        <td><?php echo $data['nombre']; ?></td>
                        <td><?php echo $data['correo']; ?></td>
                        <td><?php echo $data['turno']; ?></td>
                        <td><?php echo $rol; ?></td>
                        <td>
                            <a href="usuarios.php?id=<?php echo $data['id']; ?>" class="btn btn-success"><i
                                    class="fas fa-edit"></i></a>
                            <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=usuarios" method="post"
                                class="confirmar d-inline">
                                <button class="btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php }
            } ?>
        </tbody>
    </table>
</div>
<?php include_once "includes/footer.php"; ?>