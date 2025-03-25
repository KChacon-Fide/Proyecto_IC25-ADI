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
    $aplicar_impuesto = false;
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
            
            
            <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="flexCheckDefault" onclick="toggleImpuesto()">
                    <label class="form-check-label" for="flexCheckDefault">
                        Aplicar Impuesto al Servicio (10%)
                    </label>
                </div>

                <div class="mt-4 text-center" id="impuestoInfo" style="display: none;">
                    <h4 class="text-info"><strong>Impuesto al Servicio (10%):</strong> ₡<span id="impuestoServicio"></span></h4>
                    <h4 class="text-success"><strong>Total con Impuesto:</strong> ₡<span id="totalConImpuesto"></span></h4>
                    <hr>
                </div>
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
                        <input type="hidden" name="aplicar_impuesto" id="aplicar_impuesto" value="<?php echo $aplicar_impuesto ? '1' : '0'; ?>">
                        <input type="hidden" name="total_con_impuesto" id="total_con_impuesto" value="<?php echo $pedido['total']; ?>">
                        <input type="hidden" name="imp_servicio" id="imp_servicio" value="0">
                        <button type="summit" class="btn btn-success btn-lg" >
                        <i class="fas fa-check-circle"></i> Confirmar / Finalizar Pedido
                        </button>
                    </form>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    .table tbody tr:hover {
        background: rgba(30, 58, 138, 0.1);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleImpuesto() {
        var checkbox = document.getElementById('flexCheckDefault');
        var aplicarImpuestoInput = document.getElementById('aplicar_impuesto');
        var impuestoInfo = document.getElementById('impuestoInfo');
        var impuestoServicio = document.getElementById('impuestoServicio');
        var totalConImpuesto = document.getElementById('totalConImpuesto');
        var impServicioInput = document.getElementById('imp_servicio');
        var total = <?php echo $pedido['total']; ?>;
        var impuesto = total * 0.10;
        var totalConImpuestoValor = total + impuesto;

        aplicarImpuestoInput.value = checkbox.checked ? '1' : '0';
        impServicioInput.value = checkbox.checked ? impuesto.toFixed(0) : '0';

        if (checkbox.checked) {
            impuestoServicio.innerText = impuesto.toFixed(0);
            totalConImpuesto.innerText = totalConImpuestoValor.toFixed(0);
            impuestoInfo.style.display = 'block';
            document.getElementById('total_con_impuesto').value = totalConImpuestoValor.toFixed(0);
        } else {
            impuestoInfo.style.display = 'none';
            document.getElementById('total_con_impuesto').value = total.toFixed(0);
        }
    }

    document.getElementById('confirmarFinalizarPedido').addEventListener('click', function() {
        document.getElementById('finalizarPedidoForm').submit();
        window.open('Factura.php?id_pedido=<?php echo $id_pedido; ?>', '_blank');
    });
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
    $aplicar_impuesto = isset($_POST['aplicar_impuesto']) && $_POST['aplicar_impuesto'] == '1';
    $total_con_impuesto = $_POST['total_con_impuesto'];
    $imp_servicio = $_POST['imp_servicio'];

    $update = mysqli_query($conexion, "UPDATE pedidos SET estado = 'FINALIZADO', total = '$total_con_impuesto', ImpServicio = '$imp_servicio' WHERE id = '$id_pedido'");

    if ($update) {
        $updateMesa = mysqli_query($conexion, "UPDATE mesas SET estado = 'DISPONIBLE' WHERE id_sala = '$id_sala' AND num_mesa = '$mesa'");

        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido Finalizado',
                    html: `La orden se ha cerrado correctamente.<br>
                           ¿Desea imprimir la factura?<br>
                           <a href='Factura.php?id_pedido=$id_pedido' class='btn btn-primary btn-lg'
                              target='_blank' style='background-color: #1E3A8A; margin-top: 10px;'>
                              <i class='fas fa-file-pdf'></i> Descargar Factura en PDF
                           </a>`,
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