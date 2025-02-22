<?php
include __DIR__ . "/../../conexion.php";  // ✅ CORRECTO

header('Content-Type: application/json');

// Consulta para obtener los pedidos pendientes
// $query = "SELECT p.id, p.num_mesa, p.fecha, d.cantidad, d.nombre, p.observacio
//           FROM pedidos p
//           INNER JOIN detalle_pedidos d ON p.id = d.id_pedido
//           WHERE p.estado = 'PENDIENTE'
//           ORDER BY p.fecha DESC";

$data = json_decode(file_get_contents("php://input"), true);

// Validar que se recibe un ID válido

if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID de pedido no proporcionado."]);
    exit;
}

if (!isset($data['estado'])) {
    echo json_encode(["success" => false, "message" => "Estado de pedido no proporcionado."]);
    exit;
}


$id = intval($data['id']);

$estado = $data['estado'];

$query = "UPDATE detalle_pedidos
            SET estado = '$estado'
            WHERE id = $id";


$result = mysqli_query($conexion, $query);

echo json_encode("ok");
