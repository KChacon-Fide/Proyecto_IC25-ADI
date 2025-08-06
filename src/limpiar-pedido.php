<?php
session_start();
if (!in_array($_SESSION['rol'], [1, 3])) {
    header('Location: permisos.php');
    exit;
}

require "../conexion.php";

$id_sala = isset($_POST['id_sala']) ? intval($_POST['id_sala']) : 0;
$mesa = isset($_POST['mesa']) ? intval($_POST['mesa']) : 0;

if ($id_sala > 0 && $mesa > 0) {
    $sql = "DELETE FROM temp_pedidos 
            WHERE id_sala = {$id_sala} 
              AND num_mesa = {$mesa}";
    mysqli_query($conexion, $sql);
}

mysqli_close($conexion);
header("Location: pedido.php?id_sala={$id_sala}&mesa={$mesa}");
exit;
