<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";

    $busqueda = isset($_GET['search']) ? mysqli_real_escape_string($conexion, $_GET['search']) : '';
    $where = ($busqueda != '') ? "AND nombre LIKE '%$busqueda%'" : '';

    $query = mysqli_query($conexion, "SELECT * FROM platos WHERE estado = 1 $where ORDER BY id DESC");

    while ($data = mysqli_fetch_assoc($query)) {
        ?>
        <tr text-center>
            <td class="font-weight-bold"><?php echo strtoupper($data['nombre']); ?></td>
            <td><span class="font-weight-bold">₡<?php echo number_format($data['precio'], 0); ?></span></td>
            <td>
                <img class="img-fluid plato-img"
                     src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                     alt="Plato">
            </td>
            <td>
                <a onclick="editarPlato(<?php echo $data['id']; ?>)" class="btn btn-warning">
                    <i class='fas fa-edit'></i>
                </a>
                <button class="btn btn-danger"
                        onclick="confirmDeletePlato(<?php echo $data['id']; ?>, '<?php echo htmlspecialchars($data['nombre'], ENT_QUOTES); ?>')">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        <?php
    }
}
?>
