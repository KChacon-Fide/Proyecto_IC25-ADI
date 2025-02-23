<?php
session_start();
header("Content-Type: application/json");

// Verificar sesión activa
if (empty($_SESSION['active'])) {
    echo json_encode(["success" => false, "message" => "Acceso no autorizado"]);
    exit();
}

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "usuario", "contraseña", "nombre_base_datos");
if ($mysqli->connect_error) {
    echo json_encode(["success" => false, "message" => "Error de conexión"]);
    exit();
}

// Obtener los datos de la solicitud
$datos = json_decode(file_get_contents("php://input"), true);
if (!$datos) {
    echo json_encode(["success" => false, "message" => "Datos no recibidos"]);
    exit();
}

// Validar datos requeridos
if (!isset($datos["num_mesa"], $datos["nombre"], $datos["cantidad"])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos"]);
    exit();
}

// Variables de los datos recibidos
$num_mesa = $mysqli->real_escape_string($datos["num_mesa"]);
$nombre = $mysqli->real_escape_string($datos["nombre"]);
$cantidad = (int)$datos["cantidad"];
$fecha = (new DateTime())->format('Y-m-d H:i:s');
$descripcion = strtolower(string: trim($datos["descripcion"] ?? "")); // Usamos `?? ""` para evitar errores si no está definida

// Insertar solo si la descripción es "Listo para servir"
if ($descripcion === "LISTO PARA SERVIR") {
    
    $sql = "INSERT INTO ordenes_listas (num_mesa, nombre, cantidad, fecha ) VALUES ('$num_mesa', '$nombre', '$cantidad','$fecha')";
    
    if ($mysqli->query($sql) === TRUE) {
        echo json_encode(["success" => true, "message" => "Orden insertada correctamente"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al insertar: " . $mysqli->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "La orden no está lista para servir"]);
}

$mysqli->close();
?>
