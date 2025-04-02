<?php
session_start();
require_once "../conexion.php";
require("fpdf/fpdf.php");

date_default_timezone_set('America/Costa_Rica');

// Parámetros
$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$usuario_seleccionado = isset($_GET['usuario']) ? $_GET['usuario'] : '';

$where_clause = "WHERE DATE(p.fecha) = '$fecha_seleccionada'";
if (!empty($usuario_seleccionado)) {
    $where_clause .= " AND p.id_usuario = '$usuario_seleccionado'";
}

$query = mysqli_query($conexion, "
    SELECT 
        u.nombre AS mesero, 
        SUM(p.total) AS total_pedidos,
        SUM(p.ImpServicio) AS ingresos_servicio
    FROM pedidos p
    INNER JOIN usuarios u ON p.id_usuario = u.id
    $where_clause
    GROUP BY u.id
");
class PDF extends FPDF
{
    public $startX;

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 10, utf8_decode("Generado el: ") . date('d/m/Y H:i:s'), 0, 0, 'C');
    }
    function TableHeader()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor(30, 58, 138);
        $this->SetTextColor(255, 255, 255);
        $this->SetX($this->startX);
        $this->Cell(80, 10, 'Mesero', 1, 0, 'C', true);
        $this->Cell(60, 10, 'Total Pedidos', 1, 0, 'C', true);
        $this->Cell(60, 10, 'Ingresos Servicio', 1, 1, 'C', true);
    }
}
$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

$tabla_ancho = 80 + 60 + 60;
$pdf->startX = ($pdf->GetPageWidth() - $tabla_ancho) / 2;

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 58, 138);
$pdf->Cell(0, 10, utf8_decode("Reporte de Ingresos - Fecha: ") . date('d/m/Y', strtotime($fecha_seleccionada)), 0, 1, 'C');
$pdf->Ln(8);

$pdf->TableHeader();

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);

$total_pedidos = 0;
$total_servicio = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->SetX($pdf->startX);
    $pdf->Cell(80, 10, utf8_decode($row['mesero']), 1, 0, 'C');
    $pdf->Cell(60, 10, number_format($row['total_pedidos'], 0, '', '.'), 1, 0, 'C');
    $pdf->Cell(60, 10, number_format($row['ingresos_servicio'], 0, '', '.'), 1, 1, 'C');

    $total_pedidos += $row['total_pedidos'];
    $total_servicio += $row['ingresos_servicio'];
}
// Totales final
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX($pdf->startX);
$pdf->Cell(80, 10, 'Totales:', 1, 0, 'L', true);
$pdf->Cell(60, 10, number_format($total_pedidos, 0, '', '.'), 1, 0, 'C', true);
$pdf->Cell(60, 10, number_format($total_servicio, 0, '', '.'), 1, 1, 'C', true);

$pdf->Output('D', 'Reporte_Ingresos_' . $fecha_seleccionada . '.pdf');
?>