<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../conexion.php";

// Verificar conexión a la base de datos
if (!isset($conexion) || !$conexion) {
    echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos."]);
    exit;
}

$query = "SELECT id, num_mesa, nombre, cantidad, fecha FROM ordenes_listas ORDER BY fecha DESC";
$result = $conexion->query($query);

$ordenes_listas = [];

while ($row = $result->fetch_assoc()) {
    $ordenes_listas[] = $row;
}

echo json_encode($ordenes_listas);
$conexion->close();
?>