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
$tipoPago = isset($data['tipoPago']) ? $data['tipoPago'] : 'efectivo';
$transaccion = isset($data['transaccion']) ? $data['transaccion'] : '';

$total = 0;
$detalleIDs = [];

foreach ($items as $item) {
    $subtotal = floatval($item['precio']) * intval($item['cantidad']);
    $total += $subtotal;
    $detalleIDs[] = intval($item['id']);
}

// Ahora calculamos el impuesto correctamente
$impServicio = 0;
if ($aplicarImpuesto) {
    $impServicio = $total * 0.10;
    $total += $impServicio;
}

$queryPedido = mysqli_query($conexion, "SELECT * FROM pedidos WHERE estado = 'ACTIVO' LIMIT 1");
$pedidoOriginal = mysqli_fetch_assoc($queryPedido);

if (!$pedidoOriginal) {
    echo json_encode(['success' => false, 'message' => 'No se encontró pedido activo.']);
    exit;
}

$id_sala = $pedidoOriginal['id_sala'];
$num_mesa = $pedidoOriginal['num_mesa'];
$id_usuario = $pedidoOriginal['id_usuario'];
$fecha = date('Y-m-d H:i:s');

$insert = mysqli_query($conexion, "INSERT INTO pedidos (id_sala, num_mesa, fecha, total, estado, id_usuario, ImpServicio, tipoPago, transaccion)
VALUES ('$id_sala', '$num_mesa', '$fecha', '$total', 'FINALIZADO', '$id_usuario', '$impServicio', '$tipoPago', '$transaccion')");

$id_nuevo_pedido = mysqli_insert_id($conexion);

foreach ($detalleIDs as $idDetalle) {
    mysqli_query($conexion, "UPDATE detalle_pedidos SET id_pedido = '$id_nuevo_pedido' WHERE id = '$idDetalle'");
}

$queryTotal = mysqli_query($conexion, "
    SELECT SUM(cantidad * precio) AS subtotal 
    FROM detalle_pedidos 
    WHERE id_pedido = '{$pedidoOriginal['id']}'");

$rowTotal = mysqli_fetch_assoc($queryTotal);
$nuevo_total = $rowTotal['subtotal'];

if (!$nuevo_total) {
    mysqli_query($conexion, "UPDATE pedidos SET estado = 'FINALIZADO' WHERE id = '{$pedidoOriginal['id']}'");
    mysqli_query($conexion, "UPDATE mesas SET estado = 'DISPONIBLE' WHERE id_sala = '$id_sala' AND num_mesa = '$num_mesa'");
} else {
    mysqli_query($conexion, "UPDATE pedidos SET total = '$nuevo_total' WHERE id = '{$pedidoOriginal['id']}'");
}

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', '', 9);
        $this->Cell(58, 6, utf8_decode('Restaurante San Isidro'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(58, 6, utf8_decode('Teléfono: +506 8937-7531'), 0, 1, 'C');
        $this->Cell(58, 6, utf8_decode('Puriscal - San José, Costa Rica'), 0, 1, 'C');
        $this->Cell(58, 6, '---------------------------------', 0, 1, 'C');
        $this->Ln(1);
    }
}

$pdf = new PDF('P', 'mm', array(80, 150 + (count($items) * 6) + 60));
$pdf->AddPage();
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(58, 6, "ID #: " . str_pad($id_nuevo_pedido, 6, "0", STR_PAD_LEFT), 0, 1, 'C');
$pdf->Cell(58, 6, "Mesa: $num_mesa", 0, 1, 'C');
$pdf->Cell(58, 6, "Fecha: " . $fecha, 0, 1, 'C');
$pdf->Cell(58, 6, "---------------------------------", 0, 1, 'C');

$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(25, 5, utf8_decode('Descripción'), 0);
$pdf->Cell(10, 5, 'Cant', 0, 0, 'C');
$pdf->Cell(10, 5, 'P.U', 0, 0, 'C');
$pdf->Cell(13, 5, 'Total', 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');

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
    $pdf->Cell(10, 5, number_format($precio, 0), 0, 0, 'C');
    $pdf->Cell(13, 5, number_format($subtotal, 0), 0, 1, 'C');
}

$pdf->Cell(58, 5, "---------------------------------", 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 6, 'TOTAL:', 0);
$pdf->Cell(28, 6, number_format($total - $impServicio, 0), 0, 1, 'R');

if ($aplicarImpuesto) {
    $pdf->Cell(30, 6, 'Imp. Servicio:', 0);
    $pdf->Cell(28, 6, number_format($impServicio, 0), 0, 1, 'R');
}

$pdf->Cell(30, 6, 'TOTAL FINAL:', 0);
$pdf->Cell(28, 6, number_format($total, 0), 0, 1, 'R');

$pdf->Ln(2);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(58, 5, "Pago: " . ucfirst($tipoPago), 0, 1, 'C');
if ($tipoPago === 'tarjeta' && $transaccion) {
    $pdf->Cell(58, 5, "Transaccion: $transaccion", 0, 1, 'C');
}
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

$pdf->Output("F", "Factura_Seleccionada.pdf");

$response = [
    'success' => true,
    'message' => 'Factura dividida y registrada correctamente.',
    'transaccion' => $transaccion,
    'tipoPago' => $tipoPago,
];

echo json_encode($response);
