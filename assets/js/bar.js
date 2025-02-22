document.addEventListener("DOMContentLoaded", function () {
  // Cargar los pedidos al iniciar la página
  cargarPedidosBar();

  // Refrescar la lista de pedidos cada 5 segundos para actualizar en tiempo real
  setInterval(cargarPedidosBar, 5000);
});

// Función para obtener los pedidos pendientes del bar
function cargarPedidosBar() {
  fetch("api/obtener_pedidos.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ tipo: 2 }),
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
          body: JSON.stringify({ id: pedido.id, tipo: 2 }),
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
              cambioEstadoBebida(id, "PENDIENTE");
            });
            $(".cambiarEstadoEnPreparacion").click(function () {
              let id = $(this).data("id");
              cambioEstadoBebida(id, "EN PREPARACION");
            });
            $(".cambiarEstadoListo").click(function () {
              let id = $(this).data("id");
              cambioEstadoBebida(id, "LISTO PARA SERVIR");
            });
          });
      });
    })
    .catch((error) => console.error("Error al cargar pedidos:", error));
}

function cambioEstadoBebida(id, estado) {

    // estamos usando la misma funcion api ya que utilizamos el id del detalle y haciendo eso no es necesario hacer otro cambiar.php
    fetch("api/cambiar_estado_plato.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id: id, estado: estado }),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Error en la respuesta del servidor.");
        }
        return response.json();
      })
      .then((data) => {
        if (data == "ok") {
          location.reload();
          //Swal.fire({
           // position: "top-end",
            // icon: "succes",
            // title: "Cambio exitoso el estado",
            // showConfirmButton: false,
            // timer: 2000,
          // });
        } else {
          // Swal.fire({
          //   position: "top-end",
          //   icon: "error",
          //   title: "Error al cambiar el estado",
          //   showConfirmButton: false,
          //   timer: 2000,
          // });
        }
      });
  }




// Función para mover un pedido de bar a órdenes listas cuando el bartender finaliza su preparación
function moverPedidoBar(id, header) {
  console.log(`Enviando pedido ${id} del bar a órdenes listas...`);

  fetch("api/mover_pedido_bar.php", {
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
        card.remove(); // Eliminar la tarjeta de la vista del bar
        console.log("Pedido de bar movido correctamente.");
      } else {
        console.error("Error al mover el pedido de bar:", result.message);
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
