<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
require("../conexion.php");

// Verifica si la sesión del usuario está activa
if (empty($_SESSION['idUser'])) {
    header('Location: ../');
}

// Verifica si se recibe un ID y la tabla donde se ejecutará la acción
if (!empty($_GET['id']) && !empty($_GET['accion'])) {
    $id = $_GET['id'];
    $table = $_GET['accion'];

    // Cambia el estado a 0 (desactivado) en la tabla especificada
    $query_delete = mysqli_query($conexion, "UPDATE $table SET estado = 0 WHERE id = $id");

    // Verifica si la consulta fue exitosa
    if ($query_delete) {
        mysqli_close($conexion);
        header("Location: " . $table . '.php'); // Redirige de vuelta a la página correspondiente
    } else {
        echo "<script>alert('Error al desactivar el usuario.');</script>";
    }
}
?>

