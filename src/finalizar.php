<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";

    if (!isset($_GET['id_sala']) || !isset($_GET['mesa'])) {
        die("<div class='alert alert-danger text-center'>Faltan parámetros en la URL.</div>");
    }

    $id_sala = $_GET['id_sala'];
    $mesa = $_GET['mesa'];

    $query = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id_sala = '$id_sala' AND num_mesa = '$mesa' AND estado = 'ACTIVO'");
    $pedido = mysqli_fetch_assoc($query);

    if (!$pedido) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'No hay pedidos activos para esta mesa.',
                    confirmButtonText: 'Volver',
                    allowOutsideClick: false
                }).then(() => {
                    window.location = 'index.php';
                });
              </script>";
        exit();
    }

    $id_pedido = $pedido['id'];
    $queryDetalle = mysqli_query($conexion, "SELECT * FROM detalle_pedidos WHERE id_pedido = '$id_pedido'");

    include_once "includes/header.php";
    ?>

    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-receipt"></i> Resumen del Pedido</h4>
        </div>
        <div class="card-body">
            <div class="text-center">
                <h4><strong>Mesa:</strong> <?php echo $mesa; ?></h4>
                <h5><strong>Fecha:</strong> <?php echo $pedido['fecha']; ?></h5>
                <h4 class="text-success"><strong>Total:</strong> ₡<?php echo number_format($pedido['total'], 0); ?></h4>
                <hr>
            </div>

            <div class="card shadow-lg">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-center" id="tbl"
                            style="border-collapse: collapse; border: 0.5px solid #1E3A8A; text-align: center;">
                            <thead style="background-color: #1E3A8A; color: white;">
                                <tr>
                                    <th style="border-bottom: 0.5px solid #1E3A8A; text-align: center;">Nombre</th>
                                    <th style="border-bottom: 0.5px solid #1E3A8A; text-align: center;">Cantidad</th>
                                    <th style="border-bottom: 0.5px solid #1E3A8A; text-align: center;">Precio</th>
                                    <th style="border-bottom: 0.5px solid #1E3A8A; text-align: center;">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while ($detalle = mysqli_fetch_assoc($queryDetalle)) {
                                    $subtotal = $detalle['cantidad'] * $detalle['precio'];
                                    echo "<tr>
                            <td style='border-bottom: 0.5px solid #1E3A8A; text-align: center;'>{$detalle['nombre']}</td>
                            <td style='border-bottom: 0.5px solid #1E3A8A; text-align: center;'>{$detalle['cantidad']}</td>
                            <td style='border-bottom: 0.5px solid #1E3A8A; text-align: center;'>₡{$detalle['precio']}</td>
                            <td style='border-bottom: 0.5px solid #1E3A8A; text-align: center;'>₡{$subtotal}</td>
                          </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-center">
                        <form method="POST">
                            <input type="hidden" name="finalizar_pedido" value="1">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check-circle"></i> Confirmar / Finalizar Pedido
                            </button>
                        </form>
                        <br>

                        <a href="Factura.php?id_pedido=<?php echo $id_pedido; ?>" class="btn btn-primary btn-lg"
                            target="_blank" style="background-color: #1E3A8A;">
                            <i class="fas fa-file-pdf"></i> Descargar Factura en PDF
                        </a>
                    </div>
                </div>
            </div>

            <style>
                .table tbody tr:hover {
                    background: rgba(30, 58, 138, 0.1);
                }
            </style>


            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <?php
            if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
                $update = mysqli_query($conexion, "UPDATE pedidos SET estado = 'FINALIZADO' WHERE id = '$id_pedido'");

                if ($update) {
                    $updateMesa = mysqli_query($conexion, "UPDATE mesas SET estado = 'DISPONIBLE' WHERE id_sala = '$id_sala' AND num_mesa = '$mesa'");

                    echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido Finalizado',
                    text: 'La orden se ha cerrado correctamente.',
                    confirmButtonText: 'Aceptar',
                    allowOutsideClick: false
                }).then(() => {
                    window.location = 'index.php';
                });
              </script>";
                } else {
                    echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo finalizar el pedido.',
                    confirmButtonText: 'Intentar de nuevo'
                });
              </script>";
                }
            }

            include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>