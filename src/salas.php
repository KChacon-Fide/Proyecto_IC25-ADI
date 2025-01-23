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
        $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todo los campos son obligatorio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
    } else {
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $mesas = $_POST['mesas'];
        $result = 0;
        if (empty($id)) {
            $query = mysqli_query($conexion, "SELECT * FROM salas WHERE nombre = '$nombre' AND estado = 1");
            $result = mysqli_fetch_array($query);
            if ($result > 0) {
                $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        La sala ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
            } else {
                $query_insert = mysqli_query($conexion, "INSERT INTO salas (nombre,mesas) values ('$nombre', '$mesas')");
                if ($query_insert) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Sala registrado
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
        } else {
            $sql_update = mysqli_query($conexion, "UPDATE salas SET nombre = '$nombre' , mesas = '$mesas' WHERE id = $id");
            if ($sql_update) {
                // Eliminar mesas existentes para esta sala
                $delete_mesas = mysqli_query($conexion, "DELETE FROM mesas WHERE id_sala = $id");

                if ($delete_mesas) {
                    // Insertar las nuevas mesas
                    for ($i = 1; $i <= $mesas; $i++) {
                        $insert_mesa = mysqli_query($conexion, "INSERT INTO mesas (id_sala, num_mesa, capacidad, estado) VALUES ($id, $i, 4, 'DISPONIBLE')");
                        if (!$insert_mesa) {
                            $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Error al actualizar las mesas.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>';
                            break;
                        }
                    }
                    if (!isset($alert)) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Sala y mesas actualizadas correctamente.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
                    }
                } else {
                    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Error al eliminar las mesas existentes.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            } else {
                $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Error al modificar la sala.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            }

        }
    }
    mysqli_close($conexion);
}
include_once "includes/header.php";
?>
<div class="card">
    <div class="card-body">
        <div class="card">
            <div class="card-body">
                <?php echo (isset($alert)) ? $alert : ''; ?>
                <form action="" method="post" autocomplete="off" id="formulario">
                    <input type="hidden" name="id" id="id">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label for="nombre" class="text-dark font-weight-bold">Nombre</label>
                                <input type="text" placeholder="Ingrese Nombre" name="nombre" id="nombre"
                                    class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="mesas" class="text-dark font-weight-bold">Mesas</label>
                                <input type="number" placeholder="Mesas" name="mesas" id="mesas" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-5 text-center">
                            <label for="">Acciones</label> <br>
                            <input type="submit" value="Registrar" class="btn btn-primary" id="btnAccion">
                            <input type="button" value="Nuevo" class="btn btn-success" id="btnNuevo"
                                onclick="limpiar()">
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tbl">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Mesas</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include "../conexion.php";

                            $query = mysqli_query($conexion, "SELECT * FROM salas WHERE estado = 1");
                            $result = mysqli_num_rows($query);
                            if ($result > 0) {
                                while ($data = mysqli_fetch_assoc($query)) { ?>
                                    <tr>
                                        <td><?php echo $data['id']; ?></td>
                                        <td><?php echo $data['nombre']; ?></td>
                                        <td><?php echo $data['mesas']; ?></td>
                                        <td>
                                            <a href="#" onclick="editarCliente(<?php echo $data['id']; ?>)"
                                                class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                            <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=salas" method="post"
                                                class="confirmar d-inline">
                                                <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php }
                            } ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <script>
            function editarCliente(id) {
                // Realizar una solicitud AJAX para obtener los datos de la sala
                fetch(`obtener_sala.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                        } else {
                            // Cargar los datos en el formulario
                            document.getElementById('id').value = data.id;
                            document.getElementById('nombre').value = data.nombre;
                            document.getElementById('mesas').value = data.mesas;

                            // Cambiar el texto del botón
                            document.getElementById('btnAccion').value = 'Actualizar';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Hubo un error al obtener los datos.');
                    });
            }

            function limpiar() {
                // Limpiar el formulario
                document.getElementById('id').value = '';
                document.getElementById('nombre').value = '';
                document.getElementById('mesas').value = '';
                document.getElementById('btnAccion').value = 'Registrar';
            }
        </script>

    </div>
</div>
<?php include_once "includes/footer.php"; ?>