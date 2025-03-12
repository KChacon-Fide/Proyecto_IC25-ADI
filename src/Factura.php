<?php
require(__DIR__ . '/fpdf/fpdf.php');
include "../conexion.php";

if (!isset($_GET['id_pedido'])) {
    die("Faltan parámetros en la URL.");
}

$id_pedido = $_GET['id_pedido'];
$queryPedido = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id = '$id_pedido'");
$pedido = mysqli_fetch_assoc($queryPedido);

if (!$pedido) {
    die("El pedido no existe.");
}

$queryDetalle = mysqli_query($conexion, "SELECT * FROM detalle_pedidos WHERE id_pedido = '$id_pedido'");
$lineHeight = 6;
$numRows = mysqli_num_rows($queryDetalle);
$ticketHeight = 130 + ($numRows * $lineHeight) + 60; // Ajustado para incluir espacios correctos

$pdf = new FPDF('P', 'mm', array(80, $ticketHeight));
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(58, 6, 'Restaurante San Isidro', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(58, 6, 'Telefono: +506 8937-7531', 0, 1, 'C');
$pdf->Cell(58, 6, 'Puriscal - San Jose, Costa Rica', 0, 1, 'C');
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');
$pdf->Ln(1);

$pdf->Cell(58, 6, "Factura #: " . str_pad($id_pedido, 6, "0", STR_PAD_LEFT), 0, 1, 'C');
$pdf->Cell(58, 6, "Mesa: " . $pedido['num_mesa'], 0, 1, 'C');
$pdf->Cell(58, 6, "Fecha: " . $pedido['fecha'], 0, 1, 'C');
$pdf->Cell(58, 6, "---------------------------------", 0, 1, 'C');
$pdf->Ln(1);

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, 'Descripcion', 0);
$pdf->Cell(10, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(10, 5, 'P.U', 0, 0, 'C');
$pdf->Cell(13, 5, 'Total', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

$total = 0;
while ($detalle = mysqli_fetch_assoc($queryDetalle)) {
    $subtotal = $detalle['cantidad'] * $detalle['precio'];
    $pdf->Cell(25, 5, utf8_decode($detalle['nombre']), 0);
    $pdf->Cell(10, 5, $detalle['cantidad'], 0, 0, 'C');
    $pdf->Cell(10, 5, '$' . number_format($detalle['precio'], 0), 0, 0, 'C');
    $pdf->Cell(13, 5, '$' . number_format($subtotal, 0), 0, 1, 'C');
    $total += $subtotal;
}

$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'TOTAL:', 0);
$pdf->Cell(28, 6, '$' . number_format($total, 0), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "Pago: Efectivo", 0, 1, 'C');
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');
$pdf->Ln(5);

$qr_file = __DIR__ . '/../assets/img/qr/qr_code.png';

if (file_exists($qr_file)) {
    $pdf->Image($qr_file, 30, $pdf->GetY(), 20, 20);
    $pdf->Ln(25);
} else {
    $pdf->Cell(58, 5, "QR no disponible", 0, 1, 'C');
}

$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(58, 6, 'Gracias por su compra', 0, 1, 'C');
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');

$pdf->Output("Factura_Pedido_$id_pedido.pdf", 'I');
?>