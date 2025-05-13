<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$tiempo_expiracion = 480; // Tiempo inicial de inactividad (40 segundos)
$tiempo_modal = 20;      // Tiempo para responder en el modal (20 segundos)

// Verificar si hay sesión activa
if (empty($_SESSION['active'])) {
    header('Location: ../');
    exit();
}

// Verificar si hay inactividad
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactividad = time() - $_SESSION['LAST_ACTIVITY'];

    // Si supera el tiempo total (inactividad + modal)
    if ($inactividad > ($tiempo_expiracion + $tiempo_modal)) {
        session_unset();
        session_destroy();
        header('Location: ../index.php?timeout=1');
        exit();
    }
}

// Actualizar última actividad si se extiende la sesión
if (isset($_GET['keepalive'])) {
    $_SESSION['LAST_ACTIVITY'] = time();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema RSI</title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../assets/plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../assets/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="../assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../assets/dist/css/custom.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


</head>

<body class="hold-transition sidebar-mini">
    <script src="../assets/js/sweetalert2.all.min.js"></script>
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto ">

                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4  ">
            <!-- Brand Logo -->
            <?php if ($_SESSION['rol'] == 3) {
                echo '<a href="index.php" class="brand-link ">
                        <img src="../assets/img/Logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                        style="opacity: .8">
                        <span class="brand-text font-weight-light"> SAN ISIDRO </span>
                        </a>';
            } else {
                echo '<a href="dashboard.php" class="brand-link">
                <img src="../assets/img/Logo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3"
                    style="opacity: .8">
                <span class="brand-text font-weight-light"> SAN ISIDRO </span>
            </a>';

            }
            ?>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <i class="fas fa-user-circle fa-2x text-info"></i>
                    </div>
                    <div class="info">
                        <a href="mi_perfil.php" class="d-block"><?php echo $_SESSION['nombre']; ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">

                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <?php if ($_SESSION['rol'] == 1) {
                            echo '<li class="nav-item">
                            <a href="dashboard.php" class="nav-link">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>
                                    Dashboard
                                </p>
                            </a>
                        </li>';
                        } ?>
                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {

                            echo '<li class="nav-item">
                                        <a href="index.php" class="nav-link">
                                            <i class="nav-icon fas fa-pizza-slice"></i>
                                            <p>Nueva Orden</p>
                                        </a>
                                    </li>';
                        }
                        ?>

                        <?php if ($_SESSION['rol'] == 1) {
                            echo '
                            <li class="nav-item" data-key="submenu-ajustes">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-user-cog"></i>
                                    <p>
                                        Ajustes
                                        <i class="fas fa-angle-left right"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview">
                                    <li class="nav-item">
                                        <a href="usuarios.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Usuarios</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="config.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Configuración</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>';
                        } ?>
                        <?php if ($_SESSION['rol'] == 1) {
                            echo '
                            <li class="nav-item" data-key="submenu-inventario">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-box"></i>
                                <p>
                                    Inventario
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="bebidas.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Bebidas</p>
                                        </a>
                                    </li>
                                <li class="nav-item">
                                        <a href="platos.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Platos</p>
                                        </a>
                                    </li>
                                <li class="nav-item">
                                        <a href="salas.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Salas</p>
                                        </a>
                                    </li>
                                
                                    <li class="nav-item">
                                        <a href="proveedores.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Proveedores</p>
                                        </a>
                                    </li>
                                <li class="nav-item">
                                        <a href="inventario.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Inventario</p>
                                        </a>
                                    </li>
                                </ul>
                        </li>';
                        } ?>
                        <?php if ($_SESSION['rol'] == 1) {
                            echo '
                            <li class="nav-item" data-key="submenu-reportes">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>
                                    Reportes
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                        <a href="lista_ventas.php" class="nav-link">
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Historial Orden</p> 
                                        </a>
                                    </li>
                                <li class="nav-item">
                                        <a href="financiero.php" class="nav-link"> 
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Reporte Financiero</p> 
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="ImpServicio.php" class="nav-link"> 
                                            <i class="far fa-circle nav-icon"></i>
                                            <p>Imp Servicios</p> 
                                        </a>
                                    </li>
                                
                                </ul>
                        </li>';
                        } ?>

                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 2 || $_SESSION['rol'] == 3) {
                            echo '<li class="nav-item">
                            <a href="cocina.php" class="nav-link">
                            <i class="nav-icon fas fa-utensils"></i>
                            <p>Pedidos Cocina</p>
                            </a>
                        </li>';
                        } ?>

                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3 || $_SESSION['rol'] == 4) {
                            echo '<li class="nav-item">
                            <a href="bar.php" class="nav-link">
                                <i class="nav-icon fas fa-cocktail"></i>
                                <p>Pedidos Bar</p>
                            </a>
                        </li>';
                        } ?>

                        <?php if ($_SESSION['rol'] == 1 || $_SESSION['rol'] == 3) {
                            echo '<li class="nav-item">
                            <a href="mesero.php" class="nav-link">
                                <i class="nav-icon fas fa-check-circle"></i>
                                <p>Pedidos Mesero</p>
                            </a>
                        </li>';
                        } ?>

                        <li class="nav-item">
                            <a href="salir.php" class="nav-link">
                                <i class="nav-icon fas fa-power-off"></i>
                                <p>
                                    Salir
                                </p>
                            </a>
                        </li>

                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">

            <!-- Main content -->
            <div class="content">
                <div class="container-fluid py-2">

                    <!-- Modal de Sesión Expirada -->
                    <div class="modal fade" id="modalInactividad" tabindex="-1" aria-labelledby="modalInactividadLabel"
                        aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border border-primary">
                                <div class="modal-header text-white" style="background-color: #1E3A8A;">
                                    <h5 class="modal-title" id="modalInactividadLabel"><i class="fas fa-clock"></i>
                                        Sesión Inactiva</h5>
                                </div>
                                <div class="modal-body text-center">
                                    <p>Tu sesión está a punto de expirar por inactividad.</p>
                                    <p class="text-danger"><strong id="contadorModal">20</strong> segundos restantes.
                                    </p>
                                </div>
                                <div class="modal-footer justify-content-center" id="botonesModal">
                                    <button id="extenderSesion" class="btn btn-primary"
                                        style="background-color: #1E3A8A;">
                                        <i class="fas fa-sync-alt"></i>
                                        Extender Sesión</button>
                                    <button id="cerrarSesion" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i>
                                        Cerrar Sesión</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <style>
                        .modal-backdrop {
                            z-index: 1040 !important;
                        }

                        .modal {
                            z-index: 1050 !important;
                        }

                        body.modal-open {
                            overflow: hidden;
                            pointer-events: none;
                        }

                        body.modal-open .modal {
                            pointer-events: auto;
                        }
                    </style>

                    <script>
                        const tiempoInactividad = <?php echo $tiempo_expiracion; ?>; // 40 segundos
                        const tiempoModal = <?php echo $tiempo_modal; ?>; // 20 segundos
                        let tiempoRestante = tiempoInactividad;
                        let modalAbierto = false;
                        let cuentaRegresivaModal;
                        let inactividadTimeout;

                        function reiniciarTemporizador() {
                            if (modalAbierto) return; // No reiniciar si el modal está abierto

                            clearTimeout(inactividadTimeout);
                            inactividadTimeout = setTimeout(mostrarModal, tiempoInactividad * 1000);
                        }

                        function mostrarModal() {
                            modalAbierto = true;
                            $('#modalInactividad').modal('show');

                            let segundosRestantes = tiempoModal;
                            document.getElementById('contadorModal').innerText = segundosRestantes;

                            cuentaRegresivaModal = setInterval(() => {
                                segundosRestantes--;
                                document.getElementById('contadorModal').innerText = segundosRestantes;

                                if (segundosRestantes <= 0) {
                                    cerrarSesionForzado();
                                }
                            }, 1000);
                        }

                        function extenderSesion() {
                            // Hacer petición para mantener la sesión activa
                            fetch('?keepalive=1')
                                .then(response => {
                                    if (response.ok) {
                                        $('#modalInactividad').modal('hide');
                                        clearInterval(cuentaRegresivaModal);
                                        modalAbierto = false;
                                        reiniciarTemporizador();
                                    } else {
                                        cerrarSesionForzado();
                                    }
                                })
                                .catch(() => {
                                    cerrarSesionForzado();
                                });
                        }

                        function cerrarSesion() {
                            window.location.href = "salir.php";
                        }

                        function cerrarSesionForzado() {
                            clearInterval(cuentaRegresivaModal);
                            sessionStorage.setItem('sesionExpirada', 'true');
                            window.location.href = "salir.php?timeout=1";
                        }

                        // Event listeners
                        document.getElementById('extenderSesion').addEventListener('click', extenderSesion);
                        document.getElementById('cerrarSesion').addEventListener('click', cerrarSesion);

                        // Eventos que reinician el temporizador
                        const eventos = ['click', 'mousemove', 'keypress', 'scroll', 'keydown', 'touchstart'];
                        eventos.forEach(evento => {
                            document.addEventListener(evento, reiniciarTemporizador);
                        });

                        // Bloquear interacción cuando el modal está abierto
                        $(document).on('keydown', function (e) {
                            if (modalAbierto && e.keyCode === 9) { // Bloquear tabulación
                                e.preventDefault();
                            }
                        });

                        // Iniciar el temporizador al cargar la página
                        reiniciarTemporizador();

                        // Verificar si la sesión ya expiró al cargar la página
                        if (sessionStorage.getItem('sesionExpirada') === 'true') {
                            sessionStorage.removeItem('sesionExpirada');
                            cerrarSesionForzado();
                        }
                    </script>