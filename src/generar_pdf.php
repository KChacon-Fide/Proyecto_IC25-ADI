<?php
session_start();
require_once "fpdf/fpdf.php";
include "../conexion.php";

date_default_timezone_set('America/Costa_Rica');

class PDF extends FPDF
{
    public $startX;

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 9);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 10, utf8_decode("Generado el: " . date('d/m/Y H:i:s')), 0, 0, 'C');
    }

    function TableHeader($header)
    {
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(30, 58, 138);
        $this->SetTextColor(255, 255, 255);
        $this->SetX($this->startX);
        foreach ($header as $col) {
            $this->Cell(22, 10, utf8_decode($col), 1, 0, 'C', true);
        }
        $this->Ln();
    }
}

// Validación de fechas
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

if (!$fecha_inicio || !$fecha_fin) {
    die('Error: Debe proporcionar un rango de fechas válido.');
}

// Consulta a la base de datos
$query = mysqli_query($conexion, "
    SELECT * FROM repo_financiero 
    WHERE Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
");

$pdf = new PDF('L', 'mm', 'A4');
$pdf->SetMargins(20, 20, 20);
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->SetTextColor(30, 58, 138);
$pdf->Cell(0, 10, utf8_decode('Reporte Financiero'), 0, 1, 'C');
$pdf->Ln(8);

$header = [
    'Fecha',
    'Inv Inicial',
    'Compras',
    'Gastos',
    'Salarios',
    'Impuestos',
    'Ctas x Pagar',
    'Ttl Salidas',
    'Ventas',
    'Inv Final',
    'Ttl Entradas',
    'Utilidades'
];

$pdf->startX = ($pdf->GetPageWidth() - (count($header) * 22)) / 2;
$pdf->TableHeader($header);
$pdf->SetFont('Arial', '', 9);
$pdf->SetTextColor(0);
$total_utilidades = 0;

while ($row = mysqli_fetch_assoc($query)) {
    $pdf->SetX($pdf->startX);
    $pdf->Cell(22, 10, date('d/m/Y', strtotime($row['Fecha'])), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['inventario_inicial'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['compras'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['gastos_mes'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['salarios'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['impuestos'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['cuentas_por_pagar'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['total_salidas'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['ventas'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['inventario_final'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['total_entradas'], 2), 1, 0, 'C');
    $pdf->Cell(22, 10, number_format($row['utilidades'], 2), 1, 1, 'C');

    $total_utilidades += $row['utilidades'];
}

// Total final
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetX($pdf->startX);
$pdf->Cell(22 * 11, 10, 'Total Utilidades', 1, 0, 'L', true);
$pdf->Cell(22, 10, number_format($total_utilidades, 2), 1, 1, 'C', true);

$pdf->Output('I', 'Reporte_Financiero.pdf');
?>