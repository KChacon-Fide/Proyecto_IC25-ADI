<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    require_once "../conexion.php";
    $id_user = $_SESSION['idUser'];
    $query = mysqli_query($conexion, "SELECT p.*, s.nombre AS sala, u.nombre FROM pedidos p INNER JOIN salas s ON p.id_sala = s.id INNER JOIN usuarios u ON p.id_usuario = u.id");
    include_once "includes/header.php";
    ?>
    <div class="card shadow-lg rounded">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h3 class="mb-0"><i class="fas fa-history mr-2"></i> Historial de Órdenes</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="custom-thead">
                        <tr>
                            <th>#</th>
                            <th>Sala</th>
                            <th>Mesa</th>
                            <th>Fecha</th>
                            <th>Total (₡)</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($query)) {
                            $estado = ($row['estado'] == 'PENDIENTE')
                                ? '<span class="badge badge-warning p-2 text-dark">Pendiente</span>'
                                : '<span class="badge badge-success p-2">Completado</span>';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $row['id']; ?></td>
                                <td class="text-center font-weight-bold"><?php echo strtoupper($row['sala']); ?></td>
                                <td class="text-center"><?php echo $row['num_mesa']; ?></td>
                                <td class="text-center"><?php echo $row['fecha']; ?></td>
                                <td class="text-center font-weight-bold text-info">
                                    ₡<?php echo number_format($row['total'], 0, '', '.'); ?></td>
                                <td class="text-center"><?php echo $row['nombre']; ?></td>
                                <td class="text-center"><?php echo $estado; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="Reporte.php" class="btn btn-danger btn-lg shadow">
                <i class="fas fa-file-pdf"></i> Generar Reporte PDF
            </a>
        </div>
    </div>

    <style>
        .custom-thead {
            background: #1E3A8A;

            color: white;
            font-size: 16px;
            text-transform: uppercase;
        }

        .table tbody tr:hover {
            background: rgba(30, 58, 138, 0.1);

        }

        .table td,
        .table th {
            vertical-align: middle;
            padding: 12px;
        }

        .badge {
            font-size: 14px;
            font-weight: bold;
            border-radius: 8px;
        }

        .btn-danger {
            font-size: 18px;
            padding: 12px 24px;
            border-radius: 8px;
        }

        .card {
            border-radius: 12px;
            border: none;
        }

        .card-header {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            font-size: 18px;
        }
    </style>

    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>