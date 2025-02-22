<?php
include __DIR__ . "/../../conexion.php";  // ✅ CORRECTO

header('Content-Type: application/json');

// Consulta para obtener los pedidos pendientes
// $query = "SELECT p.id, p.num_mesa, p.fecha, d.cantidad, d.nombre, p.observacio
//           FROM pedidos p
//           INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
//           WHERE p.estado = 'PENDIENTE'
//           ORDER BY p.fecha DESC";



// Capturar el JSON enviado desde JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Validar que se recibe un ID válido
if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID de pedido no proporcionado."]);
    exit;
}

if (!isset($data['tipo'])) {
    echo json_encode(["success" => false, "message" => "Tipo de pedido no proporcionado."]);
    exit;
}

$id_pedido = intval($data['id']);

$tipo = intval($data['tipo']);

$query = "SELECT d.id, d.nombre, d.precio, d.cantidad, d.estado
          FROM detalle_pedidos d
          WHERE d.id_pedido = $id_pedido and d.tipo = $tipo
          ORDER BY d.nombre ASC";



$result = mysqli_query($conexion, $query);
$pedidos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = $row;
}

echo json_encode($pedidos);
