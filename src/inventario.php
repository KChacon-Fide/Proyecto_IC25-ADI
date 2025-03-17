<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    if (!empty($_POST)) {
        $alert = "";
        $id = $_POST['id'];
        $bebida = $_POST['bebida'];
        $cantidad = $_POST['cantidad'];
        $precio = $_POST['precio'];
        $id_proveedor = $_POST['id_proveedor'];

        if (empty($bebida) || empty($cantidad) || $cantidad <= 0 || empty($precio) || $precio <= 0) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            if (empty($id)) {
                $query = mysqli_query($conexion, "SELECT * FROM inventario WHERE id_bebida = '$bebida'");
                $result = mysqli_fetch_array($query);
                if ($result > 0) {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El inventario para esta bebida ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $total = $cantidad * $precio;
                    $iva = $total * 0.13; // Suponiendo un 13% de IVA
                    $total_final = $total + $iva;
                    $query_insert = mysqli_query($conexion, "INSERT INTO inventario (id_bebida, cantidad, precio, total, iva, total_final, id_proveedor) 
                                                            VALUES ('$bebida', '$cantidad', '$precio', '$total', '$iva', '$total_final', '$id_proveedor')");
                    if ($query_insert) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Inventario registrado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el inventario
                  </div>';
                    }
                }
            } else {
                $total = $cantidad * $precio;
                $iva = $total * 0.13;
                $total_final = $total + $iva;
                $query_update = mysqli_query($conexion, "UPDATE inventario SET id_bebida = '$bebida', cantidad = '$cantidad', precio = '$precio', 
                                                       total = '$total', iva = '$iva', total_final = '$total_final', id_proveedor = '$id_proveedor' 
                                                       WHERE id_inventario = $id");
                if ($query_update) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Inventario modificado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar el inventario
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        }
    }

    // Obtener bebidas
    $bebidas_query = mysqli_query($conexion, "SELECT * FROM bebidas");
    // Obtener proveedores
    $proveedores_query = mysqli_query($conexion, "SELECT * FROM proveedores");

    include_once "includes/header.php";
    ?>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-boxes"></i> Gestión de Inventario</h4>
        </div>
        <div class="card-body">
            <form action="" method="post" autocomplete="off" id="formulario">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <label for="bebida" class="text-dark font-weight-bold">Bebida</label>
                            <select name="bebida" id="bebida" class="form-control">
                                <option value="">Seleccione una bebida</option>
                                <?php while ($bebida = mysqli_fetch_assoc($bebidas_query)) { ?>
                                    <option value="<?php echo $bebida['id']; ?>"><?php echo $bebida['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="cantidad" class="text-dark font-weight-bold">Cantidad</label>
                            <input type="number" step="0.01" placeholder="Ingrese cantidad" class="form-control" name="cantidad" id="cantidad">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="precio" class="text-dark font-weight-bold">Precio</label>
                            <input type="number" step="0.01" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="id_proveedor" class="text-dark font-weight-bold">Proveedor</label>
                            <select name="id_proveedor" id="id_proveedor" class="form-control">
                                <option value="">Seleccione un proveedor</option>
                                <?php while ($proveedor = mysqli_fetch_assoc($proveedores_query)) { ?>
                                    <option value="<?php echo $proveedor['id_proveedor']; ?>"><?php echo $proveedor['nombre']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
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
            <table class="table table-bordered table-center" id="tbl" style="border: 0.5px solid #1E3A8A;">
                <thead style="background-color: #1E3A8A; color: white;">
                    <tr>
                        <th style="border: 0.5px solid #1E3A8A;">#</th>
                        <th style="border: 0.5px solid #1E3A8A;">Bebida</th>
                        <th style="border: 0.5px solid #1E3A8A;">Cantidad</th>
                        <th style="border: 0.5px solid #1E3A8A;">Precio</th>
                        <th style="border: 0.5px solid #1E3A8A;">Total</th>
                        <th style="border: 0.5px solid #1E3A8A;">IVA</th>
                        <th style="border: 0.5px solid #1E3A8A;">Total Final</th>
                        <th style="border: 0.5px solid #1E3A8A;">Proveedor</th>
                        <th style="border: 0.5px solid #1E3A8A;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Realiza la consulta para obtener los datos con los nombres de bebida y proveedor
                    $query = mysqli_query($conexion, "
                        SELECT i.id_inventario, b.nombre AS nombre_bebida, i.cantidad, i.precio, i.total, i.iva, i.total_final, p.nombre AS nombre_proveedor
                        FROM inventario i
                        JOIN bebidas b ON i.id_bebida = b.id
                        JOIN proveedores p ON i.id_proveedor = p.id_proveedor
                    ");
                    
                    // Recorre los resultados y muestra los datos en la tabla
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr>
                            <td style="border: 0.5px solid #1E3A8A;"><?php echo $data['id_inventario']; ?></td>
                            <td class="font-weight-bold" style="border: 0.5px solid #1E3A8A;"><?php echo strtoupper($data['nombre_bebida']); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;"><?php echo number_format($data['cantidad'], 2); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;">₡<?php echo number_format($data['precio'], 2); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;">₡<?php echo number_format($data['total'], 2); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;">₡<?php echo number_format($data['iva'], 2); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;">₡<?php echo number_format($data['total_final'], 2); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;"><?php echo strtoupper($data['nombre_proveedor']); ?></td>
                            <td style="border: 0.5px solid #1E3A8A;">
                                <a href="#" onclick="editarInventario(<?php echo $data['id_inventario']; ?>)" class="btn btn-warning">
                                    <i class='fas fa-edit'></i></a>
                                <form action="eliminar.php?id=<?php echo $data['id_inventario']; ?>&accion=inventario" method="post" class="d-inline">
                                    <button class="btn btn-danger" type="submit">
                                        <i class='fas fa-trash-alt'></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
}
include_once "includes/footer.php";
?>
