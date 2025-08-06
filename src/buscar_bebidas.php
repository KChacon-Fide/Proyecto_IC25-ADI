<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2) {
    include "../conexion.php";

    $search = isset($_GET['search'])
        ? mysqli_real_escape_string($conexion, $_GET['search'])
        : '';

    $where = $search !== ''
        ? "AND nombre LIKE '%$search%'"
        : '';

    $sql = "SELECT * FROM bebidas
            WHERE estado = 1 $where
            ORDER BY id DESC";

    $res = mysqli_query($conexion, $sql);
    while ($data = mysqli_fetch_assoc($res)) {
        ?>
        <tr>
            <td class="font-weight-bold"><?php echo strtoupper($data['nombre']); ?></td>
            <td class="font-weight-bold">₡<?php echo number_format($data['precio'], 0, '', '.'); ?></td>
            <td>
                <img class="img-fluid bebida-img"
                     src="<?php echo $data['imagen'] ?? '../assets/img/default.png'; ?>"
                     alt="Bebida">
            </td>
            <td>
                <a onclick="editarBebida(
                        <?php echo $data['id']; ?>,
                        '<?php echo addslashes($data['nombre']); ?>',
                        '<?php echo $data['precio']; ?>',
                        '<?php echo $data['imagen']; ?>')"
                   class="btn btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn btn-danger"
                        onclick="confirmDelete(
                            <?php echo $data['id']; ?>,
                            '<?php echo addslashes($data['nombre']); ?>'
                        )">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        </tr>
        <?php
    }
}
?>
