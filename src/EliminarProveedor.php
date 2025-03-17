<?php
include "../conexion.php";

// Verificar si el parámetro 'id' está presente en la URL
if (isset($_GET['id']) && isset($_GET['accion'])) {
    $id = $_GET['id']; // Obtén el ID del proveedor desde la URL
    $accion = $_GET['accion']; // Obtén la acción desde la URL

    // Si la acción es 'proveedores', proceder con la eliminación
    if ($accion == 'proveedores') {
        // Consulta SQL para eliminar el proveedor según el ID
        $query = mysqli_query($conexion, "DELETE FROM proveedores WHERE id_proveedor = $id");

        // Verificar si la eliminación fue exitosa
        if ($query) {
            // Redirigir a la página de proveedores después de la eliminación
            header('Location: proveedores.php');
            exit;
        } else {
            // En caso de error, mostrar un mensaje
            echo "Error al eliminar el proveedor.";
        }
    }
} else {
    // Si no se recibe un ID o acción, redirigir o mostrar un mensaje de error
    echo "ID o acción no válidos.";
}
?>