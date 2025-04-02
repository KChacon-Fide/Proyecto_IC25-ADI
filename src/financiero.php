<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";
    $registros_por_pagina = 10;

    $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
    $pagina = max($pagina, 1);
    $inicio = ($pagina - 1) * $registros_por_pagina;
    $total_registros_query = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM repo_financiero");
    $total_registros = mysqli_fetch_assoc($total_registros_query)['total'];
    $total_paginas = ceil($total_registros / $registros_por_pagina);

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
    $query = mysqli_query($conexion, "SELECT * FROM repo_financiero LIMIT $inicio, $registros_por_pagina");


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
                            <input type="number" step="0.01" placeholder="Ingrese inventario inicial" class="form-control"
                                name="inventario_inicial" id="inventario_inicial">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="compras" class="text-dark font-weight-bold">Compras</label>
                            <input type="number" step="0.01" placeholder="Ingrese compras" class="form-control"
                                name="compras" id="compras">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="gastos_mes" class="text-dark font-weight-bold">Gastos del Mes</label>
                            <input type="number" step="0.01" placeholder="Ingrese gastos del mes" class="form-control"
                                name="gastos_mes" id="gastos_mes">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="salarios" class="text-dark font-weight-bold">Salarios</label>
                            <input type="number" step="0.01" placeholder="Ingrese salarios" class="form-control"
                                name="salarios" id="salarios">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="impuestos" class="text-dark font-weight-bold">Impuestos</label>
                            <input type="number" step="0.01" placeholder="Ingrese impuestos" class="form-control"
                                name="impuestos" id="impuestos">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="cuentas_por_pagar" class="text-dark font-weight-bold">Cuentas por Pagar</label>
                            <input type="number" step="0.01" placeholder="Ingrese cuentas por pagar" class="form-control"
                                name="cuentas_por_pagar" id="cuentas_por_pagar">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ventas" class="text-dark font-weight-bold">Ventas</label>
                            <input type="number" step="0.01" placeholder="Ingrese ventas" class="form-control" name="ventas"
                                id="ventas">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="inventario_final" class="text-dark font-weight-bold">Inventario Final</label>
                            <input type="number" step="0.01" placeholder="Ingrese inventario final" class="form-control"
                                name="inventario_final" id="inventario_final" value="0">
                        </div>
                    </div>
                </div>
                <!-- Botón para abrir el modal -->
                <div class="row mt-3">
                    <div class="col-md-3">
                        <input type="submit" value="Registrar Reporte" class="btn btn-primary "
                            style="background-color: #1E3A8A;">
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalFechas">
                            Generar PDF
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
    <div class="card shadow-lg">
        <div class="card-body px-3" style="max-height: 800px; text-align: center;">
            <table class="table table-bordered ">
                <thead style="background-color: #1E3A8A; color: white; font-size: 14px;border: 0.5px solid #1E3A8A;">
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
                    while ($data = mysqli_fetch_assoc($query)) {
                        $fecha_formateada = date('d/m', strtotime($data['Fecha']));
                        echo "
        <tr style='font-size: 14px;'>
            <td>$fecha_formateada</td>
            <td>" . (fmod($data['inventario_inicial'], 1) == 0 ? number_format($data['inventario_inicial'], 0) : number_format($data['inventario_inicial'], 2)) . "</td>
            <td>" . (fmod($data['compras'], 1) == 0 ? number_format($data['compras'], 0) : number_format($data['compras'], 2)) . "</td>
            <td>" . (fmod($data['gastos_mes'], 1) == 0 ? number_format($data['gastos_mes'], 0) : number_format($data['gastos_mes'], 2)) . "</td>
            <td>" . (fmod($data['salarios'], 1) == 0 ? number_format($data['salarios'], 0) : number_format($data['salarios'], 2)) . "</td>
            <td>" . (fmod($data['impuestos'], 1) == 0 ? number_format($data['impuestos'], 0) : number_format($data['impuestos'], 2)) . "</td>
            <td>" . (fmod($data['cuentas_por_pagar'], 1) == 0 ? number_format($data['cuentas_por_pagar'], 0) : number_format($data['cuentas_por_pagar'], 2)) . "</td>
            <td>" . (fmod($data['total_salidas'], 1) == 0 ? number_format($data['total_salidas'], 0) : number_format($data['total_salidas'], 2)) . "</td>
            <td>" . (fmod($data['ventas'], 1) == 0 ? number_format($data['ventas'], 0) : number_format($data['ventas'], 2)) . "</td>
            <td>" . (fmod($data['inventario_final'], 1) == 0 ? number_format($data['inventario_final'], 0) : number_format($data['inventario_final'], 2)) . "</td>
            <td>" . (fmod($data['total_entradas'], 1) == 0 ? number_format($data['total_entradas'], 0) : number_format($data['total_entradas'], 2)) . "</td>
            <td>" . (fmod($data['utilidades'], 1) == 0 ? number_format($data['utilidades'], 0) : number_format($data['utilidades'], 2)) . "</td>
        </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="pagination d-flex justify-content-center mt-3">
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($pagina > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?>" aria-label="Anterior">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                        <li class="page-item <?php echo $i == $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($pagina < $total_paginas): ?>
                        <li class="page-item">
                            <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?>" aria-label="Siguiente">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </div>
    <div class="modal fade" id="modalFechas" tabindex="-1" aria-labelledby="modalFechasLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalFechasLabel">Seleccionar Rango de Fechas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="generar_pdf.php" method="get" target="_blank">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                            <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_fin" class="form-label">Fecha Fin</label>
                            <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Generar PDF</button>
                    </div>
                </form>
            </div>
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

    <?php
}
include_once "includes/footer.php";
?>

<!-- Botón-->
<div id="calc-bubble" onclick="toggleCalc()">
    <i class="fas fa-calculator"></i>
</div>

<!-- Calculadora-->
<div id="calculator">
    <input type="text" id="calc-display" readonly>
    <div class="calc-buttons">
        <button onclick="append('7')">7</button>
        <button onclick="append('8')">8</button>
        <button onclick="append('9')">9</button>
        <button onclick="append('+')">+</button>

        <button onclick="append('4')">4</button>
        <button onclick="append('5')">5</button>
        <button onclick="append('6')">6</button>
        <button onclick="append('-')">-</button>

        <button onclick="append('1')">1</button>
        <button onclick="append('2')">2</button>
        <button onclick="append('3')">3</button>
        <button onclick="append('*')">*</button>

        <button onclick="append('0')">0</button>
        <button onclick="append('.')">.</button>
        <button onclick="clearDisplay()">C</button>
        <button onclick="append('/')">/</button>

        <button class="calc-equal" onclick="calculate()">=</button>
    </div>
    <script src="../assets/js/calculator.js"></script>
    <link rel="stylesheet" href="../assets/dist/css/calculator.css">