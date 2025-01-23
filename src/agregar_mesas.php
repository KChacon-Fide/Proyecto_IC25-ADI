<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";
    if (!empty($_POST)) {
        $id_sala = $_POST['id_sala'];
        $nuevas_mesas = $_POST['nuevas_mesas'];
        $capacidad = $_POST['capacidad'];

        // Obtener el número actual de mesas de la sala
        $query = mysqli_query($conexion, "SELECT mesas FROM salas WHERE id = $id_sala");
        $result = mysqli_fetch_assoc($query);
        $total_mesas_actuales = $result['mesas'];

        // Calcular el nuevo total de mesas
        $total_mesas = $total_mesas_actuales + $nuevas_mesas;

        // Insertar las nuevas mesas
        for ($i = $total_mesas_actuales + 1; $i <= $total_mesas; $i++) {
            mysqli_query($conexion, "INSERT INTO mesas (id_sala, num_mesa, capacidad, estado) VALUES ($id_sala, $i, $capacidad, 'DISPONIBLE')");
        }

        // Actualizar el total de mesas en la tabla de salas
        mysqli_query($conexion, "UPDATE salas SET mesas = $total_mesas WHERE id = $id_sala");

        // Redirigir al listado de mesas
        header("Location: mesas.php?id_sala=$id_sala&mesas=$total_mesas");
    }
} else {
    header('Location: permisos.php');
}
