<?php
include __DIR__ . "/../../conexion.php";  

header('Content-Type: application/json');

// Consulta para obtener los pedidos de bar pendientes
$query = "SELECT p.id, p.num_mesa, p.fecha, d.cantidad, d.nombre
          FROM pedidos p
          INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
          WHERE p.estado = 'PENDIENTE' AND d.tipo = 'bebida'
          ORDER BY p.fecha DESC";

$result = mysqli_query($conexion, $query);
$pedidos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = $row;
}

echo json_encode($pedidos);
