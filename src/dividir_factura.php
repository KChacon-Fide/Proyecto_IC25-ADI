<?php
header('Content-Type: application/json; charset=utf-8');
require(__DIR__ . '/fpdf/fpdf.php');
include "../conexion.php";
mysqli_set_charset($conexion, "utf8");

date_default_timezone_set('America/Costa_Rica');

$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos o no hay elementos seleccionados.']);
    exit;
}

$items = $data['items'];
$aplicarImpuesto = isset($data['aplicarImpuesto']) && $data['aplicarImpuesto'] == true;
$tipoPago = isset($data['tipoPago']) ? ucfirst($data['tipoPago']) : 'Desconocido';

$id_pedido = rand(1, 999999);
$mesa = "Mesa 1";
$fecha_actual = date("Y-m-d H:i:s");

$lineHeight = 6;
$numRows = count($items);
$ticketHeight = 150 + ($numRows * $lineHeight) + 60;

$pdf = new FPDF('P', 'mm', array(80, $ticketHeight));
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// Encabezado
$pdf->Cell(58, 6, utf8_decode('Restaurante San Isidro'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(58, 6, utf8_decode('Teléfono: +506 8937-7531'), 0, 1, 'C');
$pdf->Cell(58, 6, utf8_decode('Puriscal - San José, Costa Rica'), 0, 1, 'C');
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');
$pdf->Ln(1);

// Info
$pdf->Cell(58, 6, "ID #: " . str_pad($id_pedido, 6, "0", STR_PAD_LEFT), 0, 1, 'C');
$pdf->Cell(58, 6, $mesa, 0, 1, 'C');
$pdf->Cell(58, 6, "Fecha: " . $fecha_actual, 0, 1, 'C');
$pdf->Cell(58, 6, "---------------------------------", 0, 1, 'C');

// Encabezado de tabla
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, utf8_decode('Descripción'), 0);
$pdf->Cell(10, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(10, 5, 'P.U', 0, 0, 'C');
$pdf->Cell(13, 5, 'Total', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

$total = 0;

// Listado de productos
foreach ($items as $item) {
    $id = intval($item['id']);
    $cantidad = intval($item['cantidad']);
    $precio = floatval($item['precio']);
    $subtotal = $cantidad * $precio;

    $query = mysqli_query($conexion, "SELECT nombre FROM detalle_pedidos WHERE id = $id");
    $detalle = mysqli_fetch_assoc($query);
    $nombre = $detalle ? $detalle['nombre'] : 'Producto';

    $pdf->Cell(25, 5, utf8_decode($nombre), 0);
    $pdf->Cell(10, 5, $cantidad, 0, 0, 'C');
    $pdf->Cell(10, 5, '' . number_format($precio, 0), 0, 0, 'C');
    $pdf->Cell(13, 5, '' . number_format($subtotal, 0), 0, 1, 'C');
    $total += $subtotal;
}

$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

// Totales
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'TOTAL:', 0);
$pdf->Cell(28, 6, '' . number_format($total, 0), 0, 1, 'R');

$impServicio = 0;
if ($aplicarImpuesto) {
    $impServicio = $total * 0.10;
    $pdf->Cell(30, 6, 'Imp. Servicio:', 0);
    $pdf->Cell(28, 6, '' . number_format($impServicio, 0), 0, 1, 'R');
    $total += $impServicio;
}

$pdf->Cell(30, 6, 'TOTAL FINAL:', 0);
$pdf->Cell(28, 6, '' . number_format($total, 0), 0, 1, 'R');

$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "Pago: " . $tipoPago, 0, 1, 'C');
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

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

// Guardar PDF
$pdf->Output("Factura_Seleccionada.pdf", 'F');

echo json_encode(['success' => true, 'message' => 'Factura generada correctamente.']);
