<?php
session_start();
require(__DIR__ . '/fpdf/fpdf.php');
include "../conexion.php";

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 15, utf8_decode('REPORTE DE ÓRDENES DEL MES'), 0, 1, 'C');
        $this->Ln(8);

        $this->SetX(27);

        $this->SetFillColor(200, 200, 200);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(25, 12, 'ID', 1, 0, 'C', true);
        $this->Cell(50, 12, 'Sala', 1, 0, 'C', true);
        $this->Cell(25, 12, 'Mesa', 1, 0, 'C', true);
        $this->Cell(55, 12, 'Fecha', 1, 0, 'C', true);
        $this->Cell(35, 12, utf8_decode('Total (¢)'), 1, 0, 'C', true);
        $this->Cell(50, 12, 'Usuario', 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Generado el: ' . date('d/m/Y H:i:s'), 0, 0, 'C');
    }
}

$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

$fecha_inicio_mes = date('Y-m-01');
$fecha_fin_mes = date('Y-m-t');

$query = mysqli_query($conexion, "SELECT p.*, s.nombre AS sala, u.nombre FROM pedidos p 
    INNER JOIN salas s ON p.id_sala = s.id 
    INNER JOIN usuarios u ON p.id_usuario = u.id 
    WHERE DATE(p.fecha) BETWEEN '$fecha_inicio_mes' AND '$fecha_fin_mes'");

$rowHeight = 12;

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->SetX(27);
    $pdf->Cell(25, $rowHeight, $row['id'], 1, 0, 'C');
    $pdf->Cell(50, $rowHeight, utf8_decode($row['sala']), 1, 0, 'C');
    $pdf->Cell(25, $rowHeight, $row['num_mesa'], 1, 0, 'C');
    $pdf->Cell(55, $rowHeight, $row['fecha'], 1, 0, 'C');
    $pdf->Cell(35, $rowHeight, '' . number_format($row['total'], 0, '', '.'), 1, 0, 'C');
    $pdf->Cell(50, $rowHeight, utf8_decode($row['nombre']), 1, 1, 'C');
}

$pdf->Output('I', 'Historial Ordenes Mes.pdf');
?>