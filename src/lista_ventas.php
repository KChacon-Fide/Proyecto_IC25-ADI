<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    require_once "../conexion.php";
    $id_user = $_SESSION['idUser'];

    $limit = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $total_pedidos_query = mysqli_query($conexion, "SELECT COUNT(id) AS total FROM pedidos");
    $total_pedidos = mysqli_fetch_assoc($total_pedidos_query)['total'];
    $total_pages = ceil($total_pedidos / $limit);

    $usuario_seleccionado = isset($_GET['usuario']) ? $_GET['usuario'] : '';

    // Modificar la consulta para filtrar por usuario si se seleccionó uno
    $where_clause = $usuario_seleccionado ? "WHERE p.id_usuario = '$usuario_seleccionado'" : '';

    $query = mysqli_query($conexion, "
        SELECT p.*, p.transaccion, s.nombre AS sala, u.nombre 
        FROM pedidos p 
        INNER JOIN salas s ON p.id_sala = s.id 
        INNER JOIN usuarios u ON p.id_usuario = u.id 
        $where_clause 
        LIMIT $limit OFFSET $offset
    ");
    include_once "includes/header.php";
    ?>
    <div class="card shadow-lg rounded">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h4 class="mb-0"><i class="fas fa-door-open"></i> Historial de Órdenes</h4>
        </div>

        <div class="card-body">
            <form method="GET" class="mb-4">
                <div class="form-group">
                    <label for="usuario">Filtrar por Usuario:</label>
                    <select id="usuario" name="usuario" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        // Obtener la lista de usuarios
                        $usuarios_query = mysqli_query($conexion, "SELECT id, nombre FROM usuarios WHERE estado = 1");
                        while ($usuario = mysqli_fetch_assoc($usuarios_query)) {
                            $selected = (isset($_GET['usuario']) && $_GET['usuario'] == $usuario['id']) ? 'selected' : '';
                            echo "<option value='{$usuario['id']}' $selected>{$usuario['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Filtrar</button>
            </form>
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-hover text-center">
                    <thead class="custom-thead">
                        <tr>

                            <th>Sala</th>
                            <th>Mesa</th>
                            <th>Fecha</th>
                            <th>Nº Transacción</th>
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
                                <td class="text-center font-weight-bold"><?php echo strtoupper($row['sala']); ?></td>
                                <td class="text-center"><?php echo $row['num_mesa']; ?></td>
                                <td class="text-center"><?php echo $row['fecha']; ?></td>
                                <td class="text-center">
                                    <?php echo !empty($row['transaccion']) ? $row['transaccion'] : '-'; ?>
                                </td>
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

        <div class="card-footer text-right">
            <a href="Reporte.php" class="btn btn-danger btn-lg shadow" style="background-color: #1E3A8A;">
                <i class="fas fa-file-pdf"></i> Día
            </a>
            <a href="ReporteSemanal.php" class="btn btn-danger btn-lg shadow" style="background-color: #1E3A8A;">
                <i class="fas fa-file-pdf"></i> Semana
            </a>
            <a href="ReporteMes.php" class="btn btn-danger btn-lg shadow" style="background-color: #1E3A8A;">
                <i class="fas fa-file-pdf"></i> Mes
            </a>
            <a href="ReporteAño.php" class="btn btn-danger btn-lg shadow" style="background-color: #1E3A8A;">
                <i class="fas fa-file-pdf"></i> Año
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

        .pagination .page-link {
            color: #1E3A8A;
        }

        .pagination .active .page-link {
            background-color: #1E3A8A;
            border-color: #1E3A8A;
            color: white;
        }
    </style>

    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>