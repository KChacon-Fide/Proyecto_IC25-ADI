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
        max-height: 800px; /* Limita la altura máxima del contenedor */
        overflow-y: auto; /* Habilita el scroll vertical */
           }
           
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let pedido = [];

    document.querySelectorAll(".addDetalle, .addDetalleBebida").forEach(button => {
        button.addEventListener("click", function () {
            const id = this.getAttribute("data-id");
            const tipo = this.getAttribute("data-tipo");
            const nombre = this.closest(".product-card").querySelector("h6").textContent;
            const precio = parseFloat(this.closest(".product-card").querySelector(".price-badge").textContent.replace("₡", ""));

            let productoExistente = pedido.find(p => p.id == id && p.tipo == tipo);
            if (productoExistente) {
                productoExistente.cantidad++;
            } else {
                pedido.push({id, nombre, precio, tipo, cantidad: 1, descripcion: ""});
            }

            actualizarPedido();
        });
    });

    function actualizarPedido() {
        let detalleHTML = "";
        let total = 0;
        pedido.forEach((producto, index) => {
            let subtotal = producto.precio * producto.cantidad;
            total += subtotal;
            detalleHTML += `
                <div class="pedido-item">
                    <div>${producto.nombre}</div>
                    <div>${producto.cantidad}</div>
                    <div>₡${subtotal.toFixed(2)}</div>
                    <button class="btn btn-info btn-sm edit-desc" data-index="${index}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-danger btn-sm remove-product" data-index="${index}"><i class="fas fa-minus"></i></button>
                    <button class="btn btn-success btn-sm add-product" data-index="${index}"><i class="fas fa-plus"></i></button>
                </div>
            `;
        });

        detalleHTML += <h5>Total: ₡${total.toFixed(2)}</h5>;
        document.getElementById("detalle_pedido").innerHTML = detalleHTML;

        document.querySelectorAll(".remove-product").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                if (pedido[index].cantidad > 1) {
                    pedido[index].cantidad--;
                } else {
                    pedido.splice(index, 1);
                }
                actualizarPedido();
            });
        });

        document.querySelectorAll(".add-product").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                pedido[index].cantidad++;
                actualizarPedido();
            });
        });

        document.querySelectorAll(".edit-desc").forEach(button => {
            button.addEventListener("click", function () {
                let index = this.getAttribute("data-index");
                let nuevaDesc = prompt("Ingrese una descripción para este producto:", pedido[index].descripcion);
                if (nuevaDesc !== null) {
                    pedido[index].descripcion = nuevaDesc;
                }
                actualizarPedido();
            });
        });
    }

    document.getElementById("realizar_pedido").addEventListener("click", function (event) {
        event.preventDefault();
        // Aquí puedes agregar la lógica para procesar el pedido sin redirigir a otra página
        console.log("Pedido procesado:", pedido);
        alert("Pedido procesado con éxito.");
        // Eliminar cualquier lógica de redirección
    });
});
</script>

<?php include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>