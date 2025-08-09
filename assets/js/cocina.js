document.addEventListener("DOMContentLoaded", function () {

    cargarPedidos();



    setInterval(cargarPedidos, 10000);
});


async function cargarPedidos() {
    try {
        let params = new URLSearchParams(document.location.search);
        let vista = params.get("Vista");

        if (vista == undefined) vista = "TODOS";
        vista = vista.toUpperCase().replaceAll("_", " ");


        
       
        $("#botonesFiltro").html(`
            <button class="btn ${botonSegunEstado("TODOS", vista)}" id = "btnTodos">Todos</button>
            <button class="btn ${botonSegunEstado("PENDIENTE", vista)}" id = "btnPendientes" >Pendientes</button>
            <button class="btn ${botonSegunEstado("EN PREPARACION", vista)}" id = "btnEnPreparacion" >En Preparación</button>
            <button class="btn ${botonSegunEstado("LISTO PARA SERVIR", vista)}" id = "btnListoParaServir">Listo para Servir</button>
        `);

        $("#btnTodos").click(function () {
            window.location.href = "cocina.php?Vista=TODOS";
        });
        $("#btnPendientes").click(function () {
            window.location.href = "cocina.php?Vista=PENDIENTE";
        });
        $("#btnEnPreparacion").click(function () {
            window.location.href = "cocina.php?Vista=EN_PREPARACION";
        });
        $("#btnListoParaServir").click(function () {
            window.location.href = "cocina.php?Vista=LISTO_PARA_SERVIR";
        });
        $("#btnServido").click(function () {
            window.location.href = "cocina.php?Vista=SERVIDO";
        });


        let contenedor = document.getElementById("pedidos-container");
        if (!contenedor) {
            throw new Error("No se encontro pedidos-container");
        }

        contenedor.innerHTML = "";

        let pedidos = await obtenerListaPedidos(1, vista);


        if (pedidos == undefined || !Array.isArray(pedidos)) {
            contenedor.innerHTML = "No hay pedidos con el filtro indicado (" + vista + ")";
            return;
        }

        if (pedidos.length == 0) {
            contenedor.innerHTML = "No hay pedidos con el filtro indicado (" + vista + ")";
            return;
        }

        for (let index = 0; index < pedidos.length; index++) {
            const pedido = pedidos[index];
            console.log("Pedido numero ", pedido.num_mesa);

            let detallePlatos = await obtenerListaDetallePedidos(pedido.id, 1, vista);
            if (detallePlatos == undefined || !Array.isArray(detallePlatos)) {
                throw Error("No se encontraron platos para el pedido");
            }

            let platos = "";
            for (let index2 = 0; index2 < detallePlatos.length; index2++) {
                const plato = detallePlatos[index2];
                console.log("Detalle de plato recibidos:", plato, "Pedido: ", pedido.num_mesa);

                platos += obtenerDisenoDetallePedido(index2, plato.nombre, plato.cantidad, plato.observacion, plato.id, plato.estado);
            }

            let card = document.createElement("div");
            card.classList.add("col-md-4");

            card.innerHTML = obtenerDisenoPedido(
                pedido.num_mesa,
                pedido.id,
                pedido.estado,
                pedido.fecha,
                calcularTiempoTranscurrido(pedido.fecha),
                platos);

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
        }
    } catch (error) {
        console.error("Error al cargar pedidos:", error);
        let contenedor = document.getElementById("pedidos-container");
        contenedor.innerHTML = error.message;
    }
}


function obtenerDisenoPedido(MesaNumero, idPedido, estadoPedido, horaPedido, duracionPedido, detallePlatos) {
    let card = `
        <div class="card ${BordeSegunEstado(estadoPedido)} mb-3 shadow-sm">
            <div class="card-header ${fondoSegunEstado(estadoPedido)} text-white">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <strong>Mesa #${MesaNumero}</strong>
                    </h5>
                    <span class="badge ${fondoSegunEstado(estadoPedido)} fs-6">
                        ${iconoSegunEstado(estadoPedido)} ${estadoPedido}
                    </span>
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center mt-1">
                    <small><i class="fa-regular fa-clock me-1"></i>${duracionPedido}</small>
                    <small class="text-end">Pedido: ${idPedido}</small>
                </div>
            </div>
            <div class="card-body p-2">
                ${detallePlatos}
            </div>
        </div>
    `;
    return card;
}


function obtenerDisenoDetallePedido(index, nombrePlato, cantidadPlato, observacionesPedido, idDetallePedido, estadoPlato) {
    let card = `
    <div class="border-bottom ${BordeSegunEstado(estadoPlato)} mb-2 p-2 rounded bg-white">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1">
            <div class="d-flex align-items-center flex-wrap gap-1">
                <span class="badge bg-secondary">x${cantidadPlato}</span>
                <strong class="text-break">${nombrePlato}</strong>
            </div>
            <div class="btn-group btn-group-sm flex-wrap" role="group">
                <button class="btn ${botonSegunEstado("PENDIENTE", estadoPlato)} cambiarEstadoPendiente"
                    title="${toolTipSegunEstado("PENDIENTE", estadoPlato)}" 
                    type="button" data-id="${idDetallePedido}">
                    ${iconoSegunEstado("PENDIENTE")}
                </button>
                <button class="btn ${botonSegunEstado("EN PREPARACION", estadoPlato)} cambiarEstadoEnPreparacion"
                    title="${toolTipSegunEstado("EN PREPARACION", estadoPlato)}" 
                    type="button" data-id="${idDetallePedido}">
                    ${iconoSegunEstado("EN PREPARACION")}
                </button>
                <button class="btn ${botonSegunEstado("LISTO PARA SERVIR", estadoPlato)} cambiarEstadoListo"
                    title="${toolTipSegunEstado("LISTO PARA SERVIR", estadoPlato)}" 
                    type="button" data-id="${idDetallePedido}">
                    ${iconoSegunEstado("LISTO PARA SERVIR")}
                </button>
            </div>
        </div>
        ${observacionesPedido ? `<div class="mt-1"><small class="text-muted text-break">${observacionesPedido}</small></div>` : ""}
    </div>
    `;
    return card;
}


async function obtenerListaPedidos(idTipo, vista) {
    try {
        const response = await fetch("api/obtener_pedidos.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ tipo: idTipo, vista: vista }),
        });

        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor.");
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Error al cargar pedidos:", error.message);
        return null;
    }
}

async function obtenerListaDetallePedidos(idPedido, idTipo, vista) {
    try {
        const response = await fetch("api/obtener_detalle_pedidos.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ id: idPedido, tipo: idTipo, vista: vista }),
        });

        if (!response.ok) {
            throw new Error("Error en la respuesta del servidor.");
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error("Error al cargar pedidos:", error);
        return null;
    }
}


function BordeSegunEstado(estado) {
    switch (estado) {
        case "PENDIENTE":
            return "border-danger";

        case "EN PREPARACION":
            return "border-warning";

        case "LISTO PARA SERVIR":
            return "border-info";

        case "SERVIDO":
            return "border-success";

        case "ACTIVO":
            return "border-success";

        case "FINALIZADO":
            return "border-light";

        default:
            return "border-black";
    }
}

function fondoSegunEstado(estado) {
    switch (estado) {
        case "PENDIENTE":
            return "bg-danger";

        case "EN PREPARACION":
            return "bg-warning";

        case "LISTO PARA SERVIR":
            return "bg-info";

        case "SERVIDO":
            return "bg-success";

        case "ACTIVO":
            return "bg-success";

        case "FINALIZADO":
            return "bg-light";

        default:
            return "bg-black";
    }
}

function botonSegunEstado(tipoBoton, estadoActual) {
    switch (tipoBoton) {

        case "PENDIENTE":
            if (estadoActual == "PENDIENTE") {
                return "btn-danger";
            } else {
                return "btn-outline-danger";
            }

        case "EN PREPARACION":
            if (estadoActual == "EN PREPARACION") {
                return "btn-warning";
            } else {
                return "btn-outline-warning";
            }

        case "LISTO PARA SERVIR":
            if (estadoActual == "LISTO PARA SERVIR") {
                return "btn-info";
            } else {
                return "btn-outline-info";
            }
        case "SERVIDO":
            if (estadoActual == "SERVIDO") {
                return "btn-primary";
            } else {
                return "btn-outline-primary";
            }
        case "ACTIVO":
            if (estadoActual == "ACTIVO") {
                return "btn-success";
            } else {
                return "btn-outline-success";
            }
        case "FINALIZADO":
            if (estadoActual == "FINALIZADO") {
                return "btn-black";
            } else {
                return "btn-outline-black";
            }
        case "TODOS":
            if (estadoActual == "TODOS") {
                return "btn-dark";
            } else {
                return "btn-outline-dark";
            }
        default:
            return "btn-dark";
    }
}

function toolTipSegunEstado(tipoBoton, estadoActual) {
    switch (tipoBoton) {
        case "PENDIENTE":
            if (estadoActual == "PENDIENTE") {
                return "Estado actual pendiente";
            } else {
                return "Cambiar estado del plato a pendiente";
            }

        case "EN PREPARACION":
            if (estadoActual == "EN PREPARACION") {
                return "Estado actual en preparacion";
            } else {
                return "Cambiar estado del plato a preparacion";
            }

        case "LISTO PARA SERVIR":
            if (estadoActual == "LISTO PARA SERVIR") {
                return "Estado actual listo para servir";
            } else {
                return "Cambiar estado del plato a listo para servir";
            }
        case "LISTO PARA SERVIR":
            if (estadoActual == "LISTO PARA SERVIR") {
                return "Estado actual listo para servir";
            } else {
                return "Cambiar estado del plato a listo para servir";
            }
        case "ACTIVO":
            if (estadoActual == "ACTIVO") {
                return "Estado actual ACTIVO";
            } else {
                return "Cambiar estado del pedido a ACTIVO";
            }
        case "FINALIZADO":
            if (estadoActual == "FINALIZADO") {
                return "Estado actual FINALIZADO";
            } else {
                return "Cambiar estado del pedido a FINALIZADO";
            }

        default:
            return "TIPO NO CONOCIDO: " + tipoBoton;
    }
}

function iconoSegunEstado(estado) {
    switch (estado) {
        case "PENDIENTE":
            return '<i class="fa-solid fa-circle-exclamation mr-1"></i>';

        case "EN PREPARACION":
            return '<i class="fa-solid fa-kitchen-set"></i>';

        case "LISTO PARA SERVIR":
            return '<i class="fa-solid fa-bell-concierge mr-1"></i>';

        case "SERVIDO":
            return '<i class="fa-solid fa-circle-check"></i>';

        case "ACTIVO":
            return '<i class="fa-regular fa-file"></i>';
        case "FINALIZADO":
            return '<i class="fa-regular fa-file-lines"></i>';

        default:
            return '<i class="fa-solid fa-bug mr-1"></i>';
    }
}

function calcularTiempoTranscurrido(horaPedido) {

    let pedido = new Date(horaPedido);
    let ahora = new Date();


    let diferenciaMs = ahora - pedido;


    let minutos = Math.floor(diferenciaMs / 60000);
    let segundos = Math.floor((diferenciaMs % 60000) / 1000);

    return `${minutos} minutos y ${segundos} segundos`;
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
                location.reload();





            } else {
                console.error("Error al cambiar el estado");
            }
        })
        .catch((error) => console.error("Error en la solicitud:", error));
}


function insertarEnOrdenesListas(num_mesa, nombre, cantidad) {
    fetch("api/insertar_orden.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ num_mesa, nombre, cantidad }),
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
                card.remove();
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


function toggleFullscreen() {
    let elem = document.documentElement;
    let icon = document.getElementById("expand-icon");

    if (!document.fullscreenElement) {
        elem.requestFullscreen().then(() => {
            icon.classList.remove("fa-expand-arrows-alt");
            icon.classList.add("fa-compress-arrows-alt");
        });
    } else {
        document.exitFullscreen().then(() => {
            icon.classList.remove("fa-compress-arrows-alt");
            icon.classList.add("fa-expand-arrows-alt");
        });
    }
}

