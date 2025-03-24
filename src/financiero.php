<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    if (!empty($_POST)) {
        $alert = "";
        $inventario_inicial = $_POST['inventario_inicial'];
        $compras = $_POST['compras'];
        $gastos_mes = $_POST['gastos_mes'];
        $salarios = $_POST['salarios'];
        $impuestos = $_POST['impuestos'];
        $cuentas_por_pagar = $_POST['cuentas_por_pagar'];
        $ventas = $_POST['ventas'];
        $inventario_final = $_POST['inventario_final'];

        // Validaciones
        if (empty($inventario_inicial) || empty($compras) || empty($gastos_mes) || empty($salarios) || empty($impuestos) || empty($cuentas_por_pagar) || empty($ventas)) {
            $alert = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        Todos los campos son obligatorios.
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>';
        } else {
            $query_insert = mysqli_query($conexion, "INSERT INTO repo_financiero 
                                                        (inventario_inicial, compras, gastos_mes, salarios, impuestos, cuentas_por_pagar, ventas, inventario_final) 
                                                        VALUES 
                                                        ('$inventario_inicial', '$compras', '$gastos_mes', '$salarios', '$impuestos', '$cuentas_por_pagar', '$ventas', '$inventario_final')");
            if ($query_insert) {
                $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Reporte financiero registrado correctamente.
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>';
            } else {
                $alert = '<div class="alert alert-danger" role="alert">
                            Error al registrar el reporte financiero.
                        </div>';
            }
        }
    }

    include_once "includes/header.php";
    ?>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-boxes"></i> Gestión de Reportes Financieros</h4>
        </div>
        <div class="card-body">
            <form action="" method="post" autocomplete="off" id="formulario">
                <?php echo isset($alert) ? $alert : ''; ?>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inventario_inicial" class="text-dark font-weight-bold">Inventario Inicial</label>
                            <input type="number" step="0.01" placeholder="Ingrese inventario inicial" class="form-control" name="inventario_inicial" id="inventario_inicial">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="compras" class="text-dark font-weight-bold">Compras</label>
                            <input type="number" step="0.01" placeholder="Ingrese compras" class="form-control" name="compras" id="compras">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gastos_mes" class="text-dark font-weight-bold">Gastos del Mes</label>
                            <input type="number" step="0.01" placeholder="Ingrese gastos del mes" class="form-control" name="gastos_mes" id="gastos_mes">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="salarios" class="text-dark font-weight-bold">Salarios</label>
                            <input type="number" step="0.01" placeholder="Ingrese salarios" class="form-control" name="salarios" id="salarios">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="impuestos" class="text-dark font-weight-bold">Impuestos</label>
                            <input type="number" step="0.01" placeholder="Ingrese impuestos" class="form-control" name="impuestos" id="impuestos">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cuentas_por_pagar" class="text-dark font-weight-bold">Cuentas por Pagar</label>
                            <input type="number" step="0.01" placeholder="Ingrese cuentas por pagar" class="form-control" name="cuentas_por_pagar" id="cuentas_por_pagar">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ventas" class="text-dark font-weight-bold">Ventas</label>
                            <input type="number" step="0.01" placeholder="Ingrese ventas" class="form-control" name="ventas" id="ventas">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inventario_final" class="text-dark font-weight-bold">Inventario Final</label>
                            <input type="number" step="0.01" placeholder="Ingrese inventario final" class="form-control" name="inventario_final" id="inventario_final">
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <input type="submit" value="Registrar Reporte" class="btn btn-primary" style="background-color: #1E3A8A;">
                    <a href="generar_pdf.php" target="_blank" class="btn btn-danger">Generar PDF</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card shadow-lg">
    <div class="card-body" style="max-height: 700px; overflow-y: auto;">
        <table  class="table table-bordered">
            <thead style="background-color: #1E3A8A; color: white; font-size: 14px;">
                <tr>
                    <th style="border: 0.5px solid #1E3A8A;">Fecha</th>
                    <th style="border: 0.5px solid #1E3A8A;">Inventario Inicial</th>
                    <th style="border: 0.5px solid #1E3A8A;">Compras</th>
                    <th style="border: 0.5px solid #1E3A8A;">Gastos</th>
                    <th style="border: 0.5px solid #1E3A8A;">Salarios</th>
                    <th style="border: 0.5px solid #1E3A8A;">Impuestos</th>
                    <th style="border: 0.5px solid #1E3A8A;">Cuentas x Pagar</th>
                    <th style="border: 0.5px solid #1E3A8A;">Total Salidas</th>
                    <th style="border: 0.5px solid #1E3A8A;">Ventas</th>
                    <th style="border: 0.5px solid #1E3A8A;">Inventario Final</th>
                    <th style="border: 0.5px solid #1E3A8A;">Total Entradas</th>
                    <th style="border: 0.5px solid #1E3A8A;">Utilidades</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = mysqli_query($conexion, "SELECT * FROM repo_financiero");
                while ($data = mysqli_fetch_assoc($query)) {
                    $fecha_formateada = date('d/m/Y', strtotime($data['Fecha']));
                    echo "
                    <trstyle='font-size: 14px;'>
                        <td style='border: 0.2px solid #1E3A8A;'>" . strtoupper($fecha_formateada) . "</td>
                        <td style='border: 0.2px solid #1E3A8A;'>" . number_format($data['inventario_inicial']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['compras']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['gastos_mes']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['salarios']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['impuestos']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['cuentas_por_pagar']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['total_salidas']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['ventas']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['inventario_final']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['total_entradas']) . "</td>
                        <td style='border: 0.5px solid #1E3A8A;'>" . number_format($data['utilidades'], 2) . "</td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
    </div>
    
</div>


<?php
}
include_once "includes/footer.php";
?>