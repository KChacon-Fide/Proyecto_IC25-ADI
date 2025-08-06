<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $items_per_page = 5;
    $offset = ($page - 1) * $items_per_page;

    $busqueda = '';
    if (!empty($_GET['search'])) {
        $busqueda = mysqli_real_escape_string($conexion, $_GET['search']);
        $where = "AND nombre LIKE '%$busqueda%'";
    } else {
        $where = '';
    }

    $total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM platos WHERE estado = 1 $where");
    $total_result = mysqli_fetch_assoc($total_query);
    $total_items = $total_result['total'];
    $total_pages = ceil($total_items / $items_per_page);

    if (!empty($_POST)) {
        $alert = "";
        $id = $_POST['id'];
        $plato = $_POST['plato'];
        $precio = $_POST['precio'];
        $foto_actual = $_POST['foto_actual'];
        $foto = $_FILES['foto'];
        $fecha = date('YmdHis');

        if (empty($plato) || empty($precio) || $precio < 0) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } elseif (!is_numeric($precio) || $precio < 0) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                    El precio debe ser un número válido mayor o igual a cero
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                  </div>';
        } else {
            $nombre = null;
            if (!empty($foto['name'])) {
                $nombre = '../assets/img/platos/' . $fecha . '.jpg';
            } else if (!empty($foto_actual) && empty($foto['name'])) {
                $nombre = $foto_actual;
            }

            if (empty($id)) {
                $query = mysqli_query($conexion, "SELECT * FROM platos WHERE nombre = '$plato' AND estado = 1");
                $result = mysqli_fetch_array($query);
                if ($result > 0) {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        El plato ya existe
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $query_insert = mysqli_query($conexion, "INSERT INTO platos (nombre,precio,imagen) VALUES ('$plato', '$precio', '$nombre')");
                    if ($query_insert) {
                        if (!empty($foto['name'])) {
                            move_uploaded_file($foto['tmp_name'], $nombre);
                        }
                        $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Plato registrado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                        $total_query = mysqli_query($conexion, "SELECT COUNT(*) as total FROM platos WHERE estado = 1 $where");
                        $total_result = mysqli_fetch_assoc($total_query);
                        $total_items = $total_result['total'];
                        $total_pages = ceil($total_items / $items_per_page);
                        header("Location: platos.php?page={$total_pages}");
                        exit;
                    } else {
                        $alert = '<div class="alert alert-danger" role="alert">
                        Error al registrar el plato
                      </div>';
                    }
                }
            } else {
                $query_update = mysqli_query($conexion, "UPDATE platos SET nombre = '$plato', precio=$precio, imagen='$nombre' WHERE id = $id");
                if ($query_update) {
                    if (!empty($foto['name'])) {
                        move_uploaded_file($foto['tmp_name'], $nombre);
                    }
                    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                        Plato modificado correctamente
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                } else {
                    $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Error al modificar el plato
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
                }
            }
        }
    }

    if (!empty($busqueda)) {
        $query = mysqli_query($conexion, "SELECT * FROM platos WHERE estado = 1 AND nombre LIKE '%$busqueda%'");
        $total_pages = 1;
    } else {
        $query = mysqli_query($conexion, "SELECT * FROM platos WHERE estado = 1 LIMIT $items_per_page OFFSET $offset");
    }

    include_once "includes/header.php";
    ?>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-door-open"></i> Gestión de Platos</h4>
        </div>
        <div class="card-body">
            <form action="" method="post" autocomplete="off" id="formulario" enctype="multipart/form-data">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="hidden" id="id" name="id">
                            <input type="hidden" id="foto_actual" name="foto_actual">
                            <label for="plato" class="text-dark font-weight-bold">Nombre del Plato</label>
                            <input type="text" placeholder="Ingrese nombre del plato" name="plato" id="plato"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="precio" class="text-dark font-weight-bold">Precio</label>
                            <input type="text" placeholder="Ingrese precio" class="form-control" name="precio" id="precio">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="foto" class="text-dark font-weight-bold">Foto (510.5px - 510.5px)</label>
                            <input type="file" class="form-control" name="foto" id="foto">
                        </div>
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="">Acciones</label> <br>
                        <input type="submit" value="Registrar" class="btn btn-primary" style="background-color: #1E3A8A;">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card shadow-lg">
        <div class="card-body" style="max-height: 600px; overflow-y: auto; text-align: center;">
            <div class="mb-3">
                <input type="text" id="buscadorPlatos" class="form-control" placeholder="Buscar plato..."
                    style="width: 100%;" onkeyup="buscarPlatos(this.value)">
            </div>

            <table class="table table-bordered table-center" id="tablaPlatos">
                <thead style="background-color: #1E3A8A; color: white; border: 0.5px solid #1E3A8A;">
                    <tr>

                        <th>Plato</th>
                        <th>Precio</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody style="text-align: center;">
                    <?php
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <tr text-center>
                            <td class="font-weight-bold">
                                <?php echo strtoupper($data['nombre']); ?>
                            </td>
                            <td><span class="font-weight-bold">₡<?php echo number_format($data['precio'], 0); ?></span></td>
                            <td>
                                <img class="img-fluid plato-img"
                                    src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                                    alt="Plato">
                            </td>
                            <td>
                                <a onclick="editarPlato(<?php echo $data['id']; ?>)" class="btn btn-warning">
                                    <i class='fas fa-edit'></i>
                                </a>
                                <button class="btn btn-danger"
                                    onclick="confirmDeletePlato(<?php echo $data['id']; ?>, '<?php echo htmlspecialchars($data['nombre'], ENT_QUOTES); ?>')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
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

            .plato-img {
                width: 80px;
                height: 80px;
                object-fit: cover;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }
        </style>
    </div>
    </div>
    </div>
    <script>
        document.getElementById('buscadorPlatos').addEventListener('keyup', function () {
            let filtro = this.value.toLowerCase();
            let filas = document.querySelectorAll('#tablaPlatos tbody tr');

            filas.forEach(function (fila) {
                let texto = fila.textContent.toLowerCase();
                fila.style.display = texto.includes(filtro) ? '' : 'none';
            });
        });
    </script>
    <script src="/assets/js/sweetalert2@11.js"></script>

    <script>
        function confirmDeletePlato(id, nombre) {
            Swal.fire({
                title: '¿Eliminar plato?',
                text: 'Confirma que deseas borrar "' + nombre + '"',
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
                    const icon = Swal.getIcon();
                    icon.classList.remove('swal2-warning');
                    icon.classList.add('swal2-success', 'swal2-icon-show');
                    icon.innerHTML =
                        '<div class="swal2-success-circular-line-left"></div>' +
                        '<span class="swal2-success-line-tip"></span>' +
                        '<span class="swal2-success-line-long"></span>' +
                        '<div class="swal2-success-ring"></div>' +
                        '<div class="swal2-success-fix"></div>' +
                        '<div class="swal2-success-circular-line-right"></div>';
                    return new Promise(resolve => setTimeout(resolve, 600));
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'eliminar.php?id=' + id + '&accion=platos';
                }
            });
        }
    </script>
    <script>
        function buscarPlatos(valor) {
            if (valor.trim() === '') {
                window.location.href = 'platos.php';
                return;
            }
            fetch('buscar_platos.php?search=' + encodeURIComponent(valor))
                .then(response => response.text())
                .then(data => {
                    document.querySelector("#tablaPlatos tbody").innerHTML = data;
                })
                .catch(error => {
                    console.error('Error al buscar platos:', error);
                });
        }
    </script>




    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
} ?>