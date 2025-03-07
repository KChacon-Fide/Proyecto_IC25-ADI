<?php
require(__DIR__ . '/fpdf/fpdf.php');
include "../conexion.php";

// Validar que se recibe un ID de pedido
if (!isset($_GET['id_pedido'])) {
    die("Faltan parámetros en la URL.");
}

$id_pedido = $_GET['id_pedido'];

// Obtener los datos del pedido
$queryPedido = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id = '$id_pedido'");
$pedido = mysqli_fetch_assoc($queryPedido);

if (!$pedido) {
    die("El pedido no existe.");
}

// Obtener detalles del pedido
$queryDetalle = mysqli_query($conexion, "SELECT * FROM detalle_pedidos WHERE id_pedido = '$id_pedido'");

// **📌 Calcular la altura dinámica**
$lineHeight = 6;
$numRows = mysqli_num_rows($queryDetalle);
$ticketHeight = 130 + ($numRows * $lineHeight) + 60; // Ajustado para incluir espacios correctos

// **📌 Crear PDF con tamaño dinámico**
$pdf = new FPDF('P', 'mm', array(80, $ticketHeight));
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// **📌 Encabezado**
$pdf->Cell(58, 6, 'Restaurante San Isidro', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(58, 6, 'Telefono: +506 8937-7531', 0, 1, 'C');
$pdf->Cell(58, 6, 'Puriscal - San Jose, Costa Rica', 0, 1, 'C');
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');
$pdf->Ln(1);

// **📌 Información del pedido**
$pdf->Cell(58, 6, "Factura #: " . str_pad($id_pedido, 6, "0", STR_PAD_LEFT), 0, 1, 'C');
$pdf->Cell(58, 6, "Mesa: " . $pedido['num_mesa'], 0, 1, 'C');
$pdf->Cell(58, 6, "Fecha: " . $pedido['fecha'], 0, 1, 'C');
$pdf->Cell(58, 6, "---------------------------------", 0, 1, 'C');
$pdf->Ln(1);

// **📌 Encabezado de la tabla**
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, 'Descripcion', 0);
$pdf->Cell(10, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(10, 5, 'P.U', 0, 0, 'C');
$pdf->Cell(13, 5, 'Total', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

// **📌 Contenido de la tabla**
$total = 0;
while ($detalle = mysqli_fetch_assoc($queryDetalle)) {
    $subtotal = $detalle['cantidad'] * $detalle['precio'];
    $pdf->Cell(25, 5, utf8_decode($detalle['nombre']), 0);
    $pdf->Cell(10, 5, $detalle['cantidad'], 0, 0, 'C');
    $pdf->Cell(10, 5, '$' . number_format($detalle['precio'], 2), 0, 0, 'C');
    $pdf->Cell(13, 5, '$' . number_format($subtotal, 2), 0, 1, 'C');
    $total += $subtotal;
}

// **📌 Línea separadora**
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

// **📌 Total y forma de pago**
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'TOTAL:', 0);
$pdf->Cell(28, 6, '$' . number_format($total, 2), 0, 1, 'R');
$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "Pago: Efectivo", 0, 1, 'C');
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');
$pdf->Ln(5);

// **📌 Cargar imagen del QR y centrarla correctamente**
$qr_file = __DIR__ . '/../assets/img/qr/qr_code.png'; 

if (file_exists($qr_file)) {
    $pdf->Image($qr_file, 30, $pdf->GetY(), 20, 20); // Moví el QR más a la derecha y ajusté tamaño
    $pdf->Ln(25); // Agregué más espacio después del QR para que no quede pegado
} else {
    $pdf->Cell(58, 5, "QR no disponible", 0, 1, 'C');
}

// **📌 Pie de página**
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(58, 6, 'Gracias por su compra', 0, 1, 'C'); // Ahora sí bien separado del QR
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');

// **📌 Generar PDF**
$pdf->Output("Factura_Pedido_$id_pedido.pdf", 'I');
?>
