<?php
require('fpdf/fpdf.php');
include "../conexion.php";

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Título
        $this->Cell(0, 10, 'Reporte Financiero', 0, 1, 'C');
        // Salto de línea
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    // Tabla simple
    function BasicTable($header, $data, $total_utilidades)
    {
        // Cabecera
        foreach ($header as $col) {
            $this->Cell(22, 7, $col, 1);
        }
        $this->Ln();
        // Datos
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->Cell(22, 6, $col, 1);
            }
            $this->Ln();
        }
        // Total de utilidades
        $this->Cell(242, 6, 'Total Utilidades', 1);
        $this->Cell(22, 6, number_format($total_utilidades, 2), 1);
    }
}

// Recibir las fechas del formulario
$fecha_inicio = isset($_GET['fecha_inicio']) ? $_GET['fecha_inicio'] : null;
$fecha_fin = isset($_GET['fecha_fin']) ? $_GET['fecha_fin'] : null;

// Validar que las fechas estén definidas
if ($fecha_inicio && $fecha_fin) {
    $query = mysqli_query($conexion, "SELECT * FROM repo_financiero WHERE Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'");
} else {
    die('Error: Debe proporcionar un rango de fechas válido.');
}

// Creación del objeto de la clase heredada con orientación horizontal
$pdf = new PDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

// Títulos de las columnas
$header = array('Fecha', 'Inv Inicial', 'Compras', 'Gastos', 'Salarios', 'Impuestos', 'Cuentas x Pagar', 'Ttl Salidas', 'Ventas', 'Inv Final', 'Ttl Entradas', 'Utilidades');

// Carga de datos
$data = array();
$total_utilidades = 0;
while ($row = mysqli_fetch_assoc($query)) {
    $fecha_formateada = date('d/m/Y', strtotime($row['Fecha']));
    $data[] = array(
        strtoupper($fecha_formateada),
        number_format($row['inventario_inicial'], 2),
        number_format($row['compras'], 2),
        number_format($row['gastos_mes'], 2),
        number_format($row['salarios'], 2),
        number_format($row['impuestos'], 2),
        number_format($row['cuentas_por_pagar'], 2),
        number_format($row['total_salidas'], 2),
        number_format($row['ventas'], 2),
        number_format($row['inventario_final'], 2),
        number_format($row['total_entradas'], 2),
        number_format($row['utilidades'], 2)
    );
    $total_utilidades += $row['utilidades'];
}

$pdf->BasicTable($header, $data, $total_utilidades);
$pdf->Output();
?>