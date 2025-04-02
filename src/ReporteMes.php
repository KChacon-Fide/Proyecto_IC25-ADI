<?php
session_start();
require_once "fpdf/fpdf.php";
include "../conexion.php";

date_default_timezone_set('America/Costa_Rica');

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
$pdf->Cell(0, 10, utf8_decode('Reporte de Órdenes del Mes'), 0, 1, 'C');
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(30, 58, 138);
$pdf->SetTextColor(255, 255, 255);

$startX = ($pdf->GetPageWidth() - (25 + 60 + 25 + 60 + 60 + 35)) / 2;
$pdf->SetX($startX);
$pdf->Cell(25, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Sala', 1, 0, 'C', true);
$pdf->Cell(25, 10, 'Mesa', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(60, 10, 'Usuario', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Total (Colon)', 1, 1, 'C', true);

$inicio_mes = date('Y-m-01 00:00:00');
$fin_mes = date('Y-m-t 23:59:59');

// Consulta
$query = mysqli_query($conexion, "
    SELECT p.*, s.nombre AS sala, u.nombre 
    FROM pedidos p 
    INNER JOIN salas s ON p.id_sala = s.id 
    INNER JOIN usuarios u ON p.id_usuario = u.id 
    WHERE p.fecha BETWEEN '$inicio_mes' AND '$fin_mes'
");

$pdf->SetFont('Arial', '', 11);
$pdf->SetTextColor(0);
$total_mes = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->SetX($startX);
    $pdf->Cell(25, 10, $row['id'], 1, 0, 'C');
    $pdf->Cell(60, 10, utf8_decode($row['sala']), 1, 0, 'C');
    $pdf->Cell(25, 10, $row['num_mesa'], 1, 0, 'C');
    $pdf->Cell(60, 10, date('d/m/Y H:i', strtotime($row['fecha'])), 1, 0, 'C');
    $pdf->Cell(60, 10, utf8_decode($row['nombre']), 1, 0, 'C');
    $pdf->Cell(35, 10, number_format($row['total'], 0, '', '.'), 1, 1, 'C');

    $total_mes += $row['total'];
}

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX($startX);
$pdf->Cell(230, 10, utf8_decode('Total del Mes:'), 1, 0, 'L', true); // 25+60+25+60+60
$pdf->Cell(35, 10, number_format($total_mes, 0, '', '.'), 1, 1, 'C', true);

$pdf->Output('I', 'Reporte Ordenes Mes.pdf');
?>