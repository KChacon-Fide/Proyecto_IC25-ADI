<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
include "../conexion.php";

if (!empty($_POST)) {
    $alert = "";
    if (empty($_POST['nombre']) || empty($_POST['mesas'])) {
        $alert = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
    } else {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $mesas = $_POST['mesas'];

        if (empty($id)) {
            // Verificar si la sala ya existe
            $query = mysqli_query($conexion, "SELECT * FROM salas WHERE nombre = '$nombre' AND estado = 1");
            if (mysqli_num_rows($query) > 0) {
                $alert = '<div class="alert alert-warning">La sala ya existe.</div>';
            } else {
                // Insertar la sala en la base de datos
                $query_insert = mysqli_query($conexion, "INSERT INTO salas (nombre, mesas) VALUES ('$nombre', '$mesas')");
                
                if ($query_insert) {
                    // Obtener el ID de la sala recién creada
                    $id_sala = mysqli_insert_id($conexion);
                    
                    // Insertar las mesas asociadas a la sala
                    for ($i = 1; $i <= $mesas; $i++) {
                        mysqli_query($conexion, "INSERT INTO mesas (id_sala, num_mesa, capacidad, estado) VALUES ($id_sala, $i, 4, 'DISPONIBLE')");
                    }

                    $alert = '<div class="alert alert-success">Sala registrada correctamente.</div>';
                } else {
                    $alert = '<div class="alert alert-danger">Error al registrar la sala.</div>';
                }
            }
        } else {
            // Actualizar la sala
            $sql_update = mysqli_query($conexion, "UPDATE salas SET nombre = '$nombre', mesas = '$mesas' WHERE id = $id");
            if ($sql_update) {
                // Eliminar mesas antiguas
                mysqli_query($conexion, "DELETE FROM mesas WHERE id_sala = $id");

                // Insertar las nuevas mesas
                for ($i = 1; $i <= $mesas; $i++) {
                    mysqli_query($conexion, "INSERT INTO mesas (id_sala, num_mesa, capacidad, estado) VALUES ($id, $i, 4, 'DISPONIBLE')");
                }

                $alert = '<div class="alert alert-success">Sala y mesas actualizadas correctamente.</div>';
            } else {
                $alert = '<div class="alert alert-danger">Error al modificar la sala.</div>';
            }
        }
    }
}

include_once "includes/header.php";
?>

<div class="card shadow-lg">
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0"><i class="fas fa-door-open"></i> Gestión de Salas</h4>
    </div>
    <div class="card-body">
        <form action="" method="post">
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <input type="hidden" id="id" name="id">
                        <label for="nombre" class="font-weight-bold">Nombre de la Sala</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingrese nombre">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="mesas" class="font-weight-bold">Cantidad de Mesas</label>
                        <input type="number" name="mesas" id="mesas" class="form-control" placeholder="Número de mesas">
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <label for="">Acciones</label> <br>
                    <input type="submit" value="Registrar" class="btn btn-primary">
                    <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-lg">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="border: 0.5px solid #2C3E50;">
                <thead style="background-color: #2C3E50; color: white;">
                    <tr>
                        <th style="border: 0.5px solid #2C3E50;">#</th>
                        <th style="border: 0.5px solid #2C3E50;">Nombre</th>
                        <th style="border: 0.5px solid #2C3E50;">Mesas</th>
                        <th style="border: 0.5px solid #2C3E50;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conexion, "SELECT * FROM salas WHERE estado = 1");
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td style="border: 0.5px solid #2C3E50;"><?php echo $data['id']; ?></td>
                            <td class="font-weight-bold" style="border: 0.5px solid #2C3E50;"><?php echo strtoupper($data['nombre']); ?></td>
                            <td class="text-success font-weight-bold" style="border: 0.5px solid #2C3E50;"><?php echo $data['mesas']; ?></td>
                            <td style="border: 0.5px solid #2C3E50;">
                                <a href="#" onclick="editarSala(<?php echo $data['id']; ?>, '<?php echo $data['nombre']; ?>', '<?php echo $data['mesas']; ?>')" class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=salas" method="post" class="d-inline">
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

<script>
    function editarSala(id, nombre, mesas) {
        document.getElementById("id").value = id;
        document.getElementById("nombre").value = nombre;
        document.getElementById("mesas").value = mesas;
    }

    function limpiar() {
        document.getElementById("id").value = '';
        document.getElementById("nombre").value = '';
        document.getElementById("mesas").value = '';
    }
</script>

<?php include_once "includes/footer.php"; ?>
