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


if (isset($_SESSION['rol']) && $_SESSION['rol' == 1]) {
    $query = "SELECT p.id, p.num_mesa, p.fecha, p.observacion
          FROM pedidos p 
          WHERE p.estado = 'PENDIENTE' AND p.id in ( 
          select d.id_pedido from detalle_pedidos d where p.idPedido = d.id";
} else {
    $idusuario = 4;
    $query = "SELECT p.id, p.num_mesa, p.fecha, p.observacion
          FROM pedidos p 
          WHERE p.estado = 'PENDIENTE'AND p.id_usuario = " . $idusuario ." AND p.id in ( 
          select d.id_pedido from detalle_pedidos d where  p.idPedido = d.id)";
}

if ($misPedidos == true && $idusuario != '') {
    $query += ' AND p.id_usuario = ' . $idusuario;
}

$result = mysqli_query($conexion, $query);
$pedidos = [];

while ($row = mysqli_fetch_assoc($result)) {
    $pedidos[] = $row;
}

echo json_encode($pedidos);



function escribirEnArchivo($contenido)
{
    $nombreArchivo = "./log.txt";
    // Abre el archivo en modo de escritura. Si el archivo no existe, lo crea.
    // 'a' significa "append", lo que agrega el contenido al final del archivo si ya existe.
    $archivo = fopen($nombreArchivo, 'a');

    if ($archivo === false) {
        echo "Error al abrir el archivo.";
        return false;
    }

    // Escribe el contenido en el archivo
    fwrite($archivo, $contenido . PHP_EOL);  // PHP_EOL agrega un salto de línea al final

    // Cierra el archivo después de escribir
    fclose($archivo);

    echo "Contenido escrito correctamente en el archivo.";
    return true;
}
