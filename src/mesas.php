<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    if (isset($_GET['id_sala']) && isset($_GET['mesas'])) {
        $id = $_GET['id_sala'];
        $mesas = $_GET['mesas'];
    } else {
        // Si no existen los parámetros, redirigir o mostrar un mensaje de error
        echo "Faltan parámetros en la URL.";
        exit;
    }
    include_once "includes/header.php";
    ?>
    <div class="card">
        <div class="card-header text-center">
            Mesas
        </div>
        <div class="card-body">
            <?php if ($_SESSION['rol'] == 1) { ?>
                <div class="text-right mb-3">
                    <!-- Botón para abrir el formulario de agregar mesas (solo visible para Administradores) -->
                    <button class="btn btn-success" data-toggle="modal" data-target="#agregarMesasModal">Agregar Mesas</button>
                </div>
            <?php } ?>
            <!-- Formulario para editar capacidad y estado -->
            <div class="row">
                <div class="col-md-12">
                    <?php if ($_SESSION['rol'] == 1) { // Formulario solo visible para Administradores ?>
                        <form action="editar_mesas.php" method="post" id="formulario-editar">
                            <!-- Campo oculto para enviar el ID de la mesa -->
                            <input type="hidden" name="id_mesa" id="id_mesa">
                            <!-- Campo oculto para enviar el ID de la sala -->
                            <input type="hidden" name="id_sala" id="id_sala" value="<?php echo $id; ?>">
                            <!-- Campo oculto para enviar la cantidad de mesas -->
                            <input type="hidden" name="mesas" id="mesas" value="<?php echo $mesas; ?>">

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="capacidad" class="font-weight-bold">Capacidad</label>
                                        <input type="number" name="capacidad" id="capacidad" class="form-control"
                                            placeholder="Ingrese capacidad" required>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="estado" class="font-weight-bold">Estado</label>
                                        <select name="estado" id="estado" class="form-control">
                                            <option value="DISPONIBLE">Disponible</option>
                                            <option value="OCUPADA">Ocupada</option>
                                            <option value="RESERVADA">Reservado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary btn-block">Guardar Cambios</button>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
            <hr>
            <div class="row">
                <?php
                include "../conexion.php";
                $query = mysqli_query($conexion, "SELECT * FROM mesas WHERE id_sala = $id");
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) {
                        // Consulta para verificar si hay pedidos pendientes
                        $consulta = mysqli_query($conexion, "SELECT COUNT(*) as total FROM pedidos WHERE id_sala = $id AND num_mesa = " . $data['num_mesa'] . " AND estado = 'ACTIVO'");
                        $resultPedido = mysqli_fetch_assoc($consulta);
                        $isPendiente = $resultPedido['total'] > 0;
                        ?>
                        <div class="col-md-3">
                            <div class="card card-widget widget-user">
                            <div class="widget-user-header bg-<?php 
                                if ($data['estado'] == 'RESERVADA' && $isPendiente) {
                                    echo 'danger'; // Rojo para 'RESERVADO' con pedido pendiente
                                } elseif ($data['estado'] == 'RESERVADA') {
                                    echo 'warning'; // Amarillo para 'RESERVADO' sin pedido pendiente
                                } elseif ($isPendiente) {
                                    echo 'danger'; // Rojo para 'PENDIENTE' sin estar reservado
                                } else {
                                    echo 'success'; // Verde para 'DISPONIBLE'
                                }
                            ?>">
                                <h3 class="widget-user-username">MESA <?php echo $data['num_mesa']; ?></h3>
                                <h5 class="widget-user-desc">Capacidad: <?php echo $data['capacidad']; ?></h5>
                            </div>
                                <div class="widget-user-image">
                                    <img class="img-circle elevation-2" src="../assets/img/mesa.jpg" alt="User Avatar">
                                </div>
                                <div class="card-footer">
                                    <div class="description-block">
                                        <!-- Botón para atender o finalizar -->
                                        <?php
                                        if (!$isPendiente) {
                                            echo '<a class="btn btn-outline-info mb-2" href="pedido.php?id_sala=' . $id . '&mesa=' . $data['num_mesa'] . '">Atender</a>';
                                        } else {
                                            echo '<a class="btn btn-outline-success mb-2" href="finalizar.php?id_sala=' . $id . '&mesa=' . $data['num_mesa'] . '">Finalizar</a>';
                                        }
                                        ?>
                                        <!-- Botón para abrir el modal, pasamos el id de la mesa -->
                                        <a class="btn btn-outline-info mb-2" href="#" data-toggle="modal" data-target="#cambiarMesaModal" onclick="setMesaId(<?php echo $data['id_mesa']; ?>)">
                                            Cambio Mesa
                                        </a>

                                       
                                        <!-- Botones de editar y eliminar solo visibles para Administradores -->
                                        <?php if ($_SESSION['rol'] == 1) { ?>
                                            <div class="d-flex justify-content-center">
                                                <button class="btn btn-primary mx-1"
                                                    onclick="cargarDatosMesa(<?php echo $data['id_mesa']; ?>, <?php echo $data['capacidad']; ?>, '<?php echo $data['estado']; ?>')">
                                                    <i class='fas fa-edit'></i>
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
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
       <!-- Modal -->
    <div class="modal fade" id="cambiarMesaModal" tabindex="-1" role="dialog" aria-labelledby="cambiarMesaModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cambiarMesaModalLabel">Cambiar Mesa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="cambiar_mesa.php" method="POST">
                <div class="modal-body">
                <!-- Campo oculto para pasar el id de la mesa -->
                <input type="hidden" id="id_mesa_antigua" name="id_mesa_antigua">

                <label for="nueva_mesa">Selecciona la nueva mesa:</label>
                <select class="form-control" id="nueva_mesa" name="nueva_mesa">
                    <!-- Suponiendo que tienes una lista de mesas disponibles -->
                    <?php
                    // Obtener todas las mesas disponibles
                    $query = "SELECT * FROM mesas WHERE estado = 'disponible'";
                    $result = mysqli_query($conexion, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                    echo '<option value="' . $row['id_mesa'] . '">Mesa ' . $row['num_mesa'] . '</option>';
                    }
                    ?>
                </select>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar Cambio</button>
                </div>
            </form>
            </div>
        </div>
        </div>

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

    <script>
        function cargarDatosMesa(idMesa, capacidad, estado) {
            document.getElementById('id_mesa').value = idMesa;
            document.getElementById('capacidad').value = capacidad;
            document.getElementById('estado').value = estado;
        }
        function setMesaId(mesaId) {
            // Asignar el id de la mesa seleccionada al campo oculto
            document.getElementById('id_mesa_antigua').value = mesaId;
        }
    </script>
    <?php
    include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>