<?php
session_start();
    include_once "includes/header.php";
    ?>
    <div class="card">
        <div class="card-header text-center">
            Mi Perfil
        </div>
        <div class="card-body">
            <div class="row">

                <div class="container mt-5">
                    <div class="card shadow-lg">
                        
                        <div class="card-body">
                            <?php
                            // Datos del perfil
                            $nombre = $_SESSION['nombre'];
                            $correo = $_SESSION['correo'];

                            echo "<p><strong>Nombre:</strong> $nombre</p>";
                            echo "<p><strong>Correo:</strong> $correo</p>";

                            switch ($_SESSION['rol']) {
                                case 1:
                                    echo "<p class='mt-4'><strong>Rol de admininistrador</strong></p>";
                                    echo "<p>Los privilegios son:</p>";
                                    echo "<ul>";
                                    echo "<li> Aqui iran los permisos del admin</li>";
                                    echo "</ul>";
                                    break;

                                case 2:
                                    echo "<p class='mt-4'><strong>Rol de cocinero</strong></p>";
                                    echo "<p>Los privilegios son:</p>";
                                    echo "<ul>";
                                    echo "<li> Aqui iran los permisos del cocinero</li>";
                                    echo "</ul>";
                                    break;

                                case 3:
                                    echo "<p class='mt-4'><strong>Rol de mesero</strong></p>";
                                    echo "<p>Los privilegios son:</p>";
                                    echo "<ul>";
                                    echo "<li>Puede crear nuevas ventas</li>";
                                    echo "<li>Puede asignar el cliente a la venta</li>";
                                    echo "<li>Puede agregar los platillos a la cuenta</li>";
                                    echo "</ul>";
                                    break;

                                case 4:
                                    echo "<p class='mt-4'><strong>Rol de bartender</strong></p>";
                                    echo "<p>Los privilegios son:</p>";
                                    echo "<ul>";
                                    echo "<li> Aqui iran los permisos del bartender</li>";
                                    echo "</ul>";
                                    break;

                                case 5:
                                    echo "<p class='mt-4'><strong>Rol de cajero</strong></p>";
                                    echo "<p>Los privilegios son:</p>";
                                    echo "<ul>";
                                    echo "<li> Aqui iran los permisos del cajero</li>";
                                    echo "</ul>";
                                    break;


                            }

                            ?>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
    <?php include_once "includes/footer.php";

?>