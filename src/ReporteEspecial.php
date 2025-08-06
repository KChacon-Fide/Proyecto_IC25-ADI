<?php
session_start();
require_once "fpdf/fpdf.php";
include "../conexion.php";

date_default_timezone_set('America/Costa_Rica');

$usuario = $_GET['usuario'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

$where = [];
if ($usuario)
    $where[] = "p.id_usuario = '" . mysqli_real_escape_string($conexion, $usuario) . "'";
if ($from_date)
    $where[] = "DATE(p.fecha) >= '" . mysqli_real_escape_string($conexion, $from_date) . "'";
if ($to_date)
    $where[] = "DATE(p.fecha) <= '" . mysqli_real_escape_string($conexion, $to_date) . "'";

$where_sql = count($where)
    ? "WHERE " . implode(" AND ", $where)
    : "";

class PDF extends FPDF
{
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 10, utf8_decode("Generado el: " . date('d/m/Y H:i:s')), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 58, 138);
$title = 'Reporte Especial de Órdenes';
if ($from_date && $to_date) {
    $title .= " ({$from_date} a {$to_date})";
} elseif ($from_date) {
    $title .= " Desde {$from_date}";
} elseif ($to_date) {
    $title .= " Hasta {$to_date}";
}
$pdf->Cell(0, 10, utf8_decode($title), 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(30, 58, 138);
$pdf->SetTextColor(255, 255, 255);

$cols = [
    ['w' => 20, 't' => 'ID'],
    ['w' => 50, 't' => 'Sala'],
    ['w' => 20, 't' => 'Mesa'],
    ['w' => 50, 't' => 'Fecha'],
    ['w' => 40, 't' => utf8_decode('Nº Transacción')],
    ['w' => 40, 't' => 'Usuario'],
    ['w' => 45, 't' => 'Total (Colon)']
];

$startX = ($pdf->GetPageWidth() - array_sum(array_column($cols, 'w'))) / 2;
$pdf->SetX($startX);
foreach ($cols as $c) {
    $pdf->Cell($c['w'], 10, $c['t'], 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);
$total_all = 0;

$sql = "
  SELECT p.id,p.fecha,p.transaccion,p.total,
         s.nombre AS sala,
         p.num_mesa,
         u.nombre AS usuario
  FROM pedidos p
  JOIN salas   s ON p.id_sala   = s.id
  JOIN usuarios u ON p.id_usuario = u.id
  $where_sql
  ORDER BY p.fecha DESC
";
$q = mysqli_query($conexion, $sql);

while ($r = mysqli_fetch_assoc($q)) {
    $pdf->SetX($startX);
    $pdf->Cell(20, 8, $r['id'], 1, 0, 'C');
    $pdf->Cell(50, 8, utf8_decode($r['sala']), 1, 0, 'C');
    $pdf->Cell(20, 8, $r['num_mesa'], 1, 0, 'C');
    $pdf->Cell(50, 8, date('d/m/Y H:i', strtotime($r['fecha'])), 1, 0, 'C');
    $pdf->Cell(40, 8, !empty($r['transaccion']) ? $r['transaccion'] : '-', 1, 0, 'C');
    $pdf->Cell(40, 8, utf8_decode($r['usuario']), 1, 0, 'C');
    $pdf->Cell(45, 8, number_format($r['total'], 0, '', '.'), 1, 1, 'C');
    $total_all += $r['total'];
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX($startX);
$sumWidth = array_sum(array_column($cols, 'w')) - 45;
$pdf->Cell($sumWidth, 10, utf8_decode('Total General:'), 1, 0, 'L', true);
$pdf->Cell(45, 10, number_format($total_all, 0, '', '.'), 1, 1, 'C', true);

$pdf->Output('I', 'Reporte_Especial.pdf');
?>