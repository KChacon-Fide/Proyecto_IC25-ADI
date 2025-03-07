<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
require_once "../conexion.php";
$query = mysqli_query($conexion, "SELECT * FROM config");
$data = mysqli_fetch_assoc($query);

if ($_POST) {
    $alert = '';
    if (empty($_POST['nombre']) || empty($_POST['telefono']) || empty($_POST['email']) || empty($_POST['direccion'])) {
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $nombre = $_POST['nombre'];
        $telefono = $_POST['telefono'];
        $email = $_POST['email'];
        $direccion = $_POST['direccion'];
        $id = $_POST['id'];
        $update = mysqli_query($conexion, "UPDATE config SET nombre = '$nombre', telefono = '$telefono', email = '$email', direccion = '$direccion' WHERE id = $id");
        if ($update) {
            $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Datos actualizados correctamente.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al actualizar los datos.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        }
    }
}

include_once "includes/header.php";
?>

<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-building"></i> Configuración de la Empresa</h4>
    </div>
    <div class="card-body">
        <?php echo isset($alert) ? $alert : ''; ?>
        <form action="" method="post" class="p-3">
            <div class="row">
                <input type="hidden" name="id" value="<?php echo $data['id'] ?>">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Nombre de la Empresa:</label>
                        <input type="text" name="nombre" class="form-control" value="<?php echo $data['nombre']; ?>"
                            id="txtNombre" placeholder="Nombre de la Empresa" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Teléfono:</label>
                        <input type="number" name="telefono" class="form-control"
                            value="<?php echo $data['telefono']; ?>" id="txtTelEmpresa"
                            placeholder="Teléfono de la Empresa" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Correo Electrónico:</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $data['email']; ?>"
                            id="txtEmailEmpresa" placeholder="Correo de la Empresa" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold">Dirección:</label>
                        <input type="text" name="direccion" class="form-control"
                            value="<?php echo $data['direccion']; ?>" id="txtDirEmpresa"
                            placeholder="Dirección de la Empresa" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Modificar Datos</button>
        </form>
    </div>
</div>

<?php include_once "includes/footer.php"; ?>