<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    if (isset($_GET['id_sala']) && isset($_GET['mesas'])) {
        $id = $_GET['id_sala'];
        $mesas = $_GET['mesas'];
    } else {
        echo '<div class="alert alert-danger">Faltan parámetros en la URL.</div>';
        exit;
    }
    include_once "includes/header.php";
    ?>
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-chair"></i> Gestión de Mesas</h4>
        </div>
        <div class="card-body">
            <?php if ($_SESSION['rol'] == 1) { ?>
                <div class="text-right mb-3">
                    <button class="btn btn-success" data-toggle="modal" data-target="#agregarMesasModal">
                        <i class="fas fa-plus"></i> Agregar Mesas
                    </button>
                </div>
            <?php } ?>

            <div class="row">
                <div class="col-md-12">
                    <?php if ($_SESSION['rol'] == 1) { ?>
                        <form action="editar_mesas.php" method="post" id="formulario-editar">
                            <input type="hidden" name="id_mesa" id="id_mesa">
                            <input type="hidden" name="id_sala" id="id_sala" value="<?php echo $id; ?>">
                            <input type="hidden" name="mesas" id="mesas" value="<?php echo $mesas; ?>">

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label for="capacidad" class="font-weight-bold">Capacidad</label>
                                        <input type="number" name="capacidad" id="capacidad" class="form-control"
                                            placeholder="Ingrese capacidad" required>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label for="estado" class="font-weight-bold">Estado</label>
                                        <select name="estado" id="estado" class="form-control">
                                            <option value="DISPONIBLE">Disponible</option>
                                            <option value="OCUPADA">Ocupada</option>
                                            <option value="DESACTIVADA">Reservada</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label for="nombre_cliente" class="font-weight-bold">Nombre del Cliente</label>
                                        <input type="text" name="nombre_cliente" id="nombre_cliente" class="form-control"
                                            placeholder="Ingrese el nombre del cliente">
                                    </div>
                                </div>

                                <div class="col-md-2 d-flex" style="padding-top: 32px;">
                                    <button type="submit" class="btn btn-primary btn-block align-self-start" style="height: 38px;">
                                        <i class="fas fa-save"></i> Guardar
                                    </button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-lg">
        <div class="card-body">

            <div class="row">
                <?php
                include "../conexion.php";
                $query = mysqli_query($conexion, "SELECT * FROM mesas WHERE id_sala = $id");
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                        $consulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM pedidos WHERE id_sala = $id AND num_mesa = " . $data['num_mesa'] . " AND estado = 'ACTIVO'");
                        $resultPedido = mysqli_fetch_assoc($consulta);
                        $isPendiente = $resultPedido['total'] > 0;
                        ?>
                        <div class="col-md-4">
                            <div class="card border-secondary shadow-lg">
                                <div class="card-header bg-<?php
                                if ($data['estado'] == 'DESACTIVADA' && $isPendiente) {
                                    echo 'danger';
                                } elseif ($data['estado'] == 'DESACTIVADA') {
                                    echo 'warning';
                                } elseif ($isPendiente) {
                                    echo 'danger';
                                } else {
                                    echo 'success';
                                }
                                ?> text-white text-center">
                                    <h5 class="font-weight-bold mb-0">MESA <?php echo $data['num_mesa']; ?></h5>
                                </div>
                                <div class="card-body text-center">
                                <p class="text-muted">Cliente: <span class="font-weight-bold">
                                    <?php echo $data['nombre_cliente'] ? $data['nombre_cliente'] : 'Sin asignar'; ?>
                                </span></p>
                                    <img src="../assets/img/mesa.jpg" class="img-thumbnail rounded-circle mb-2" alt="Mesa">
                                    <p class="text-muted">Capacidad: <span
                                            class="font-weight-bold"><?php echo $data['capacidad']; ?></span></p>
                                    <div class="btn-group d-flex">
                                        <?php
                                        if (!$isPendiente) {
                                            echo '<a class="btn btn-outline-info w-50" href="pedido.php?id_sala=' . $id . '&mesa=' . $data['num_mesa'] . '"><i class="fas fa-concierge-bell"></i> <!--Atender --> </a>';
                                        } else {
                                            echo '<a class="btn btn-outline-info w-50" href="pedido.php?id_sala=' . $id . '&mesa=' . $data['num_mesa'] . '"><i class="fas fa-concierge-bell"></i> <!--Atender --> </a>';

                                            echo '<a class="btn btn-outline-success w-50" href="finalizar.php?id_sala=' . $id . '&mesa=' . $data['num_mesa'] . '"><i class="fas fa-check-circle"></i> Finalizar</a>';
                                        }
                                        ?>
                                        <!--<a class="btn btn-outline-info w-50" href="#" data-toggle="modal"
                                            data-target="#cambiarMesaModal" onclick="setMesaId(<?php echo $data['id_mesa']; ?>)">
                                            <i class="fas fa-exchange-alt"> </i>  --Cambiar --
                                        </a> -->
                                    </div>

                                    <?php if ($_SESSION['rol'] == 1) { ?>
                                        <div class="mt-2">
                                            <button class="btn btn-primary"
                                                onclick="cargarDatosMesa(<?php echo $data['id_mesa']; ?>, <?php echo $data['capacidad']; ?>, '<?php echo $data['estado']; ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="eliminar_mesa.php" method="post" class="d-inline">
                                                <input type="hidden" name="id_mesa" value="<?php echo $data['id_mesa']; ?>">
                                                <input type="hidden" name="id_sala" value="<?php echo $id; ?>">
                                                <input type="hidden" name="mesas" value="<?php echo $mesas; ?>">
                                                <button class="btn btn-danger mx-2" type="submit">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>

    <!-- MODAL PARA CAMBIO DE MESA -->
    <!--<div class="modal fade" id="cambiarMesaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="cambiar_mesa.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Cambiar Mesa</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_mesa_antigua" name="id_mesa_antigua">
                        <label for="nueva_mesa">Selecciona la nueva mesa:</label>
                        <select class="form-control" id="nueva_mesa" name="nueva_mesa">
                            <?php
                            $query = "SELECT * FROM mesas WHERE estado = 'DISPONIBLE'";
                            $result = mysqli_query($conexion, $query);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<option value="' . $row['id_mesa'] . '">Mesa ' . $row['num_mesa'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Confirmar Cambio</button>
                    </div>
                </form>
            </div>
        </div>
    </div> -->


    <!-- Modal para agregar mesas (solo para Administradores) -->
    <?php if ($_SESSION['rol'] == 1) { ?>
        <div class="modal fade" id="agregarMesasModal" tabindex="-1" role="dialog" aria-labelledby="agregarMesasModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <form action="agregar_mesas.php" method="post">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="agregarMesasModalLabel">Agregar Mesas</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id_sala" value="<?php echo $id; ?>">
                            <div class="form-group">
                                <label for="nuevas_mesas">Cantidad de Mesas</label>
                                <input type="number" name="nuevas_mesas" id="nuevas_mesas" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="capacidad">Capacidad</label>
                                <input type="number" name="capacidad" id="capacidad" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php } ?>
    <!-- jQuery y Bootstrap JS (justo antes de cerrar el body) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function cargarDatosMesa(idMesa, capacidad, estado, nombreCliente) {
            document.getElementById('id_mesa').value = idMesa;
            document.getElementById('capacidad').value = capacidad;
            document.getElementById('estado').value = estado;
            document.getElementById('nombre_cliente').value = nombreCliente || ''; // Maneja valores nulos
        }

        function setMesaId(mesaId) {
            document.getElementById('id_mesa_antigua').value = mesaId;
        }


    </script>

    <?php
    include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>