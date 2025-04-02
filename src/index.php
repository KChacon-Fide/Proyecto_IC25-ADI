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
                                <img src="../assets/img/salas1.jpg" class="card-img-top" alt="Imagen de Sala">
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
<!-- Botón-->
<div id="calc-bubble" onclick="toggleCalc()">
    <i class="fas fa-calculator"></i>
</div>

<!-- Calculadora-->
<div id="calculator">
    <input type="text" id="calc-display" readonly>
    <div class="calc-buttons">
        <button onclick="append('7')">7</button>
        <button onclick="append('8')">8</button>
        <button onclick="append('9')">9</button>
        <button onclick="append('+')">+</button>

        <button onclick="append('4')">4</button>
        <button onclick="append('5')">5</button>
        <button onclick="append('6')">6</button>
        <button onclick="append('-')">-</button>

        <button onclick="append('1')">1</button>
        <button onclick="append('2')">2</button>
        <button onclick="append('3')">3</button>
        <button onclick="append('*')">*</button>

        <button onclick="append('0')">0</button>
        <button onclick="append('.')">.</button>
        <button onclick="clearDisplay()">C</button>
        <button onclick="append('/')">/</button>

        <button class="calc-equal" onclick="calculate()">=</button>
    </div>
    <script src="../assets/js/calculator.js"></script>
    <link rel="stylesheet" href="../assets/dist/css/calculator.css">