<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    require_once "../conexion.php";

    // Validar la fecha seleccionada (por defecto, la fecha actual)
    $fecha_seleccionada = isset($_POST['fecha']) ? $_POST['fecha'] : date('Y-m-d');

    // Validar el usuario seleccionado
    $usuario_seleccionado = isset($_POST['usuario']) ? $_POST['usuario'] : '';

    // Si se presionó el botón "Generar PDF", redirigir a pdf_comisiones.php
    if (isset($_POST['generar_pdf'])) {
        header("Location: pdf_comisiones.php?fecha=$fecha_seleccionada&usuario=$usuario_seleccionado");
        exit;
    }

    // Modificar la consulta para filtrar por usuario si se seleccionó uno
    $where_clause = "WHERE DATE(p.fecha) = '$fecha_seleccionada'";
    if ($usuario_seleccionado) {
        $where_clause .= " AND p.id_usuario = '$usuario_seleccionado'";
    }

    $query_ingresos = mysqli_query($conexion, "
        SELECT 
            u.nombre AS mesero, 
            SUM(p.total) AS total_pedidos,
            SUM(p.ImpServicio) AS ingresos_servicio
        FROM pedidos p
        INNER JOIN usuarios u ON p.id_usuario = u.id
        $where_clause
        GROUP BY u.id
    ");

    // Almacenar los resultados
    $resultados = [];
    while ($row = mysqli_fetch_assoc($query_ingresos)) {
        $resultados[] = [
            'mesero' => $row['mesero'],
            'total_pedidos' => $row['total_pedidos'],
            'ingresos_servicio' => $row['ingresos_servicio']
        ];
    }

    include_once "includes/header.php";
?>
    <div class="card shadow-lg rounded">
        <div class="card-header bg-primary text-white d-flex align-items-center">
            <h4 class="mb-0"><i class="fas fa-calendar-alt"></i> Ingresos por Fecha</h4>
        </div>
        <div class="card-body">
            <!-- Formulario para seleccionar la fecha y filtrar por usuario -->
            <form method="POST" class="mb-4">
                <div class="form-group">
                    <label for="fecha">Seleccionar Fecha:</label>
                    <input type="date" id="fecha" name="fecha" class="form-control" value="<?php echo $fecha_seleccionada; ?>" required>
                </div>
                <div class="form-group">
                    <label for="usuario">Filtrar por Usuario:</label>
                    <select id="usuario" name="usuario" class="form-control">
                        <option value="">Todos</option>
                        <?php
                        // Obtener la lista de usuarios
                        $usuarios_query = mysqli_query($conexion, "SELECT id, nombre FROM usuarios");
                        while ($usuario = mysqli_fetch_assoc($usuarios_query)) {
                            $selected = ($usuario_seleccionado == $usuario['id']) ? 'selected' : '';
                            echo "<option value='{$usuario['id']}' $selected>{$usuario['nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" name="consultar" class="btn btn-primary">Consultar</button>
                <button type="submit" name="generar_pdf" class="btn btn-danger">Generar PDF</button>
            </form>
            <!-- Tabla de resultados -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="custom-thead">
                        <tr>
                            <th>Mesero</th>
                            <th>Total Pedidos (₡)</th>
                            <th>Ingresos por Servicio (₡)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($resultados)) { ?>
                            <?php foreach ($resultados as $resultado) { ?>
                                <tr>
                                    <td class="text-center font-weight-bold"><?php echo $resultado['mesero']; ?></td>
                                    <td class="text-center font-weight-bold text-info">
                                        ₡<?php echo number_format($resultado['total_pedidos'], 0, '', '.'); ?>
                                    </td>
                                    <td class="text-center font-weight-bold text-success">
                                        ₡<?php echo number_format($resultado['ingresos_servicio'], 0, '', '.'); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="3" class="text-center">No hay datos para la fecha seleccionada.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
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