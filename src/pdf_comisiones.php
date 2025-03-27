<?php
require_once "../conexion.php";
require(__DIR__ . '/fpdf/fpdf.php');

$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$usuario_seleccionado = isset($_GET['usuario']) ? $_GET['usuario'] : '';

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

// Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Título
$pdf->Cell(0, 10, 'Reporte de Ingresos - Fecha: ' . $fecha_seleccionada, 0, 1, 'C');
$pdf->Ln(10);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Mesero', 1, 0, 'C');
$pdf->Cell(60, 10, 'Total Pedidos (₡)', 1, 0, 'C');
$pdf->Cell(60, 10, 'Ingresos Servicio (₡)', 1, 1, 'C');

// Datos de la tabla
$pdf->SetFont('Arial', '', 12);
while ($row = mysqli_fetch_assoc($query_ingresos)) {
    $pdf->Cell(60, 10, $row['mesero'], 1, 0, 'C');
    $pdf->Cell(60, 10, number_format($row['total_pedidos'], 0, '', '.'), 1, 0, 'C');
    $pdf->Cell(60, 10, number_format($row['ingresos_servicio'], 0, '', '.'), 1, 1, 'C');
}

// Salida del PDF
$pdf->Output('D', 'Reporte_Ingresos_' . $fecha_seleccionada . '.pdf');
?>