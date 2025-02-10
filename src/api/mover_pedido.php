<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Incluir la conexión a la base de datos
require_once __DIR__ . "/../../conexion.php";

// Verificar si la conexión a la base de datos está establecida
if (!isset($conexion) || !$conexion) {
    echo json_encode(["success" => false, "message" => "Error en la conexión a la base de datos."]);
    exit;
}

// Capturar el JSON enviado desde JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Validar que se recibe un ID válido
if (!isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "ID de pedido no proporcionado."]);
    exit;
}

$id_pedido = intval($data['id']);

// Verificar si el pedido existe en la tabla `pedidos`
$query_check = "SELECT * FROM pedidos WHERE id = ?";
$stmt_check = $conexion->prepare($query_check);
$stmt_check->bind_param("i", $id_pedido);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Pedido no encontrado."]);
    exit;
}

// Insertar el pedido en `ordenes_listas`
$query_insert = "INSERT INTO ordenes_listas (num_mesa, nombre, cantidad, fecha)
                 SELECT num_mesa, nombre, cantidad, fecha FROM pedidos WHERE id = ?";
$stmt_insert = $conexion->prepare($query_insert);
$stmt_insert->bind_param("i", $id_pedido);

if ($stmt_insert->execute()) {
    // Eliminar el pedido de la tabla `pedidos` después de insertarlo en `ordenes_listas`
    $query_delete = "DELETE FROM pedidos WHERE id = ?";
    $stmt_delete = $conexion->prepare($query_delete);
    $stmt_delete->bind_param("i", $id_pedido);
    $stmt_delete->execute();

    echo json_encode(["success" => true, "message" => "Pedido movido correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al mover el pedido."]);
}

// Cerrar conexiones
$stmt_check->close();
$stmt_insert->close();
$stmt_delete->close();
$conexion->close();
?>