<?php
session_start();
if (empty($_SESSION['active'])) {
    header('Location: ../');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Bar</title>
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/dist/css/Bar.css">

    
    
</head>

<body>
    

    <!-- Botón de regreso -->
    <img src="../assets/img/return.png" alt="Volver al Dashboard" class="return-button"
        onclick="location.href='dashboard.php';">

    <div class="container mt-4">
        <h2  class="fw-bold">ESTADO DEL BAR </h2>
        <div id="botonesFiltro" class="d-flex gap-2 mb-4">
        </div>
        <hr>

        <div id="pedidos-container" class="row g-3">

        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/bar.js"></script>
    <script src="../assets/plugins/fontawesome-free/js/all.min.js"></script>
    
</body>

</html>