<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    if (!empty($_POST)) {
        include "../conexion.php";

        // Verifica si todos los campos necesarios están presentes
        if (isset($_POST['id_mesa'], $_POST['capacidad'], $_POST['estado'], $_POST['id_sala'], $_POST['mesas'], $_POST['nombre_cliente']) && $_POST['token'] == $_SESSION['token']) {
            $id_mesa = $_POST['id_mesa'];
            $capacidad = $_POST['capacidad'];
            $estado = $_POST['estado'];
            $id_sala = $_POST['id_sala'];
            $mesas = $_POST['mesas'];
            $nombre_cliente = $_POST['nombre_cliente']; // Aunque no se usa, se captura para evitar errores

            // Actualiza la mesa
            $query = mysqli_query($conexion, "UPDATE mesas SET capacidad = '$capacidad', estado = '$estado', nombre_cliente = '$nombre_cliente' WHERE id_mesa = '$id_mesa'");

            if ($query) {
                header("Location: mesas.php?id_sala=$id_sala&mesas=$mesas");
                exit;
            } else {
                echo "Error al actualizar la mesa.";
            }
        } else {
            echo "Error: Faltan datos para procesar la solicitud.";
        }
    }
} else {
    header('Location: permisos.php');
    exit;
}
?>