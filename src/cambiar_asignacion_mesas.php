<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    if (isset($_POST['mesa_actual']) && isset($_POST['nueva_mesa'])) {
        $mesaActual = $_POST['mesa_actual'];
        $nuevaMesa = $_POST['nueva_mesa'];

        // Conexión a la base de datos
        include "../conexion.php";

        // Liberar la mesa actual (cambiar el estado a DISPONIBLE)
        $queryLiberarMesa = mysqli_query($conexion, "UPDATE mesas SET estado = 'DISPONIBLE' WHERE id_mesa = $mesaActual");

        // Asignar la nueva mesa (cambiar el estado a OCUPADA)
        $queryAsignarMesa = mysqli_query($conexion, "UPDATE mesas SET estado = 'OCUPADA' WHERE id_mesa = $nuevaMesa");

        // Comprobar si las consultas se ejecutaron correctamente
        if ($queryLiberarMesa && $queryAsignarMesa) {
            $_SESSION['mensaje'] = "La asignación de mesa se ha cambiado exitosamente.";
            header("Location: mesas.php?id_sala=" . $_GET['id_sala']);
        } else {
            $_SESSION['mensaje'] = "Hubo un error al cambiar la asignación de mesa.";
            header("Location: mesas.php?id_sala=" . $_GET['id_sala']);
        }
    }
} else {
    header('Location: permisos.php');
}
?>