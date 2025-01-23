<?php
session_start();
if ($_SESSION['rol'] != 1) {
    echo json_encode(['error' => 'No tienes permisos para esta acción.']);
    exit;
}
include "../conexion.php";

if (!empty($_GET['id'])) {
    $id = $_GET['id'];

    // Consultar la sala en la base de datos
    $query = mysqli_query($conexion, "SELECT * FROM salas WHERE id = $id AND estado = 1");
    $result = mysqli_num_rows($query);

    if ($result > 0) {
        $data = mysqli_fetch_assoc($query);
        echo json_encode($data); // Enviar datos en formato JSON
    } else {
        echo json_encode(['error' => 'Sala no encontrada.']);
    }
} else {
    echo json_encode(['error' => 'ID no proporcionado.']);
}

mysqli_close($conexion);
?>
