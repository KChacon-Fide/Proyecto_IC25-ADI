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
            $query = mysqli_query($conexion, "SELECT * FROM salas WHERE nombre = '$nombre' AND estado = 1");
            if (mysqli_num_rows($query) > 0) {
                $alert = '<div class="alert alert-warning">La sala ya existe.</div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO salas (nombre, mesas) VALUES ('$nombre', '$mesas')");

                if ($query_insert) {
                    $id_sala = mysqli_insert_id($conexion);

                    for ($i = 1; $i <= $mesas; $i++) {
                        mysqli_query($conexion, "INSERT INTO mesas (id_sala, num_mesa, capacidad, estado) VALUES ($id_sala, $i, 4, 'DISPONIBLE')");
                    }
                    $alert = '<div class="alert alert-success">Sala registrada correctamente.</div>';
                } else {
                    $alert = '<div class="alert alert-danger">Error al registrar la sala.</div>';
                }
            }
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE salas SET nombre = '$nombre', mesas = '$mesas' WHERE id = $id");
            if ($sql_update) {
                mysqli_query($conexion, "DELETE FROM mesas WHERE id_sala = $id");

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
    <div class="card-body text-center">
        <form action="" method="post" >
            <?php echo isset($alert) ? $alert : ''; ?>
            <div class="row">
                <div class="col-md-2 ">
                    <div class="form-group">
                        <input type="hidden" id="id" name="id">
                        <label for="nombre" class="font-weight-bold">Nombre de la Sala</label>
                        <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingrese nombre">
                    </div>
                </div>
                <div class="col-md-2 ">
                    <div class="form-group">
                        <label for="mesas" class="font-weight-bold">Cantidad de Mesas</label>
                        <input type="number" name="mesas" id="mesas" class="form-control" placeholder="Número de mesas">
                    </div>
                </div>
                <div class="col-md-2 ">
                    <label for="">Acciones</label> <br>
                    <input type="submit" value="Registrar" class="btn btn-primary" style="background-color: #1E3A8A;">
                    
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card shadow-lg">
    <div class="card-body" style="max-height: 600px; overflow-y: auto;">
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="border: 0.5px solid #1E3A8A;">
                <thead style="background-color: #1E3A8A; color: white;">
                    <tr>
                       
                        <th style="border: 0.5px solid #1E3A8A;">Nombre</th>
                        <th style="border: 0.5px solid #1E3A8A;">Mesas</th>
                        <th style="border: 0.5px solid #1E3A8A;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conexion, "SELECT * FROM salas WHERE estado = 1");
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            
                            <td class="font-weight-bold" >
                                <?php echo strtoupper($data['nombre']); ?>
                            </td>
                            <td class=" font-weight-bold" >
                                <?php echo $data['mesas']; ?>
                            </td>
                            <td >
                                <a href="#"
                                    onclick="editarSala(<?php echo $data['id']; ?>, '<?php echo $data['nombre']; ?>', '<?php echo $data['mesas']; ?>')"
                                    class="btn btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=salas" method="post"
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
        .table tbody  {
            background-color:  rgba(77, 100, 165, 0.1);
            

        }
        .table th{
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