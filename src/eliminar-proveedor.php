<?php
session_start();
if ($_SESSION['rol'] != 1) {
    header('Location: permisos.php');
    exit;
}
require("../conexion.php");

if (!empty($_POST['id_proveedor'])) {
    $id = intval($_POST['id_proveedor']);
    $sql = "UPDATE proveedores SET estado = 0 WHERE id_proveedor = {$id}";
    if (mysqli_query($conexion, $sql)) {
        mysqli_close($conexion);
        header("Location: proveedores.php?msg=deleted");
        exit;
    } else {
        echo "<script>
            alert('Error al eliminar el proveedor.');
            window.location = 'proveedores.php';
        </script>";
        exit;
    }
} else {
    header("Location: proveedores.php");
    exit;
}
