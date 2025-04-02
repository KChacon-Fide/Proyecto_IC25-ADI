<?php
session_start();
if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
    include "../conexion.php";

    if (!isset($_GET['id_sala']) || !isset($_GET['mesa'])) {
        die("<div class='alert alert-danger text-center'>Faltan parámetros en la URL.</div>");
    }

    $id_sala = $_GET['id_sala'];
    $mesa = $_GET['mesa'];

    $query = mysqli_query($conexion, "SELECT * FROM pedidos WHERE id_sala = '$id_sala' AND num_mesa = '$mesa' AND estado = 'ACTIVO'");
    $pedido = mysqli_fetch_assoc($query);

    if (!$pedido) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'No hay pedidos activos para esta mesa.',
                    confirmButtonText: 'Volver',
                    allowOutsideClick: false
                }).then(() => {
                    window.location = 'index.php';
                });
              </script>";
        exit();
    }

    $id_pedido = $pedido['id'];
    $queryDetalle = mysqli_query($conexion, "SELECT * FROM detalle_pedidos WHERE id_pedido = '$id_pedido'");
    $aplicar_impuesto = false;
    include_once "includes/header.php";
    ?>

    <div class="card">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-receipt"></i> Resumen del Pedido</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Información de la mesa -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4" style="height: 100%;">
                        <div class="card-header bg-primary text-white text-center">
                            <h5 class="mb-0"><i class="fas fa-utensils"></i> Información de la Mesa</h5>
                        </div>
                        <div class="card-body text-center">
                            <h4><strong>Mesa:</strong> <?php echo $mesa; ?></h4>
                            <h5><strong>Fecha:</strong> <?php echo $pedido['fecha']; ?></h5>
                        </div>
                    </div>
                </div>

                <!-- Total del pedido -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4" style="height: 100%;">
                        <div class="card-header bg-success text-white text-center">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Total del Pedido</h5>
                        </div>
                        <div class="card-body text-center">
                            <h4 class="text-success"><strong>Total:</strong>
                                ₡<?php echo number_format($pedido['total'], 0); ?></h4>
                            <div class="form-check form-switch mt-3">
                                <input class="form-check-input" type="checkbox" id="flexCheckDefault"
                                    onclick="toggleImpuesto()">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Impuesto de Servicio (10%)
                                </label>
                            </div>
                            <div class="mt-3" id="impuestoInfo" style="display: none;">
                                <h5 class="text-info"><strong>Impuesto al Servicio:</strong> ₡<span
                                        id="impuestoServicio"></span></h5>
                                <h5 class="text-success"><strong>Total con Impuesto:</strong> ₡<span
                                        id="totalConImpuesto"></span></h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métodos de pago -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4" style="height: 100%;">
                        <div class="card-header  text-white text-center" style="background-color: #1E3A8A;">
                            <h5 class="mb-0"><i class="fas fa-credit-card"></i> Método de Pago</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethodEfectivo"
                                    value="efectivo">
                                <label class="form-check-label" for="paymentMethodEfectivo">
                                    Efectivo
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="paymentMethod" id="paymentMethodTarjeta"
                                    value="tarjeta" checked>
                                <label class="form-check-label" for="paymentMethodTarjeta">
                                    Tarjeta
                                </label>
                            </div>
                            <!-- Botón para mostrar modal de vuelto -->
                            <button id="btnVuelto" class="btn btn-warning mt-3 d-none" data-bs-toggle="modal"
                                data-bs-target="#modalVuelto">
                                <i class="fas fa-cash-register"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow-lg">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-center"
                        style="border-collapse: collapse; border: 0.5px solid #1E3A8A; text-align: center;">
                        <thead style="background-color: #1E3A8A; color: white;">
                            <tr>
                                <th>Nombre</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Seleccionar</th>
                                <th>Cantidad a Pagar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($detalle = mysqli_fetch_assoc($queryDetalle)) {
                                $subtotal = $detalle['cantidad'] * $detalle['precio'];
                                echo "<tr>
                                    <td>{$detalle['nombre']}</td>
                                    <td>{$detalle['cantidad']}</td>
                                    <td>₡{$detalle['precio']}</td>
                                    <td>₡{$subtotal}</td>
                                    <td>
                                        <input type='checkbox' class='detalle-checkbox' data-id='{$detalle['id']}' data-precio='{$detalle['precio']}'>
                                    </td>
                                    <td style='position: relative;'>
                                   <input type='number' class='cantidad-a-pagar' data-id='{$detalle['id']}' min='1' max='{$detalle['cantidad']}' value='1' style='width: 60px;'>
                                   <button type='button' class='btn btn-success btn-sm toggle-row' 
                                   data-id='{$detalle['id']}' title='Inhabilitar' 
                                   style='position: absolute; top: 50%; transform: translateY(-50%); right:60px;'>
                                   <i class='bi bi-check-lg'></i>
                                    </button>
                                     </td>

                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-center">
                    <form method="POST">
                        <input type="hidden" name="finalizar_pedido" value="1">
                        <input type="hidden" name="aplicar_impuesto" id="aplicar_impuesto"
                            value="<?php echo $aplicar_impuesto ? '1' : '0'; ?>">
                        <input type="hidden" name="total_con_impuesto" id="total_con_impuesto"
                            value="<?php echo $pedido['total']; ?>">
                        <input type="hidden" name="imp_servicio" id="imp_servicio" value="0">
                        <input type="hidden" name="tipo_pago" id="tipo_pago" value="tarjeta">
                        <button type="button" class="btn btn-primary btn-lg" onclick="DividirFactura()">
                            <i class="fas fa-file-invoice"></i> Dividir Factura
                        </button>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-check-circle"></i> Confirmar / Finalizar Pedido
                        </button>
                    </form>
                    <br>

                </div>
            </div>
        </div>
    </div>
    </div>

    <style>
        .disabled-row {
            background-color: #f0f0f0 !important;
            opacity: 0.6;
        }
    </style>

    <!-- Modal de Vuelto -->
    <div class="modal fade" id="modalVuelto" tabindex="-1" aria-labelledby="modalVueltoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-primary">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalVueltoLabel"><i class="fas fa-coins"></i> Calcular Vuelto</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Cerrar"></button>
                </div>
                <div class="modal-body text-center">
                    <h5>Total a Pagar:</h5>
                    <h3 class="text-success fw-bold" id="totalVuelto">₡0</h3>

                    <div class="mt-3">
                        <label for="montoCliente" class="form-label">Monto entregado por el cliente:</label>
                        <input type="text" class="form-control text-center fw-bold" id="montoCliente" placeholder="₡"
                            oninput="formatearMonto(this)" onkeypress="return soloNumeros(event)">

                    </div>

                    <div class="mt-3">
                        <h5>Vuelto:</h5>
                        <h4 class="text-danger fw-bold" id="vueltoCalculado">₡0</h4>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" onclick="calcularVuelto()">Calcular</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function DividirFactura() {
            const checkboxes = document.querySelectorAll('.detalle-checkbox:checked');
            let itemsSeleccionados = [];

            checkboxes.forEach(checkbox => {
                const row = checkbox.closest('tr');

                if (row.classList.contains('disabled-row')) return;

                const id = checkbox.dataset.id;
                const precio = parseFloat(checkbox.dataset.precio);
                const cantidadInput = row.querySelector(`.cantidad-a-pagar[data-id="${id}"]`);
                const cantidad = parseInt(cantidadInput.value);

                if (cantidad > 0) {
                    itemsSeleccionados.push({ id, cantidad, precio });
                }
            });
            if (itemsSeleccionados.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No hay elementos seleccionados',
                    text: 'Por favor, seleccione al menos un elemento para generar la factura.',
                });
                return;
            }

            const aplicarImpuesto = document.getElementById('aplicar_impuesto').value === '1';
            const impServicio = parseFloat(document.getElementById('imp_servicio').value);
            const tipoPago = document.getElementById('tipo_pago').value;
            const datos = {
                items: itemsSeleccionados,
                aplicarImpuesto: aplicarImpuesto,
                impServicio: impServicio,
                tipoPago: tipoPago
            };

            console.log('Datos enviados al servidor:', datos);
            fetch('dividir_factura.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Factura Generada',
                            html: `<a href="Factura_Seleccionada.pdf" class="btn btn-primary btn-lg" target="_blank">
                                <i class="fas fa-file-pdf"></i> Descargar Factura
                           </a>`,
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'No se pudo generar la factura.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error en la solicitud:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.',
                    });
                });
        }
        function toggleImpuesto() {
            var checkbox = document.getElementById('flexCheckDefault');
            var aplicarImpuestoInput = document.getElementById('aplicar_impuesto');
            var impuestoInfo = document.getElementById('impuestoInfo');
            var impuestoServicio = document.getElementById('impuestoServicio');
            var totalConImpuesto = document.getElementById('totalConImpuesto');
            var impServicioInput = document.getElementById('imp_servicio');
            var total = <?php echo $pedido['total']; ?>;
            var impuesto = total * 0.10;
            var totalConImpuestoValor = total + impuesto;

            aplicarImpuestoInput.value = checkbox.checked ? '1' : '0';
            impServicioInput.value = checkbox.checked ? impuesto.toFixed(0) : '0';

            if (checkbox.checked) {
                impuestoServicio.innerText = impuesto.toFixed(0);
                totalConImpuesto.innerText = totalConImpuestoValor.toFixed(0);
                impuestoInfo.style.display = 'block';
                document.getElementById('total_con_impuesto').value = totalConImpuestoValor.toFixed(0);
            } else {
                impuestoInfo.style.display = 'none';
                document.getElementById('total_con_impuesto').value = total.toFixed(0);
            }
        }
        document.querySelectorAll('input[name="paymentMethod"]').forEach((elem) => {
            elem.addEventListener("change", function (event) {
                var tipoPagoInput = document.getElementById('tipo_pago');
                tipoPagoInput.value = event.target.value;
            });
        });

        // MODAL CALCULO DE VUELTO
        // Mostrar/ocultar el botón del vuelto según el método de pago
        document.querySelectorAll('input[name="paymentMethod"]').forEach((elem) => {
            elem.addEventListener("change", function () {
                const btnVuelto = document.getElementById("btnVuelto");
                const tipo = this.value;
                document.getElementById('tipo_pago').value = tipo;

                if (tipo === "efectivo") {
                    btnVuelto.classList.remove("d-none");
                } else {
                    btnVuelto.classList.add("d-none");
                }
            });
        });
        const modalVuelto = document.getElementById('modalVuelto');
        modalVuelto.addEventListener('show.bs.modal', () => {
            const aplicarImpuesto = document.getElementById("aplicar_impuesto").value === "1";
            let total = 0;
            const checkboxes = document.querySelectorAll('.detalle-checkbox:checked');
            if (checkboxes.length > 0) {
                checkboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    if (row.classList.contains('disabled-row')) return;

                    const id = checkbox.dataset.id;
                    const precio = parseFloat(checkbox.dataset.precio);
                    const cantidadInput = document.querySelector(`.cantidad-a-pagar[data-id="${id}"]`);
                    const cantidad = parseInt(cantidadInput.value);

                    if (!isNaN(cantidad) && cantidad > 0) {
                        total += precio * cantidad;
                    }
                });
                if (aplicarImpuesto) {
                    const impuesto = total * 0.10;
                    total += impuesto;
                }
            } else {
                total = aplicarImpuesto
                    ? parseFloat(document.getElementById("total_con_impuesto").value)
                    : <?php echo $pedido['total']; ?>;
            }
            document.getElementById("totalVuelto").innerText = `₡${total.toLocaleString()}`;
            document.getElementById("totalVuelto").dataset.total = total.toFixed(2);
            document.getElementById("montoCliente").value = '';
            document.getElementById("vueltoCalculado").innerText = "₡0";
        });
        function calcularVuelto() {
            let valorInput = document.getElementById("montoCliente").value.replace(/[₡,]/g, '');
            const montoCliente = parseFloat(valorInput);
            const totalReal = parseFloat(document.getElementById("totalVuelto").dataset.total || "0");

            if (isNaN(montoCliente) || montoCliente <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Monto inválido',
                    text: 'Por favor, ingrese un monto válido entregado por el cliente.'
                });
                return;
            }
            const vuelto = montoCliente - totalReal;
            document.getElementById("vueltoCalculado").innerText = vuelto >= 0
                ? `₡${vuelto.toLocaleString()}`
                : `₡0`;
            if (vuelto < 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Monto insuficiente',
                    text: `El monto entregado no cubre el total a pagar.`,
                });
            }
        }
        document.getElementById("montoCliente").addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                calcularVuelto();
            }
        });
        function formatearMonto(input) {
            let valor = input.value.replace(/[₡,]/g, '').trim();

            if (isNaN(valor)) {
                input.value = '';
                return;
            }

            let numero = parseFloat(valor);
            if (isNaN(numero)) numero = 0;

            input.value = '₡' + numero.toLocaleString();
        }
        function soloNumeros(evt) {
            const charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode >= 48 && charCode <= 57) || charCode === 46) {
                return true;
            }

            evt.preventDefault();
            return false;
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toggle-row').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    const row = button.closest('tr');
                    if (row.classList.contains('disabled-row')) {
                        row.classList.remove('disabled-row');
                        row.querySelectorAll('input, button, select').forEach(el => el.disabled = false);
                        button.classList.remove('btn-danger');
                        button.classList.add('btn-success');
                        button.innerHTML = '<i class="bi bi-check-lg"></i>';
                    } else {
                        row.classList.add('disabled-row');
                        row.querySelectorAll('input, button, select').forEach(el => {
                            if (!el.classList.contains('toggle-row')) el.disabled = true;
                        });
                        button.classList.remove('btn-success');
                        button.classList.add('btn-danger');
                        button.innerHTML = '<i class="bi bi-x-lg"></i>';
                    }
                });
            });
        });
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_pedido'])) {
        $aplicar_impuesto = isset($_POST['aplicar_impuesto']) && $_POST['aplicar_impuesto'] == '1';
        $total_con_impuesto = $_POST['total_con_impuesto'];
        $imp_servicio = $_POST['imp_servicio'];
        $tipo_pago = $_POST['tipo_pago'];

        $update = mysqli_query($conexion, "UPDATE pedidos SET estado = 'FINALIZADO', total = '$total_con_impuesto', ImpServicio = '$imp_servicio', tipoPago = '$tipo_pago' WHERE id = '$id_pedido'");

        if ($update) {
            $updateMesa = mysqli_query($conexion, "UPDATE mesas SET estado = 'DISPONIBLE' WHERE id_sala = '$id_sala' AND num_mesa = '$mesa'");

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Pedido Finalizado',
                    html: `La orden se ha cerrado correctamente.<br>
                           ¿Desea imprimir la factura?<br>
                           <a href='Factura.php?id_pedido=$id_pedido' class='btn btn-primary btn-lg'
                              target='_blank' style='background-color: #1E3A8A; margin-top: 10px;'>
                              <i class='fas fa-file-pdf'></i> Descargar Factura en PDF
                           </a>`,
                    allowOutsideClick: false
                }).then(() => {
                    window.location = 'index.php';
                });
            </script>";
        } else {
            echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo finalizar el pedido.',
                confirmButtonText: 'Intentar de nuevo'
            });
        </script>";
        }
    }

    include_once "includes/footer.php";
} else {
    header('Location: permisos.php');
}
?>
<!-- Botón flotante -->
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
    <link rel="stylesheet" href="../assets/plugins/bootstrap/css/bootstrap-icons.css">