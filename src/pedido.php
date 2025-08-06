<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include_once "includes/header.php";
?>

<link rel="stylesheet" href="../assets/dist/css/pedido.css">

<div class="container-fluid">
    <div class="row">
        <!-- Sección de Platos y Bebidas -->
        <div class="col-md-8">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h4><i class="fas fa-utensils"></i> Menú</h4>
        </div>
        <div class="card-body">
            <input type="hidden" id="id_sala" value="<?php echo $_GET['id_sala'] ?>">
            <input type="hidden" id="mesa" value="<?php echo $_GET['mesa'] ?>">

            <!-- Buscador -->
            <div class="mb-3">
                <input type="text" id="buscador" class="form-control" placeholder="Buscar platos o bebidas por nombre...">
            </div>

            <!-- Platos -->
            <h4 class="text-center mb-3">Platos</h4>
            <div class="row" id="contenedor_platos">
                <?php
                include "../conexion.php";
                $query = mysqli_query($conexion, "SELECT * FROM platos WHERE estado = 1");
                while ($data = mysqli_fetch_assoc($query)) { ?>
                    <div class="col-md-4 producto-item" data-nombre="<?php echo strtolower($data['nombre']); ?>">
                        <div class="card product-card text-center p-2">
                            <img src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                                 class="product-image img-thumbnail">
                            <h6 class="mt-2"><?php echo $data['nombre']; ?></h6>
                            <span class="badge badge-dark price-badge" style="background-color: #1E3A8A;">₡<?php echo number_format($data['precio'], 2); ?></span>
                            <button class="btn btn-success btn-sm addDetalle"
                                    data-id="<?php echo $data['id']; ?>" data-tipo="plato">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <!-- Bebidas -->
            <h4 class="text-center mt-4 mb-3">Bebidas</h4>
            <div class="row" id="contenedor_bebidas">
                <?php
                $queryBebidas = mysqli_query($conexion, "SELECT * FROM bebidas WHERE estado = 1");
                while ($data = mysqli_fetch_assoc($queryBebidas)) { ?>
                    <div class="col-md-3 producto-item" data-nombre="<?php echo strtolower($data['nombre']); ?>">
                        <div class="card product-card text-center p-2">
                            <img src="<?php echo ($data['imagen'] == null) ? '../assets/img/default.png' : $data['imagen']; ?>"
                                 class="product-image img-thumbnail"
                                 style="width: 180px; height: 150px; object-fit:contain ; border-radius: 8px;">
                            <h6 class="mt-2"><?php echo $data['nombre']; ?></h6>
                            <span class="badge badge-dark price-badge" style="background-color: #1E3A8A;">₡<?php echo number_format($data['precio'], 2); ?></span>
                            <button class="btn btn-success btn-sm addDetalleBebida"
                                    data-id="<?php echo $data['id']; ?>" data-tipo="bebida">
                                <i class="fas fa-cart-plus"></i> Agregar
                            </button>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const buscador = document.getElementById("buscador");
    const productos = document.querySelectorAll(".producto-item");

    buscador.addEventListener("input", function () {
        const filtro = buscador.value.toLowerCase();
        productos.forEach(producto => {
            const nombre = producto.getAttribute("data-nombre");
            if (nombre.includes(filtro)) {
                producto.style.display = "block";
            } else {
                producto.style.display = "none";
            }
        });
    });
});

</script>
    <!-- Sección de pedido -->
        <div class="col-md-4">
            <div class="card shadow-lg">
                <div class="card-header text-white text-center" style="background-color: #007bff;">
                    <h4><i class="fas fa-shopping-cart"></i> Pedido</h4>
                </div>
                <div class="card-body">
                    <div  style="width: 1000px; ">
                    <div id="detalle_pedido"></div>
                    </div>
                    <form id="form_limpia" action="limpiar-pedido.php" method="post" class="d-none">
  <input type="hidden" name="id_sala" value="<?php echo $_GET['id_sala'] ?>">
  <input type="hidden" name="mesa"    value="<?php echo $_GET['mesa'] ?>">
</form>

                    <hr>
                    <button class="btn btn-danger btn-block" id="limpiar_pedido">
  <i class="fas fa-trash-alt"></i>  Limpiar Pedido
</button>

                    <button class="btn btn-primary btn-block" id="realizar_pedido" style="background-color: #1E3A8A;">
                        <i class="fas fa-check"></i>  Realizar Pedido
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#detalle_pedido {
    max-height: 800px;
    overflow-y: auto;
}


.product-card {
    height: 270px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #ddd;
}

.product-card img.product-image {
    width: 100%;
    height: 140px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 5px;
}

.product-card h6 {
    font-size: 16px;
    margin: 0 0 4px;
    min-height: 30px;
}

.price-badge {
    font-size: 14px;
    padding: 5px;
    margin-bottom: 6px;
    display: block;
    background-color: #1E3A8A;
    color: #fff;
}


#contenedor_bebidas .product-card {
    height: 280px;
}

#contenedor_bebidas img.product-image {
    height: 120px;
    object-fit: contain;
}
#contenedor_bebidas .product-card {
    height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 8px 8px 12px 8px;
    background-color: #f8f9fa;
    border-radius: 10px;
    border: 1px solid #ddd;
}

#contenedor_bebidas img.product-image {
    height: 120px;
    width: auto;
    object-fit: contain;
    border-radius: 8px;
    margin: 0 auto 5px auto;
    display: block;
}

#contenedor_bebidas .product-image {
    width: 100%;
    height: 120px;
    object-fit: contain;
    border-radius: 8px;
    margin-bottom: 5px;
}


#contenedor_bebidas .btn-success {
    font-size: 14px;
    padding: 5px 10px;
    width: 100%;
    background-color: #28A745 !important;
    border: none;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const btnLimpiar = document.getElementById("limpiar_pedido");
  const detalle   = document.getElementById("detalle_pedido");
  const formLimpia = document.getElementById("form_limpia"); // el formulario oculto

  btnLimpiar.addEventListener("click", function() {
    detalle.innerHTML = "";

    formLimpia.submit();
  });
});
</script>



<?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>