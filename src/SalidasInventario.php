<?php
require_once "fpdf/fpdf.php";
require_once "../conexion.php";

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);

// Encabezado
$pdf->Cell(0, 10, utf8_decode('Reporte de Salidas de Inventario'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(50, 10, 'Producto', 1);
$pdf->Cell(30, 10, 'Cantidad', 1);
$pdf->Cell(35, 10, 'Precio Unitario', 1);
$pdf->Cell(35, 10, 'Total Ganado', 1);
$pdf->Cell(40, 10, 'Fecha', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

$total_salidas = 0;
$total_ganado = 0;

$query = mysqli_query($conexion, "
    SELECT i.id_bebida, b.nombre, ih.cantidad AS anterior, i.cantidad AS actual, i.precio, ih.fecha 
    FROM inventario i
    JOIN inventario_historial ih ON i.id_bebida = ih.id_bebida
    JOIN bebidas b ON i.id_bebida = b.id
    WHERE i.cantidad < ih.cantidad
");

while ($row = mysqli_fetch_assoc($query)) {
    $cantidad_salida = $row['anterior'] - $row['actual'];
    $precio_unitario = $row['precio'];
    $total = $precio_unitario * $cantidad_salida;

    $pdf->Cell(50, 10, utf8_decode($row['nombre']), 1);
    $pdf->Cell(30, 10, number_format($cantidad_salida, 2), 1);
    $pdf->Cell(35, 10, '₡' . number_format($precio_unitario, 2), 1);
    $pdf->Cell(35, 10, '₡' . number_format($total, 2), 1);
    $pdf->Cell(40, 10, date('d/m/Y H:i', strtotime($row['fecha'])), 1);
    $pdf->Ln();

    $total_salidas += $cantidad_salida;
    $total_ganado += $total;
}

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(50, 10, 'Cantidad Vendida:', 1);
$pdf->Cell(30, 10, number_format($total_salidas, 2), 1);
$pdf->Cell(35, 10, '', 1);
$pdf->Cell(35, 10, '₡' . number_format($total_ganado, 2), 1);
$pdf->Cell(40, 10, '', 1);

$pdf->Output("I", "SalidasInventario.pdf");
?>