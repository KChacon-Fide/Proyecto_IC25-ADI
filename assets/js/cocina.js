document.addEventListener("DOMContentLoaded", function () {
    // Cargar los pedidos al iniciar la página
    cargarPedidos();

    // Refrescar la lista de pedidos cada 5 segundos para actualizar en tiempo real
    setInterval(cargarPedidos, 5000);
});

// Función para obtener los pedidos pendientes desde la API
function cargarPedidos() {
    fetch("api/obtener_pedidos.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la respuesta del servidor.");
            }
            return response.json();
        })
        .then(data => {
            console.log("Pedidos recibidos:", data);
            let contenedor = document.getElementById("pedidos-container");
            if (!contenedor) return;
            contenedor.innerHTML = ""; // Limpiar el contenedor para evitar duplicados

            if (data.length === 0) {
                contenedor.innerHTML = "<p class='text-center text-muted'>No hay órdenes pendientes.</p>";
                return;
            }

            // Generar una tarjeta para cada pedido
            data.forEach(pedido => {
                let card = document.createElement("div");
                card.classList.add("pedido-card");

                card.innerHTML = `
                    <div class="card">
                        <div class="card-header bg-warning text-dark"
                             data-id="${pedido.id}" 
                             onclick="moverPedido(${pedido.id}, this)">
                            <h5>Mesa ${pedido.num_mesa}</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Plato:</strong> ${pedido.nombre}</p>
                            <p><strong>Cantidad:</strong> ${pedido.cantidad}</p>
                            <p><strong>Hora de pedido:</strong> ${pedido.fecha}</p>
                        </div>
                    </div>
                `;

                contenedor.appendChild(card);
            });
        })
        .catch(error => console.error("Error al cargar pedidos:", error));
}

// Función para mover un pedido a órdenes listas cuando el cocinero finaliza su preparación
function moverPedido(id, header) {
    console.log(`Enviando pedido ${id} a órdenes listas...`);

    fetch("api/mover_pedido.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ id: id })
    })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                let card = header.closest('.pedido-card');
                card.remove(); // Eliminar la tarjeta de la vista de cocina
                console.log("Pedido movido correctamente.");
            } else {
                console.error("Error al mover el pedido:", result.message);
                alert(result.message);
            }
        })
        .catch(error => {
            console.error("Error en la solicitud:", error);
        });
}

// Función para activar/desactivar el modo pantalla completa
function toggleFullscreen() {
    let elem = document.documentElement;
    let icon = document.getElementById("expand-icon");

    if (!document.fullscreenElement) {
        elem.requestFullscreen().then(() => {
            icon.classList.remove("fa-expand-arrows-alt");
            icon.classList.add("fa-compress-arrows-alt"); // Cambiar icono a modo reducir
        });
    } else {
        document.exitFullscreen().then(() => {
            icon.classList.remove("fa-compress-arrows-alt");
            icon.classList.add("fa-expand-arrows-alt"); // Cambiar icono a modo expandir
        });
    }
}
