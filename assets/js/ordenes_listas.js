document.addEventListener("DOMContentLoaded", function () {
    // Cargar las órdenes listas al iniciar la página
    cargarOrdenesListas();

    // Refrescar la lista de órdenes cada 5 segundos
    setInterval(cargarOrdenesListas, 5000);
});

// Función para obtener órdenes listas desde la API
function cargarOrdenesListas() {
    fetch("api/obtener_ordenes_listas.php")
        .then(response => response.json())
        .then(data => {
            let contenedor = document.getElementById("ordenes-listas-container");
            if (!contenedor) return;
            contenedor.innerHTML = "";

            if (data.length === 0) {
                contenedor.innerHTML = "<p class='text-center text-muted'>No hay órdenes listas.</p>";
                return;
            }

            // Crear tarjetas de órdenes listas
            data.forEach(orden => {
                let card = document.createElement("div");
                card.classList.add("pedido-card");

                card.innerHTML = `
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5>Mesa ${orden.num_mesa}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Plato:</strong> ${orden.nombre}</p>
                            <p><strong>Cantidad:</strong> ${orden.cantidad}</p>
                            <p><strong>Hora de pedido:</strong> ${orden.fecha}</p>
                        </div>
                    </div>
                `;

                contenedor.appendChild(card);
            });
        })
        .catch(error => console.error("Error al cargar órdenes listas:", error));
}
