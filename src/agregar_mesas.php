<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";
    if (!empty($_POST)) {
        $id_sala = intval($_POST['id_sala']);
        $nuevas_mesas = intval($_POST['nuevas_mesas']);
        $capacidad = intval($_POST['capacidad']);

        $q1 = mysqli_query($conexion, "SELECT mesas FROM salas WHERE id = $id_sala");
        $r1 = mysqli_fetch_assoc($q1);
        $total_mesas_actuales = (int) $r1['mesas'];

        $usados = [];
        $q2 = mysqli_query($conexion, "
            SELECT num_mesa
              FROM mesas
             WHERE id_sala = $id_sala
             ORDER BY num_mesa
        ");
        while ($row = mysqli_fetch_assoc($q2)) {
            $usados[] = (int) $row['num_mesa'];
        }

        $insertadas = 0;
        $current = 1;
        while ($insertadas < $nuevas_mesas) {
            if (!in_array($current, $usados, true)) {
                $sql = "
                  INSERT INTO mesas
                    (id_sala, num_mesa, capacidad, estado)
                  VALUES
                    ($id_sala, $current, $capacidad, 'DISPONIBLE')
                ";
                mysqli_query($conexion, $sql);
                $usados[] = $current;
                $insertadas++;
            }
            $current++;
        }

        mysqli_query($conexion, "
            UPDATE salas
               SET mesas = (
                   SELECT COUNT(*)
                     FROM mesas
                    WHERE id_sala = $id_sala
               )
             WHERE id = $id_sala
        ");

        $cnt = mysqli_query($conexion, "SELECT mesas FROM salas WHERE id = $id_sala");
        $cnt = mysqli_fetch_assoc($cnt);
        $nuevo_total = intval($cnt['mesas']);

        mysqli_close($conexion);
        header("Location: mesas.php?id_sala={$id_sala}&mesas={$nuevo_total}");
        exit;
    }
} else {
    header('Location: permisos.php');
    exit;
}
?>