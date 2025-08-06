<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $items_per_page = 5;
    $offset = ($page - 1) * $items_per_page;

    $total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM proveedores WHERE estado = 1");
    $total_result = mysqli_fetch_assoc($total_query);
    $total_items = $total_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    $query = mysqli_query(
        $conexion,
        "SELECT * FROM proveedores WHERE estado = 1 LIMIT $items_per_page OFFSET $offset"
    );

    if (!empty($_POST)) {
        $alert = "";
        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $fecha = date('YmdHis');

        if (empty($nombre)) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El campo de nombre es obligatorio
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {

            if (empty($id)) {
                $q_activo = mysqli_query(
                    $conexion,
                    "SELECT * FROM proveedores WHERE nombre = '$nombre' AND estado = 1"
                );
                if (mysqli_num_rows($q_activo) > 0) {
                    $alert = '<div class="alert alert-warning">El proveedor ya existe</div>';
                } else {
                    $q_inactivo = mysqli_query(
                        $conexion,
                        "SELECT * FROM proveedores WHERE nombre = '$nombre' AND estado = 0"
                    );
                    if (mysqli_num_rows($q_inactivo) > 0) {
                        mysqli_query(
                            $conexion,
                            "UPDATE proveedores SET estado = 1 WHERE nombre = '$nombre'"
                        );
                    } else {
                        mysqli_query(
                            $conexion,
                            "INSERT INTO proveedores (nombre, estado) VALUES ('$nombre', 1)"
                        );
                    }
                    header("Location: proveedores.php?page={$page}&msg=created");
                    exit;

                    if ($query_insert) {
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Proveedor registrado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                        header("Location: proveedores.php?page={$page}&msg=created");
                        exit;
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">
                    Error al registrar el proveedor
                  </div>';
                    }
                }
            } else {
                $query_update = mysqli_query($conexion, "UPDATE proveedores SET nombre = '$nombre' WHERE id_proveedor = $id");
                if ($query_update) {
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Proveedor modificado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                    header("Location: proveedores.php?page={$page}");
                    exit;
                } else {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar el proveedor
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                    header("Location: proveedores.php?page={$page}");
                    exit;
                }
            }
        }
    }
    include_once "includes/header.php";
    ?>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-door-open"></i> Gestión de Proveedores</h4>
        </div>
        <div class="card-body">
            <form action="" method="post" autocomplete="off" id="formulario">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <label for="nombre" class="text-dark font-weight-bold">Nombre del Proveedor</label>
                            <input type="text" placeholder="Ingrese nombre del proveedor" name="nombre" id="nombre"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
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
                <table class="table table-bordered table-center text-center" style="border: 0.5px solid #1E3A8A;">
                    <thead style="background-color: #1E3A8A; color: white;">
                        <tr>

                            <th style="border: 0.5px solid #1E3A8A;">Proveedor</th>
                            <th style="border: 0.5px solid #1E3A8A;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($data = mysqli_fetch_assoc($query)) { ?>
                            <tr>
                                <td class="font-weight-bold">
                                    <?php echo strtoupper($data['nombre']); ?>
                                </td>
                                <td>
                                    <button class="btn btn-warning"
                                        onclick="editarProveedor(<?= $data['id_proveedor'] ?>,'<?= addslashes($data['nombre']) ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form id="delProv<?= $data['id_proveedor'] ?>" action="eliminar-proveedor.php" method="post"
                                        class="d-none">
                                        <input type="hidden" name="id_proveedor" value="<?= $data['id_proveedor'] ?>">
                                        <input type="hidden" name="accion" value="proveedores">
                                    </form>
                                    <button class="btn btn-danger ml-2"
                                        onclick="confirmDeleteProveedor(<?= $data['id_proveedor'] ?>,'<?= addslashes($data['nombre']) ?>')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
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
        function limpiarFormulario() {
            document.getElementById("id").value = "";
            document.getElementById("nombre").value = "";
        }

        function editarProveedor(id, nombre) {
            document.getElementById("id").value = id;
            document.getElementById("nombre").value = nombre;
        }
    </script>
    <script src="/assets/js/sweetalert2@11.js"></script>
    <script>
        function confirmDeleteProveedor(id, nombre) {
            Swal.fire({
                title: '¿Eliminar proveedor?',
                text: `Se borrará "${nombre}"`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#1E3A8A',
                cancelButtonColor: '#dc3545',
                reverseButtons: true,
                width: 350,
                preConfirm: () => Swal.showLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delProv${id}`).submit();
                }
            });
        }
    </script>


    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>