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

if (!isset($data['tipo'])) {
    echo json_encode(["success" => false, "message" => "Tipo de pedido no proporcionado."]);
    exit;
}
$tipo = intval($data['tipo']);

if (isset($data['vista'])) {
    $vista = $data['vista'];
} else {
    $vista = 'TODOS';
}



$query = "SELECT p.id, p.num_mesa, p.fecha, p.estado
          FROM pedidos p 
          WHERE  p.estado = 'ACTIVO' AND p.id in ( 
                            SELECT d.id_pedido 
                            FROM detalle_pedidos d 
                            WHERE d.id_pedido= p.id AND 
                                  @tipo AND 
                                  @estado
                            ORDER BY d.nombre)
                        order by p.fecha";
 
if ($tipo == 0) {
    $query = str_replace("@tipo", "'1=1'",$query);
} else {
    $query = str_replace("@tipo", "d.tipo = '$tipo'", $query);
}

if ($vista == 'TODOS') {
    $query = str_replace("@estado", "'1=1'",$query);
} else {
    $query = str_replace("@estado", "d.estado = '$vista'", $query);
}
$pedidos = [];
escribirLog($query);

$result = mysqli_query($conexion, $query);


while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = $row;
}

echo json_encode($pedidos);


