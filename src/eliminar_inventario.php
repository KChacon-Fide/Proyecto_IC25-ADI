<?php
session_start();
if (!in_array($_SESSION['rol'], [1, 2])) {
    header("Location: permisos.php");
    exit;
}

include "../conexion.php";

if (isset($_POST['id_inventario'])) {
    $id = intval($_POST['id_inventario']);
} elseif (isset($_GET['id_inventario'])) {
    $id = intval($_GET['id_inventario']);
} else {
    header("Location: inventario.php");
    exit;
}

$query = mysqli_query($conexion, "DELETE FROM inventario WHERE id_inventario = $id");

if ($query) {
    header("Location: inventario.php?msg=deleted");
    exit;
} else {
    echo "<script>
        alert('Error al eliminar el registro');
        window.location = 'inventario.php';
    </script>";
}
