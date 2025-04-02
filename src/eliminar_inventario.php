<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";

    if (isset($_POST['id_inventario'])) {
        $id = intval($_POST['id_inventario']);
        $query = mysqli_query($conexion, "DELETE FROM inventario WHERE id_inventario = $id");

        if ($query) {
            header("Location: inventario.php?msg=deleted");
        } else {
            echo "<script>
                alert('Error al eliminar el registro');
                window.location = 'inventario.php';
            </script>";
        }
    } else {
        header("Location: inventario.php");
    }
} else {
    header("Location: permisos.php");
}
?>