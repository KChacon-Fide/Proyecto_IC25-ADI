<?php
header('Content-Type: application/json; charset=utf-8');
require(__DIR__ . '/fpdf/fpdf.php');
include "../conexion.php";

mysqli_set_charset($conexion, "utf8");

// Obtener los datos enviados desde el cliente
$data = json_decode(file_get_contents("php://input"), true);

if (!$data || empty($data['items'])) {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos o no hay elementos seleccionados.']);
    exit;
}

$items = $data['items'];
$aplicarImpuesto = isset($data['aplicarImpuesto']) && $data['aplicarImpuesto'] == true;

// Configuración del ticket
$lineHeight = 6;
$numRows = count($items);
$ticketHeight = 130 + ($numRows * $lineHeight) + 60;

$pdf = new FPDF('P', 'mm', array(80, $ticketHeight));
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

// Encabezado del ticket
$pdf->Cell(58, 6, utf8_decode('Restaurante San Isidro'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(58, 6, utf8_decode('Teléfono: +506 8937-7531'), 0, 1, 'C');
$pdf->Cell(58, 6, utf8_decode('Puriscal - San José, Costa Rica'), 0, 1, 'C');
$pdf->Cell(58, 6, '---------------------------------', 0, 1, 'C');
$pdf->Ln(1);

// Detalles de los productos seleccionados
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, utf8_decode('Descripción'), 0);
$pdf->Cell(10, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(10, 5, 'P.U', 0, 0, 'C');
$pdf->Cell(13, 5, 'Total', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

$totalFactura = 0;
$subtotalSeleccionado = 0;

foreach ($items as $item) {
    $id = intval($item['id']);
    $cantidad = intval($item['cantidad']);
    $precio = floatval($item['precio']);
    $subtotal = $cantidad * $precio;

    // Obtener el nombre del producto desde la base de datos
    $query = mysqli_query($conexion, "SELECT nombre FROM detalle_pedidos WHERE id = $id");
    $detalle = mysqli_fetch_assoc($query);

    $pdf->Cell(25, 5, utf8_decode($detalle['nombre']), 0);
    $pdf->Cell(10, 5, $cantidad, 0, 0, 'C');
    $pdf->Cell(10, 5, '$' . number_format($precio, 2), 0, 0, 'C');
    $pdf->Cell(13, 5, '$' . number_format($subtotal, 2), 0, 1, 'C');
    $subtotalSeleccionado += $subtotal;
}

$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

// Calcular el impuesto si está activo
$impServicio = 0;
if ($aplicarImpuesto) {
    $impServicio = $subtotalSeleccionado * 0.10; // 10% del subtotal seleccionado
    $totalFactura = $subtotalSeleccionado + $impServicio;

    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 6, utf8_decode('Imp. Servicio (10%):'), 0);
    $pdf->Cell(28, 6, '$' . number_format($impServicio, 2), 0, 1, 'R');
} else {
    $totalFactura = $subtotalSeleccionado;
}

// Total final
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'TOTAL:', 0);
$pdf->Cell(28, 6, '$' . number_format($totalFactura, 2), 0, 1, 'R');

$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');
$pdf->Cell(58, 5, utf8_decode('Gracias por su compra'), 0, 1, 'C');
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

// Guardar el PDF
$pdf->Output("Factura_Seleccionada.pdf", 'F');

// Respuesta al cliente
echo json_encode(['success' => true, 'message' => 'Factura generada correctamente.']);
?>