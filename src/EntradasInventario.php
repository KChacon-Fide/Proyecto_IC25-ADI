<?php
require_once "fpdf/fpdf.php";
require_once "../conexion.php";

date_default_timezone_set('America/Costa_Rica');

class PDF extends FPDF
{
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(80, 80, 80);
        $fecha_hora = date('d/m/Y H:i:s');
        $this->Cell(0, 10, utf8_decode("Generado el: $fecha_hora"), 0, 0, 'C');
    }
}

$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$fechaInicioMes = date('Y-m-01 00:00:00', strtotime($fecha));
$fechaFinMes = date('Y-m-t 23:59:59', strtotime($fecha));

$pdf = new PDF('P', 'mm', 'A4');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

// Título
$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 58, 138); // Azul elegante
$pdf->Cell(0, 10, utf8_decode('Reporte de Entradas de Inventario'), 0, 1, 'C');
$pdf->Ln(8);

// Encabezados de la tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(30, 58, 138);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(80, 10, 'Producto', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Cantidad', 1, 1, 'C', true);

// Contenido
$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);
$total_entradas = 0;

$query = mysqli_query($conexion, "
    SELECT b.nombre, i.cantidad, i.fecha
    FROM inventario i
    JOIN bebidas b ON i.id_bebida = b.id
    WHERE i.fecha BETWEEN '$fechaInicioMes' AND '$fechaFinMes'
    ORDER BY i.fecha ASC
");

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->Cell(80, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(50, 10, date('d/m/Y H:i', strtotime($row['fecha'])), 1, 0, 'C');
    $pdf->Cell(40, 10, number_format($row['cantidad'], 0), 1, 1, 'C');
    $total_entradas += $row['cantidad'];
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->Cell(130, 10, 'Total Productos Ingresados:', 1, 0, 'L', true); // 80 + 50
$pdf->Cell(40, 10, number_format($total_entradas, 0), 1, 1, 'C', true);

$pdf->Output("I", "EntradasInventario.pdf");
?>