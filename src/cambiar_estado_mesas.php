<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";

    if (!empty($_POST['id_mesa']) && !empty($_POST['nuevo_estado']) && !empty($_POST['id_sala']) && !empty($_POST['mesas'])) {
        $id_mesa = $_POST['id_mesa'];
        $nuevo_estado = $_POST['nuevo_estado'];
        $id_sala = $_POST['id_sala'];
        $mesas = $_POST['mesas'];


        $query = mysqli_query($conexion, "UPDATE mesas SET estado = '$nuevo_estado' WHERE id = '$id_mesa'");

        if ($query) {

            header("Location: mesas.php?id_sala=$id_sala&mesas=$mesas");
            exit();
        } else {
            echo "Error al cambiar el estado de la mesa.";
        }
    } else {
        echo "Faltan datos para cambiar el estado.";
    }
} else {
    header('Location: permisos.php');
}
?>