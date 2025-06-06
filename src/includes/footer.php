</div>
<!-- /.container-fluid -->
</div>
<!-- /.content -->
</div>
<!-- Main Footer -->
<footer class="main-footer">
    <strong>Copyright &copy; 2025 <a href="#">Restaurante San Isidro</a>.</strong>
    Todos los derechos reservados.
    <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 4.0.0
    </div>
</footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script src="../assets/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap -->
<script src="../assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE -->
<script src="../assets/dist/js/adminlte.min.js"></script>

<script src="../assets/plugins/chart.js/Chart.min.js"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="../assets/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="../assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>

<script src="../assets/js/sweetalert2.all.min.js"></script>
<script src="../assets/js/funciones.js"></script>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const submenuKeys = ["submenu-ajustes", "submenu-inventario", "submenu-reportes"];
        submenuKeys.forEach(key => {
            const saved = localStorage.getItem(key);
            if (saved === "open") {
                const parent = document.querySelector(`[data-key='${key}']`);
                if (parent) {
                    parent.classList.add("menu-open");
                    const link = parent.querySelector(".nav-link");
                    if (link) link.classList.add("active");
                }
            }
        });
        submenuKeys.forEach(key => {
            const parent = document.querySelector(`[data-key='${key}']`);
            if (parent) {
                const link = parent.querySelector(".nav-link");
                link.addEventListener("click", function () {
                    if (parent.classList.contains("menu-open")) {
                        localStorage.removeItem(key);
                    } else {
                        localStorage.setItem(key, "open");
                    }
                });
            }
        });
    });
</script>


</html>