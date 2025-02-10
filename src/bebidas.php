<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";

    if (!empty($_POST)) {
        $alert = "";
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $foto_actual = $_POST['foto_actual'];
        $foto = $_FILES['foto'];
        $fecha = date('YmdHis');
        $directorio = '../assets/img/bebidas/';

        // Verificar si la carpeta de imágenes existe, si no, crearla
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        // Validar que los datos sean correctos
        if (empty($nombre) || empty($precio) || $precio < 0) {
            $alert = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
        } else {
            // Definir la imagen
            $imagen = $foto_actual;
            if (!empty($foto['name'])) {
                $imagen = $directorio . $fecha . '.jpg';
            }

            if (empty($id)) {
                // Inserción de nueva bebida
                $query = mysqli_query($conexion, "SELECT * FROM bebidas WHERE nombre = '$nombre' AND estado = 1");
                if (mysqli_num_rows($query) > 0) {
                    $alert = '<div class="alert alert-warning">La bebida ya existe.</div>';
                } else {
                    $query_insert = mysqli_query($conexion, "INSERT INTO bebidas (nombre, precio, imagen, fecha) VALUES ('$nombre', '$precio', '$imagen', NOW())");
                    if ($query_insert) {
                        if (!empty($foto['name'])) {
                            move_uploaded_file($foto['tmp_name'], $imagen);
                        }
                        $alert = '<div class="alert alert-success">Bebida registrada correctamente.</div>';
                    } else {
                        $alert = '<div class="alert alert-danger">Error al registrar la bebida.</div>';
                    }
                }
            } else {
                // Actualización de bebida existente
                $query_update = mysqli_query($conexion, "UPDATE bebidas SET nombre = '$nombre', precio = $precio, imagen = '$imagen' WHERE id = $id");
                if ($query_update) {
                    if (!empty($foto['name'])) {
                        move_uploaded_file($foto['tmp_name'], $imagen);
                    }
                    $alert = '<div class="alert alert-success">Bebida modificada correctamente.</div>';
                } else {
                    $alert = '<div class="alert alert-warning">Error al modificar la bebida.</div>';
                }
            }
        }
    }

    include_once "includes/header.php";
    ?>
    <div class="card">
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <input type="hidden" id="foto_actual" name="foto_actual">
                            <label for="nombre">Bebida</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                placeholder="Nombre de la bebida">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="precio">Precio</label>
                            <input type="text" name="precio" id="precio" class="form-control" placeholder="Precio">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="foto">Imagen</label>
                            <input type="file" class="form-control" name="foto" id="foto">
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Acciones</label> <br>
                        <input type="submit" value="Registrar" class="btn btn-primary">
                        <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bebida</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($conexion, "SELECT * FROM bebidas WHERE estado = 1");
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td><?php echo $data['id']; ?></td>
                            <td><?php echo $data['nombre']; ?></td>
                            <td><?php echo $data['precio']; ?></td>
                            <td><img src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                                    width="100"></td>
                            <td>
                                <a href="#"
                                    onclick="editarBebida(<?php echo $data['id']; ?>, '<?php echo $data['nombre']; ?>', '<?php echo $data['precio']; ?>', '<?php echo $data['imagen']; ?>')"
                                    class="btn btn-primary"><i class='fas fa-edit'></i></a>
                                <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=bebidas" method="post"
                                    class="confirmar d-inline">
                                    <button class="btn btn-danger" type="submit"><i class='fas fa-trash-alt'></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function editarBebida(id, nombre, precio, imagen) {
            document.getElementById("id").value = id;
            document.getElementById("nombre").value = nombre;
            document.getElementById("precio").value = precio;
            document.getElementById("foto_actual").value = imagen;
        }
    </script>

    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
} ?>