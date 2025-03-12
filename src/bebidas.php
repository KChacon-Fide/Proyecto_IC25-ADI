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
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }
        if (empty($nombre) || empty($precio) || $precio < 0) {
            $alert = '<div class="alert alert-warning">Todos los campos son obligatorios.</div>';
        } else {
            $imagen = $foto_actual;
            if (!empty($foto['name'])) {
                $imagen = $directorio . $fecha . '.jpg';
            }
            if (empty($id)) {
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
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-wine-glass-alt"></i> Gestión de Bebidas</h4>
        </div>
        <div class="card-body">
            <form action="" method="post" enctype="multipart/form-data">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <input type="hidden" id="foto_actual" name="foto_actual">
                            <label for="nombre" class="font-weight-bold">Nombre de la Bebida</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                placeholder="Ingrese nombre de la bebida">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="precio" class="font-weight-bold">Precio</label>
                            <input type="text" name="precio" id="precio" class="form-control" placeholder="Ingrese precio">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="foto" class="font-weight-bold">Imagen (510.5px - 510.5px)</label>
                            <input type="file" class="form-control" name="foto" id="foto">
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Acciones</label> <br>
                        <input type="submit" value="Registrar" class="btn btn-primary" style="background-color: #1E3A8A;">
                        <input type="button" value="Nuevo" onclick="limpiar()" class="btn btn-success">
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
                            <th style="border: 0.5px solid #1E3A8A;">#</th>
                            <th style="border: 0.5px solid #1E3A8A;">Bebida</th>
                            <th style="border: 0.5px solid #1E3A8A;">Precio</th>
                            <th style="border: 0.5px solid #1E3A8A;">Imagen</th>
                            <th style="border: 0.5px solid #1E3A8A;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conexion, "SELECT * FROM bebidas WHERE estado = 1");
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td style="border: 0.5px solid #1E3A8A;"><?php echo $data['id']; ?></td>
                                <td class="font-weight-bold" style="border: 0.5px solid #1E3A8A;">
                                    <?php echo strtoupper($data['nombre']); ?>
                                </td>
                                <td class="text-success font-weight-bold" style="border: 0.5px solid #1E3A8A;">
                                    ₡<?php echo number_format($data['precio'], 0, '', '.'); ?></td>
                                <td style="border: 0.5px solid #1E3A8A;">
                                    <img class="img-thumbnail"
                                        src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                                        alt="" width="80">
                                </td>
                                <td style="border: 0.5px solid #1E3A8A;">
                                    <a href="#"
                                        onclick="editarBebida(<?php echo $data['id']; ?>, '<?php echo $data['nombre']; ?>', '<?php echo $data['precio']; ?>', '<?php echo $data['imagen']; ?>')"
                                        class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="eliminar.php?id=<?php echo $data['id']; ?>&accion=bebidas" method="post"
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
        .table tbody tr:hover {
            background: rgba(30, 58, 138, 0.1);

        }
    </style>
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