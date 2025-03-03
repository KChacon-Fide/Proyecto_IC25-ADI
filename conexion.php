<?php
    $host = "localhost";
    $user = "root";
    $clave = "Kenya_05";
    $bd = "restaurante";
    $conexion = mysqli_connect($host,$user,$clave,$bd);
    if (mysqli_connect_errno()){
        echo "No se pudo conectar a la base de datos";
        exit();
    }
    mysqli_select_db($conexion,$bd) or die("No se encuentra la base de datos");
    mysqli_set_charset($conexion,"utf8");

    function escribirLog($mensaje) {
        $logFile = __DIR__ . '/log.txt';  // Ruta del archivo log
        $fecha = date('Y-m-d H:i:s');  // Fecha y hora actual
        $mensajeCompleto = "[$fecha] $mensaje" . PHP_EOL;
        file_put_contents($logFile, $mensajeCompleto, FILE_APPEND);  // Escribir al archivo log
    }
    
?>
