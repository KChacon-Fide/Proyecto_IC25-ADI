<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    $alerta_inventario = "";
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $items_per_page = 10;
    $offset = ($page - 1) * $items_per_page;


    $total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM inventario");
    $total_result = mysqli_fetch_assoc($total_query);
    $total_items = $total_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    $query_bajo_inventario = mysqli_query($conexion, "
    SELECT b.nombre AS nombre_bebida, i.cantidad 
    FROM inventario i
    JOIN bebidas b ON i.id_bebida = b.id
    WHERE i.cantidad < 5
");

    if (mysqli_num_rows($query_bajo_inventario) > 0) {
        $alerta_inventario = '<div class="alert alert-dismissible fade show" role="alert">
        <strong class="text-danger">¡Atención!</strong> Los siguientes productos tienen menos de 5 unidades:<ul>';
        while ($producto = mysqli_fetch_assoc($query_bajo_inventario)) {
            $alerta_inventario .= "<li>{$producto['nombre_bebida']} - {$producto['cantidad']} unidades</li>";
        }
        $alerta_inventario .= '</ul>
        
    </div>';
    }
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
                    $iva = $total * 0.13;
                    $total_final = $total + $iva;
                    $query_insert = mysqli_query($conexion, "INSERT INTO inventario (id_bebida, cantidad, precio, total, iva, total_final, id_proveedor, fecha) 
                                                            VALUES ('$bebida', '$cantidad', '$precio', '$total', '$iva', '$total_final', '$id_proveedor', NOW())");
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
                            
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar el inventario
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            
                        </button>
                    </div>';
                }
            }
        }
    }


    $bebidas_query = mysqli_query($conexion, "SELECT * FROM bebidas WHERE estado = 1");

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
                <?php echo isset($alerta_inventario) ? $alerta_inventario : ''; ?>
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
                            <input type="number" step="0.01" placeholder="Ingrese cantidad" class="form-control"
                                name="cantidad" id="cantidad">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="precio" class="text-dark font-weight-bold">Precio</label>
                            <input type="number" step="0.01" placeholder="Ingrese precio" class="form-control" name="precio"
                                id="precio">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="id_proveedor" class="text-dark font-weight-bold">Proveedor</label>
                            <select name="id_proveedor" id="id_proveedor" class="form-control">
                                <option value="">Seleccione un proveedor</option>
                                <?php while ($proveedor = mysqli_fetch_assoc($proveedores_query)) { ?>
                                    <option value="<?php echo $proveedor['id_proveedor']; ?>">
                                        <?php echo $proveedor['nombre']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md2">
                        <label for="">Acciones</label> <br>
                        <input type="submit" value="Registrar" class="btn btn-primary" id="submitButton"
                            style="background-color: #1E3A8A;">
                    </div>
                </div>
            </form>
            <div class="row mt-2">
                <div class="col-md-4">
                    <form action="EntradasInventario.php" method="GET" target="_blank">
                        <div class="form-group">
                            <label for="fecha_entrada" class="text-dark font-weight-bold">Fecha de Entrada</label>
                            <div class="d-flex">
                                <input type="date" id="fecha_entrada" name="fecha" class="form-control mr-2" required>
                                <button type="submit" class="btn btn-success" title="PDF Entradas">
                                    <i class="fas fa-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-body" style="max-height: 600px; overflow-y: auto;">
            <div class="table-responsive">
                <table class="table table-center text-center">
                    <thead style="background-color: #1E3A8A; color: white;">
                        <tr>

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

                        $query = mysqli_query($conexion, "
                        SELECT i.id_inventario, b.nombre AS nombre_bebida, i.cantidad, i.precio, i.total, i.iva, i.total_final, p.nombre AS nombre_proveedor
                        FROM inventario i
                        JOIN bebidas b ON i.id_bebida = b.id
                        JOIN proveedores p ON i.id_proveedor = p.id_proveedor
                        LIMIT $items_per_page OFFSET $offset
                    ");


                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>

                                <td class="font-weight-bold"><?php echo strtoupper($data['nombre_bebida']); ?></td>
                                <td><?php echo number_format($data['cantidad'], 0); ?></td>
                                <td>₡<?php echo number_format($data['precio'], 0); ?></td>
                                <td>₡<?php echo number_format($data['total'], 0); ?></td>
                                <td>₡<?php echo number_format($data['iva'], 0); ?></td>
                                <td>₡<?php echo number_format($data['total_final'], 0); ?></td>
                                <td><?php echo strtoupper($data['nombre_proveedor']); ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a onclick="editarInventario(<?php echo $data['id_inventario']; ?>)"
                                            class="btn btn-warning">
                                            <i class='fas fa-edit'></i>
                                        </a>
                                        <!-- El formulario oculto para POST -->
                                        <form action="eliminar_inventario.php" method="POST"
                                            id="formDel<?= $data['id_inventario'] ?>" class="d-inline">
                                            <input type="hidden" name="id_inventario" value="<?= $data['id_inventario'] ?>">
                                        </form>
                                        <!-- Sin <form>, solo un botón que ejecuta tu alerta -->
                                        <button class="btn btn-danger" style="margin-left:4px;" onclick="confirmDeleteInventario(
    <?php echo $data['id_inventario']; ?>,
    '<?php echo addslashes($data['nombre_bebida']); ?>'
  )">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="text-center mt-3">
            <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page - 1; ?>">Anterior</a></li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item"><a class="page-link" href="?page=<?php echo $page + 1; ?>">Siguiente</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
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
        function editarInventario(id) {

            fetch(`obtener_inventario.php?id=${id}`)
                .then(response => response.json())
                .then(data => {

                    document.getElementById('id').value = data.id_inventario;
                    document.getElementById('bebida').value = data.id_bebida;
                    document.getElementById('cantidad').value = data.cantidad;
                    document.getElementById('precio').value = data.precio;
                    document.getElementById('id_proveedor').value = data.id_proveedor;

                    document.getElementById('submitButton').value = 'Actualizar';
                })
                .catch(error => console.error('Error:', error));
        }
    </script>
    <script src="/assets/js/sweetalert2@11.js"></script>
    <script>
        function confirmDeleteInventario(id, nombre) {
            Swal.fire({
                title: '¿Eliminar inventario?',
                text: `Se eliminará el inventario de "${nombre}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1E3A8A',
                cancelButtonColor: '#dc3545',
                reverseButtons: true,
                width: 350,
                preConfirm: () => {
                    Swal.showLoading();
                    return new Promise(r => setTimeout(r, 300));
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Envía el formulario oculto vía POST
                    document.getElementById(`formDel${id}`).submit();
                }
            });
        }
    </script>



    <?php
}
include_once "includes/footer.php";
?>