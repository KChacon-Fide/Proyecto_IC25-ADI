<?php
include "../conexion.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = mysqli_query($conexion, "SELECT * FROM inventario WHERE id_inventario = $id");
    $data = mysqli_fetch_assoc($query);
    echo json_encode($data);
}
?>