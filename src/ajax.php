<?php
require_once "../conexion.php";
session_start();
if (isset($_GET['detalle'])) {
    $id = $_SESSION['idUser'];
    $id_sala = $_GET['id_sala'];
    $id_mesa = $_GET['id_mesa'];
    $datos = array();
    $detalle = mysqli_query($conexion, "SELECT d.*, p.nombre, p.precio, p.imagen FROM temp_pedidos d INNER JOIN platos p ON d.id_producto = p.id WHERE d.id_usuario = $id AND d.tipo = 1 AND d.id_sala = $id_sala AND d.num_mesa = $id_mesa");
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['nombre'] = $row['nombre'];
        $data['cantidad'] = $row['cantidad'];
        $data['precio'] = $row['precio'];
        $data['imagen'] = ($row['imagen'] == null) ? '../assets/img/default.png' : $row['imagen'];
        $data['total'] = $data['precio'] * $data['cantidad'];
        $data['observacion'] = $row['observacion'];
        $data['tipo'] = 1;
        array_push($datos, $data);
    }

    $detalle = mysqli_query($conexion, "SELECT d.*, b.nombre, b.precio, b.imagen FROM temp_pedidos d INNER JOIN bebidas b ON d.id_producto = b.id WHERE d.id_usuario = $id AND d.tipo = 2 AND d.id_sala = $id_sala AND d.num_mesa = $id_mesa");
    while ($row = mysqli_fetch_assoc($detalle)) {
        $data['id'] = $row['id'];
        $data['nombre'] = $row['nombre'];
        $data['cantidad'] = $row['cantidad'];
        $data['precio'] = $row['precio'];
        $data['imagen'] = ($row['imagen'] == null) ? '../assets/img/default.png' : $row['imagen'];
        $data['total'] = $data['precio'] * $data['cantidad'];
        $data['observacion'] = $row['observacion'];
        $data['tipo'] = 2;
        array_push($datos, $data);
    }


    echo json_encode($datos);
    die();
} else if (isset($_GET['delete_detalle'])) {
    $id_detalle = $_GET['id'];
    $query = mysqli_query($conexion, "DELETE FROM temp_pedidos WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['detalle_cantidad'])) {
    $id_detalle = $_GET['id'];
    $cantidad = $_GET['cantidad'];
    $query = mysqli_query($conexion, "UPDATE temp_pedidos set cantidad = $cantidad WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['agregar_observacion'])) {
    $id_detalle = $_GET['id'];
    $observacion = $_GET['agregar_observacion'];
    $query = mysqli_query($conexion, "UPDATE temp_pedidos set observacion = '$observacion' WHERE id = $id_detalle");
    if ($query) {
        $msg = "ok";
    } else {
        $msg = "Error";
    }
    echo $msg;
    die();
} else if (isset($_GET['procesarPedido'])) {
    $id_sala = $_GET['id_sala'];
    $id_user = $_SESSION['idUser'];
    $mesa = $_GET['mesa'];

    $observacion = "";

    $consulta = mysqli_query($conexion, "SELECT d.*, p.nombre FROM temp_pedidos d INNER JOIN platos p ON d.id_producto = p.id WHERE d.tipo = 1 AND d.id_usuario = $id_user AND d.id_sala = $id_sala AND d.num_mesa = $mesa 
                                            UNION ALL SELECT d.*, b.nombre FROM temp_pedidos d INNER JOIN bebidas b ON d.id_producto = b.id WHERE d.tipo = 2 AND d.id_usuario = $id_user AND d.id_sala = $id_sala AND d.num_mesa = $mesa ");
    $total = 0;
    while ($row = mysqli_fetch_assoc($consulta)) {
        $total += $row['cantidad'] * $row['precio'];
    }
    $pedido = mysqli_query($conexion, "SELECT id as idPedido FROM pedidos WHERE id_sala = $id_sala AND num_mesa = $mesa AND id_usuario = $id_user AND estado = 'ACTIVO'"); //Verificar si existe Pedido
    $existe_pedido = mysqli_num_rows($pedido) > 0 ? true : false;
    
    
    if ($existe_pedido == true) {
        $row = mysqli_fetch_assoc($pedido);
        $id_pedido = $row['idPedido'];
        $insertar = 1;
    }else{
        $insertar = mysqli_query($conexion, "INSERT INTO pedidos (id_sala, num_mesa, total, observacion, id_usuario) VALUES ($id_sala, $mesa, '$total', '$observacion', $id_user)");
        $id_pedido = mysqli_insert_id($conexion);
    }

    if ($insertar == 1) {

        $consulta = mysqli_query($conexion, "SELECT d.*, p.nombre FROM temp_pedidos d INNER JOIN platos p ON d.id_producto = p.id WHERE d.tipo = 1 AND d.id_usuario = $id_user 
                                            UNION ALL SELECT d.*, b.nombre FROM temp_pedidos d INNER JOIN bebidas b ON d.id_producto = b.id WHERE d.tipo = 2 AND d.id_usuario = $id_user ");
        while ($dato = mysqli_fetch_assoc($consulta)) {
            $nombre = $dato['nombre'];
            $cantidad = $dato['cantidad'];
            $precio = $dato['precio'];
            $tipo = $dato['tipo'];
            $observacionesPlato = $dato['observacion'];
            $insertarDet = mysqli_query($conexion, "INSERT INTO detalle_pedidos (nombre, precio, cantidad, id_pedido, tipo, observacion) VALUES ('$nombre', '$precio', $cantidad, $id_pedido, $tipo, '$observacionesPlato')");
        }
        if ($insertarDet > 0) {
            $eliminar = mysqli_query($conexion, "DELETE FROM temp_pedidos WHERE id_usuario = $id_user AND id_sala = $id_sala AND num_mesa = $mesa");
            $sala = mysqli_query($conexion, "SELECT * FROM salas WHERE id = $id_sala");
            $resultSala = mysqli_fetch_assoc($sala);
            $msg = array('mensaje' => $resultSala['mesas']);
        }
    } else {
        $msg = array('mensaje' => 'error');
    }

    echo json_encode($msg);
    die();
} else if (isset($_GET['editarUsuario'])) {
    $idusuario = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM usuario WHERE idusuario = $idusuario");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['editarProducto'])) {
    $id = $_GET['id'];
    $sql = mysqli_query($conexion, "SELECT * FROM platos WHERE id = $id");
    $data = mysqli_fetch_array($sql);
    echo json_encode($data);
    exit;
} else if (isset($_GET['finalizarPedido'])) {
    $id_sala = $_GET['id_sala'];
    $id_user = $_SESSION['idUser'];
    $mesa = $_GET['mesa'];
    $insertar = mysqli_query($conexion, "UPDATE pedidos SET estado='FINALIZADO' WHERE id_sala=$id_sala AND num_mesa=$mesa AND estado='PENDIENTE' AND id_usuario=$id_user");
    if ($insertar) {
        $sala = mysqli_query($conexion, "SELECT * FROM salas WHERE id = $id_sala");
        $resultSala = mysqli_fetch_assoc($sala);
        $msg = array('mensaje' => $resultSala['mesas']);
    } else {
        $msg = array('mensaje' => 'error');
    }

    echo json_encode($msg);
    die();
}
if (isset($_POST['regDetalle'])) {
    $id_producto = $_POST['id'];
    $id_user = $_SESSION['idUser'];
    $id_sala = $_POST['id_sala'];
    $id_mesa = $_POST['id_mesa'];
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : 1;
    $consulta = mysqli_query($conexion, "SELECT * FROM temp_pedidos WHERE id_producto = $id_producto AND id_usuario = $id_user AND tipo = 1 AND id_sala = $id_sala AND num_mesa = $id_mesa");
    $row = mysqli_fetch_assoc($consulta);
    if (empty($row)) {
        $producto = mysqli_query($conexion, "SELECT * FROM platos WHERE id = $id_producto");
        $result = mysqli_fetch_assoc($producto);
        $precio = $result['precio'];
        $query = mysqli_query($conexion, "INSERT INTO temp_pedidos (cantidad, precio, id_producto, id_usuario, tipo, id_sala, num_mesa) VALUES (1, $precio, $id_producto, $id_user, 1, $id_sala, $id_mesa)");
    } else {
        $nueva = $row['cantidad'] + 1;
        $query = mysqli_query($conexion, "UPDATE temp_pedidos SET cantidad = $nueva WHERE id_producto = $id_producto AND id_usuario = $id_user AND tipo = 1 AND id_sala = $id_sala AND num_mesa = $id_mesa");
    }
    if ($query) {
        $msg = "registrado";
    } else {
        $msg = "Error al ingresar";
    }
    echo json_encode($msg);
    die();
}

if (isset($_POST['regDetalleBebida'])) {
    $id_producto = $_POST['id'];
    $id_user = $_SESSION['idUser'];
    $id_sala = $_POST['id_sala'];
    $id_mesa = $_POST['id_mesa'];
    $cantidad = $_POST['cantidad'];
    $consulta = mysqli_query($conexion, "SELECT * FROM temp_pedidos WHERE id_producto = $id_producto AND id_usuario = $id_user AND tipo = 2 AND id_sala = $id_sala AND num_mesa = $id_mesa");
    $row = mysqli_fetch_assoc($consulta);
    if (empty($row)) {
        $producto = mysqli_query($conexion, "SELECT * FROM bebidas WHERE id = $id_producto");
        $result = mysqli_fetch_assoc($producto);
        $precio = $result['precio'];
        $query = mysqli_query($conexion, "INSERT INTO temp_pedidos (cantidad, precio, id_producto, id_usuario, tipo, id_sala, num_mesa) VALUES (1, $precio, $id_producto, $id_user, 2, $id_sala, $id_mesa)");

    } else {
        $nueva = $row['cantidad'] + 1;
        $query = mysqli_query($conexion, "UPDATE temp_pedidos SET cantidad = $nueva WHERE id_producto = $id_producto AND id_usuario = $id_user AND tipo = 2 AND id_sala = $id_sala AND num_mesa = $id_mesa");

    }
    if ($query) {

        $update_inventario = mysqli_query($conexion, "UPDATE inventario SET cantidad = cantidad - $cantidad WHERE id_bebida = $id_producto");
        if ($update_inventario) {
            $msg = "registrado";
        } else {
            $msg = "Error al actualizar el inventario";
        }
    } else {
        $msg = "Error al ingresar";
    }
    echo json_encode($msg);
    die();
}


