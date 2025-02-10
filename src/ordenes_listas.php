<?php
session_start();
if (empty($_SESSION['active'])) {
     exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Órdenes Listas</title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/dist/css/Cocina.css"> 
</head>

<body>
    <!-- Botón de regreso -->
    <img src="../assets/img/return.png" alt="Volver a Cocina" class="return-button" onclick="location.href='dashboard.php';">

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content">
                <h2 class="text-center">Órdenes Listas</h2>
                <div id="ordenes-listas-container">
                    <!-- Aquí se insertarán las órdenes listas dinámicamente -->
                </div>
            </section>
        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/dist/js/adminlte.min.js"></script>
    <script src="../assets/js/ordenes_listas.js"></script>
</body>
</html>
