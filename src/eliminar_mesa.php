<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";

    if (!empty($_POST['id_mesa']) && !empty($_POST['id_sala']) && !empty($_POST['mesas'])) {
        $id_mesa = $_POST['id_mesa'];
        $id_sala = $_POST['id_sala'];
        $mesas = $_POST['mesas'];

        // Eliminamos la mesa usando la columna "id"
        $query = mysqli_query($conexion, "DELETE FROM mesas WHERE id = '$id_mesa'");

        if ($query) {
            // Redireccionamos con los valores de sala y mesas
            header("Location: mesas.php?id_sala=$id_sala&mesas=$mesas");
            exit();
        } else {
            echo "Error al eliminar la mesa.";
        }
    } else {
        echo "Faltan datos para eliminar la mesa.";
    }
} else {
    header('Location: permisos.php');
}
?>