document.addEventListener("DOMContentLoaded", function () {
  // Cargar los pedidos al iniciar la página
  cargarPedidos();

  // Refrescar la lista de pedidos cada 5 segundos para actualizar en tiempo real

  setInterval(cargarPedidos, 5000);
});

// Función para obtener los pedidos pendientes desde la API
function cargarPedidos() {
  fetch("api/obtener_pedidos.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ tipo: 1 }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Error en la respuesta del servidor.");
      }
      return response.json();
    })
    .then((data) => {
      console.log("Pedidos recibidos:", data);
      let contenedor = document.getElementById("pedidos-container");
      if (!contenedor) return;
      contenedor.innerHTML = ""; // Limpiar el contenedor para evitar duplicados

      if (data.length === 0) {
        contenedor.innerHTML =
          "<p class='text-center text-muted'>No hay órdenes pendientes.</p>";
        return;
      }

      // Generar una tarjeta para cada pedido
      data.forEach((pedido) => {
        console.log("Pedido numero ", pedido.id);
        let card = document.createElement("div");
        card.classList.add("pedido-card");

        let infoPedido = ` <div class="card">
                    <div class="card-header bg-warning text-dark"
                        data-id="${pedido.id}" 
                        onclick="moverPedido(${pedido.id}, this)">
                        <h5>Mesa ${pedido.num_mesa}</h5>
                        <p><strong>Hora de pedido:</strong> ${pedido.fecha}</p>
                    </div>
                    <div class="card-body"> `;
        fetch("api/obtener_detalle_pedidos.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: pedido.id, tipo: 1 }),
        })
          .then((response2) => {
            if (!response2.ok) {
              throw new Error("Error en la respuesta del servidor.");
            }
            return response2.json();
          })
          .then((data2) => {
            console.log("Detalle de pedidos recibidos:", data2);
            data2.forEach((DetallePedido) => {
              infoPedido += ` <p><strong>${DetallePedido.cantidad}: </strong> ${DetallePedido.nombre} <strong>[${DetallePedido.estado}]</strong> 
              <button class="btn btn-danger cambiarEstadoPendiente" type="button" data-id="${DetallePedido.id}">Pendiente</button>
              <button class="btn btn-warning cambiarEstadoEnPreparacion" type="button" data-id="${DetallePedido.id}">En Preparacion</button>
              <button class="btn btn-success cambiarEstadoListo" type="button" data-id="${DetallePedido.id}">Listo</button>
              </p><p></p>`;
            });
            infoPedido += ` </div><div class="card-body"><p><strong>Observacion: </strong> ${pedido.observacion}</p></div>`;
            card.innerHTML = infoPedido;
            contenedor.appendChild(card);

            $(".cambiarEstadoPendiente").click(function () {
              let id = $(this).data("id");
              cambioEstadoPlato(id, "PENDIENTE");
            });
            $(".cambiarEstadoEnPreparacion").click(function () {
              let id = $(this).data("id");
              cambioEstadoPlato(id, "EN PREPARACION");
            });
            $(".cambiarEstadoListo").click(function () {
              let id = $(this).data("id");
              cambioEstadoPlato(id, "LISTO PARA SERVIR");
            });
          });
      });
    })
    .catch((error) => console.error("Error al cargar pedidos:", error));
}

function cambioEstadoPlato(id, estado, num_mesa, nombre, cantidad) {
  fetch("api/cambiar_estado_plato.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id, estado: estado }),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data == "ok") {
        if (estado === "LISTO PARA SERVIR") {
          insertarEnOrdenesListas(num_mesa, nombre, cantidad, fecha);
        } else {
          location.reload();
        }
      } else {
        console.error("Error al cambiar el estado");
      }
    })
    .catch((error) => console.error("Error en la solicitud:", error));
}

// Función para insertar en la tabla 'ordenes_listas'
function insertarEnOrdenesListas(num_mesa, nombre, cantidad) {
  fetch("api/insertar_orden.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ num_mesa, nombre, cantidad}),
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("Pedido insertado en órdenes listas.");
        location.reload();
      } else {
        console.error("Error al insertar en órdenes listas:", data.message);
      }
    })
    .catch((error) => console.error("Error en la solicitud:", error));
}


// Función para mover un pedido a órdenes listas cuando el cocinero finaliza su preparación
function moverPedido(id, header) {
  console.log(`Enviando pedido ${id} a órdenes listas...`);

  fetch("api/mover_pedido.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ id: id }),
  })
    .then((response) => response.json())
    .then((result) => {
      if (result.success) {
        let card = header.closest(".pedido-card");
        card.remove(); // Eliminar la tarjeta de la vista de cocina
        console.log("Pedido movido correctamente.");
      } else {
        console.error("Error al mover el pedido:", result.message);
        alert(result.message);
      }
    })
    .catch((error) => {
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
