<?php
session_start();



include_once "includes/header.php";
include "../conexion.php";
$query1 = mysqli_query($conexion, "SELECT COUNT(id) AS total FROM salas WHERE estado = 1");
$totalSalas = mysqli_fetch_assoc($query1);
$query2 = mysqli_query($conexion, "SELECT COUNT(id) AS total FROM platos WHERE estado = 1");
$totalPlatos = mysqli_fetch_assoc($query2);
$query3 = mysqli_query($conexion, "SELECT COUNT(id) AS total FROM usuarios WHERE estado = 1");
$totalUsuarios = mysqli_fetch_assoc($query3);
$query4 = mysqli_query($conexion, "SELECT COUNT(id) AS total FROM pedidos");
$totalPedidos = mysqli_fetch_assoc($query4);

$query5 = mysqli_query($conexion, "SELECT SUM(total) AS total FROM pedidos");
$totalVentas = mysqli_fetch_assoc($query5);
?>
<link rel="stylesheet" href="../assets/dist/css/dashboard.css">
<link rel="stylesheet" href="../assets/dist/css/calculator.css">
<div class="card" id="panel">
    <div class="card-header text-center">
        Panel
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info" style="background-color:rgb(67, 163, 184) !important;">
                    <div class="inner">
                        <h3><?php echo $totalPlatos['total']; ?></h3>

                        <p>Platos</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-bag"></i>
                    </div>
                    <a href="platos.php" class="small-box-footer">Más Información <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success" style="background-color:rgb(67, 167, 69) !important;">
                    <div class="inner">
                        <h3><?php echo $totalSalas['total']; ?></h3>

                        <p>Salas</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-stats-bars"></i>
                    </div>
                    <a href="salas.php" class="small-box-footer">Más Información <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success" style="background-color:rgb(255, 172, 5) !important;">
                    <div class="inner">
                        <h3><?php echo $totalUsuarios['total']; ?></h3>

                        <p>Usuarios</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-person-add"></i>
                    </div>
                    <a href="usuarios.php" class="small-box-footer">Más Información <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger" style="background-color:rgb(191, 36, 51) !important;">
                    <div class="inner">
                        <h3><?php echo $totalPedidos['total']; ?></h3>

                        <p>Pedidos</p>
                    </div>
                    <div class="icon">
                        <i class="ion ion-pie-graph"></i>
                    </div>
                    <a href="lista_ventas.php" class="small-box-footer">Más Información <i
                            class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <!-- ./col -->
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    <div class="card-header border-0 ">
                        <div class="d-flex justify-content-between">
                            <h3 class="card-title">Ventas</h3>
                        </div>
                    </div>
                    <div class="card-body background-color:  rgba(77, 100, 165, 0.1);">
                        <div class="d-flex">
                            <p class="d-flex flex-column">
                                <span class="text-bold text-lg">₡<?php echo $totalVentas['total']; ?></span>
                                <span>Total</span>
                            </p>
                        </div>
                        <!-- /.d-flex -->

                        <div class="position-relative mb-4">
                            <canvas id="sales-chart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
 if ($_SESSION['rol'] == 2) {
     echo "<script>
        document.getElementById('panel').innerHTML = \"<img src='../assets/img/cocinero.jpg' style='width: 100%; height: 100%; object-fit: contain;'>\";
     </script>";
 } 

if ($_SESSION['rol'] == 4) {
    echo "<script>
       document.getElementById('panel').innerHTML = \"<img src='../assets/img/bartender.jpg' style='width: 100%; height: 100%; object-fit: contain;'>\";
    </script>";
} 


?>

<?php include_once "includes/footer.php"; ?>

<div id="calc-bubble" onclick="toggleCalc()">
    <i class="fas fa-calculator"></i>
</div>
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
    <script src="../assets/js/dashboard.js"></script>