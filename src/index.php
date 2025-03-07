<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include_once "includes/header.php";
    ?>
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-door-open"></i> Gestión de Salas</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                include "../conexion.php";
                $query = mysqli_query($conexion, "SELECT * FROM salas WHERE estado = 1");
                $result = mysqli_num_rows($query);
                if ($result > 0) {
                    while ($data = mysqli_fetch_assoc($query)) { ?>
                        <div class="col-md-4">
                            <div class="card border-secondary shadow-lg">
                                <img src="../assets/img/salas.jpg" class="card-img-top" alt="Imagen de Sala">
                                <div class="card-body text-center">
                                    <h5 class="font-weight-bold"><?php echo strtoupper($data['nombre']); ?></h5>
                                    <p class="text-muted">Cantidad de Mesas: <span
                                            class="font-weight-bold text-success"><?php echo $data['mesas']; ?></span></p>
                                    <a href="mesas.php?id_sala=<?php echo $data['id']; ?>&mesas=<?php echo $data['mesas']; ?>"
                                        class="btn btn-primary btn-block">
                                        <i class="far fa-eye mr-2"></i> Ver Mesas
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
    <?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>