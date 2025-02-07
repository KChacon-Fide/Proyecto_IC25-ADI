
<?php
// Incluir conexión a la base de datos
include "../conexion.php";

// Verificar que los datos del formulario han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Obtener los datos del formulario
    $id_mesa_antigua = $_POST['id_mesa_antigua']; // Mesa actual
    $id_mesa_nueva = $_POST['nueva_mesa']; // Nueva mesa seleccionada

    // Iniciar una transacción para asegurar que todas las operaciones se hagan correctamente
    mysqli_begin_transaction($conexion);
    
    try {
        // Obtener los pedidos actuales de la mesa antigua
        $query_pedido = "SELECT * FROM pedidos WHERE num_mesa = '$id_mesa_antigua'";
        $result_pedido = mysqli_query($conexion, $query_pedido);
        
        // Insertar los mismos productos en la nueva mesa
        while ($pedido = mysqli_fetch_assoc($result_pedido)) {
            $producto_id = $pedido['producto_id'];
            $cantidad = $pedido['cantidad'];

            // Insertar el pedido en la nueva mesa
            $query_insert = "INSERT INTO pedidos (id_mesa, producto_id, cantidad) 
                             VALUES ('$id_mesa_nueva', '$producto_id', '$cantidad')";
            mysqli_query($conexion, $query_insert);
        }

        // Opcional: Actualizar el estado de la mesa antigua para que esté disponible
        $query_actualizar_mesa = "UPDATE mesas SET estado = 'disponible' WHERE id_mesa = '$id_mesa_antigua'";
        mysqli_query($conexion, $query_actualizar_mesa);
        
        // Opcional: Actualizar el estado de la nueva mesa para que esté ocupada
        $query_actualizar_nueva_mesa = "UPDATE mesas SET estado = 'ocupada' WHERE id_mesa = '$id_mesa_nueva'";
        mysqli_query($conexion, $query_actualizar_nueva_mesa);

        // Si todo ha ido bien, confirmar la transacción
        mysqli_commit($conexion);

        // Redirigir a la página de selección de mesas con un mensaje de éxito
        header("Location: mesas.php?");
        exit();
        
    } catch (Exception $e) {
        // Si ocurre un error, deshacer la transacción
        
        
        // Redirigir con un mensaje de error
        header("Location: mesas.php?");
        exit();
    }
} else {
    // Si no se recibió el formulario, redirigir a la página de selección de mesas
    header("Location: mesas.php?");
    exit();
}
?>
