document.addEventListener("DOMContentLoaded", function () {

    cargarOrdenesListas();
    setInterval(cargarOrdenesListas, 5000);
});

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

            data.forEach(orden => {
                let card = document.createElement("div");
                card.classList.add("pedido-card");
                card.setAttribute("id", `orden-${orden.id}`);


                let fecha = new Date(orden.fecha);
                let opcionesFecha = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                let fechaFormateada = fecha.toLocaleDateString('es-ES', opcionesFecha);

                card.innerHTML = `
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5>Mesa ${orden.num_mesa}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Plato:</strong> ${orden.nombre}</p>
                            <p><strong>Cantidad:</strong> ${orden.cantidad}</p>
                            <p><strong>Hora de pedido:</strong> ${fechaFormateada}</p>
                        </div>
                    </div>`;

                contenedor.appendChild(card);
            });
        })
        .catch(error => console.error("Error al cargar órdenes listas:", error));
}

