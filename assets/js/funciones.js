document.addEventListener("DOMContentLoaded", function () {
  if ($("#detalle_pedido").length > 0) {
    listar();
  }

  $("#tbl").DataTable({
    language: {
      url: "//cdn.datatables.net/plug-ins/1.10.11/i18n/Spanish.json",
    },
    order: [[0, "desc"]],
  });

  $(".confirmar").submit(function (e) {
    e.preventDefault();
    Swal.fire({
      title: "Esta seguro de eliminar?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "SI, Eliminar!",
    }).then((result) => {
      if (result.isConfirmed) {
        this.submit();
      }
    });
  });

  $(document).on("click", ".addDetalle", function () {
    let id_producto = $(this).data("id");
    registrarDetalle(id_producto);
  });
  
  $(".addDetalleBebida").click(function () {
  let id_producto = $(this).data("id");
  let cantidad = parseInt($("#cantidad").val()) || 1; // Si no hay valor, toma 1 por defecto
  registrarDetalleBebida(id_producto, cantidad);
});

  $("#realizar_pedido").click(function (e) {
    e.preventDefault();
    let totalDePlatos = parseInt($("#totalPlatos").val());
    if (totalDePlatos == 0) {
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: "Por favor añadir productos para continuar",
        showConfirmButton: false,
        timer: 2000,
      });
      return false;
    }
    var action = "procesarPedido";
    var id_sala = $("#id_sala").val();
    var mesa = $("#mesa").val();
    var observacion = $("#observacion").val();
    $.ajax({
      url: "ajax.php",
      async: true,
      data: {
        procesarPedido: action,
        id_sala: id_sala,
        mesa: mesa,
        observacion: observacion,
      },
      success: function (response) {
        const res = JSON.parse(response);
        if (response != "error") {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Pedido Solicitado",
            showConfirmButton: false,
            timer: 2000,
          });
          setTimeout(() => {
            window.location =
              "mesas.php?id_sala=" + id_sala + "&mesas=" + res.mensaje;
          }, 1500);
        } else {
          Swal.fire({
            position: "top-end",
            icon: "error",
            title: "Error al generar",
            showConfirmButton: false,
            timer: 2000,
          });
        }
      },
      error: function (error) {
        alert(error);
      },
    });
  });

  $(".finalizarPedido").click(function () {
    let totalDePlatos = parseInt($("#totalPlatos").val());
    if (totalDePlatos == 0) {
      Swal.fire({
        position: "top-end",
        icon: "error",
        title: "Por favor añadir productos para continuar",
        showConfirmButton: false,
        timer: 2000,
      });
      return false;
    }
    var action = "finalizarPedido";
    var id_sala = $("#id_sala").val();
    var mesa = $("#mesa").val();
    $.ajax({
      url: "ajax.php",
      async: true,
      data: {
        finalizarPedido: action,
        id_sala: id_sala,
        mesa: mesa,
      },
      success: function (response) {
        const res = JSON.parse(response);
        if (response != "error") {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Pedido Finalizado",
            showConfirmButton: false,
            timer: 2000,
          });
          setTimeout(() => {
            window.location =
              "mesas.php?id_sala=" + id_sala + "&mesas=" + res.mensaje;
          }, 1500);
        } else {
          Swal.fire({
            position: "top-end",
            icon: "error",
            title: "Error al finalizar",
            showConfirmButton: false,
            timer: 2000,
          });
        }
      },
      error: function (error) {
        alert(error);
      },
    });
  });
});

function listar() {
  let html = "";
  let detalle = "detalle";
  let params = new URLSearchParams(document.location.search);
  $.ajax({
    url: "ajax.php",
    dataType: "json",
    data: {
      id_sala: params.get("id_sala"),
      id_mesa: params.get("mesa"),
      detalle: detalle,
    },
    success: function (response) {
      $("#totalPlatos").val(response.length);
      response.forEach((row) => {
        html += `<div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    
                    <p class="my-2">Producto: ${row.nombre}</p>
                    <p class="mb-2">Precio: ${row.precio}</p>
                    <p class="mb-2">Cantidad: ${row.cantidad}</p>
                    <div class="mt-1">
                       
                        <div class="form-group">
                                <label for="addObservacion">Observaciones</label>
                                <textarea id="addObservacion" class="form-control addObservacion"  rows="1" data-id="${row.id}"
                                    placeholder="Observaciones">${row.observacion}</textarea>
                            </div>
                        <button class="btn btn-danger eliminarPlato" type="button" data-id="${row.id}">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>`;
      });
      document.querySelector("#detalle_pedido").innerHTML = html;
      $(".eliminarPlato").click(function () {
        let id = $(this).data("id");
        eliminarPlato(id);
      });
      $(".addCantidad").change(function (e) {
        let id = $(this).data("id");
        cantidadPlato(e.target.value, id);
      });
      $(".addObservacion").change(function (e) {
        let id = $(this).data("id");
        addObservacion(e.target.value, id);
      });
    },
    error: function (error) {
      console.log(error);
    }
  });
}

function registrarDetalle(id_pro) {
  let action = "regDetalle";
  let params = new URLSearchParams(document.location.search);
  let cantidad = 1;
  $.ajax({
    url: "ajax.php",
    type: "POST",
    dataType: "json",
    data: {
      id: id_pro,
      id_mesa: params.get("mesa"),
      id_sala: params.get("id_sala"),
      cantidad: cantidad, // <-- ¡Asegurate de incluir esta línea!
      regDetalle: action
    },
    success: function (response) {
      if (response == "registrado") {
        listar();
        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Producto agregado",
          showConfirmButton: false,
          timer: 2000
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "No se pudo agregar el producto",
          showConfirmButton: false,
          timer: 2000
        });
      }
    },
    error: function (error) {
      console.log(error);
    }
  });
}



function registrarDetalleBebida(id_pro) {
  let action = "regDetalleBebida";
  let params = new URLSearchParams(document.location.search);
  
  // Capturamos la cantidad desde el input
  let cantidad = parseInt($("#cantidad").val()) || 1; // Si no hay valor, toma 1 por defecto
  
  $.ajax({
    url: "ajax.php",
    type: "POST",
    dataType: "json",
    data: {
      id: id_pro,
      id_mesa: params.get("mesa"),
      id_sala: params.get("id_sala"),
      cantidad: cantidad, // Pasamos la cantidad
      regDetalleBebida: action,
    },
    success: function (response) {
      if (response == "registrado") {
        listar();
      }
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "Producto agregado",
        showConfirmButton: false,
        timer: 2000,
      });
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function eliminarPlato(id) {
  let detalle = "Eliminar";
  $.ajax({
    url: "ajax.php",
    data: {
      id: id,
      delete_detalle: detalle,
    },
    success: function (response) {
      if (response == "ok") {
        Swal.fire({
          position: "top-end",
          icon: "success",
          title: "Producto Eliminado",
          showConfirmButton: false,
          timer: 2000,
        });
        listar();
      } else {
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: "Error al eliminar el producto",
          showConfirmButton: false,
          timer: 2000,
        });
      }
    },
  });
}

function cantidadPlato(cantidad, id) {
  let detalle = "cantidad";
  $.ajax({
    url: "ajax.php",
    data: {
      id: id,
      cantidad: cantidad,
      detalle_cantidad: detalle,
    },
    success: function (response) {
      if (response != "ok") {
        listar();
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: "Error al agregar cantidad",
          showConfirmButton: false,
          timer: 2000,
        });
      }
    },
  });
}

function addObservacion(texto, id) {
  $.ajax({
    url: "ajax.php",
    data: {
      id: id,
      agregar_observacion: texto,
    },
    success: function (response) {
      if (response != "ok") {
        listar();
        Swal.fire({
          position: "top-end",
          icon: "error",
          title: "Error al agregar cantidad",
          showConfirmButton: false,
          timer: 2000,
        });
      }
    },
  });
}


function btnCambiar(e) {
  e.preventDefault();
  const actual = document.getElementById("actual").value;
  const nueva = document.getElementById("nueva").value;
  if (actual == "" || nueva == "") {
    Swal.fire({
      position: "top-end",
      icon: "error",
      title: "Los campos estan vacios",
      showConfirmButton: false,
      timer: 2000,
    });
  } else {
    const cambio = "pass";
    $.ajax({
      url: "ajax.php",
      type: "POST",
      data: {
        actual: actual,
        nueva: nueva,
        cambio: cambio,
      },
      success: function (response) {
        if (response == "ok") {
          Swal.fire({
            position: "top-end",
            icon: "success",
            title: "Contraseña modificado",
            showConfirmButton: false,
            timer: 2000,
          });
          document.querySelector("#frmPass").reset();
          $("#nuevo_pass").modal("hide");
        } else if (response == "dif") {
          Swal.fire({
            position: "top-end",
            icon: "error",
            title: "La contraseña actual incorrecta",
            showConfirmButton: false,
            timer: 2000,
          });
        } else {
          Swal.fire({
            position: "top-end",
            icon: "error",
            title: "Error al modificar la contraseña",
            showConfirmButton: false,
            timer: 2000,
          });
        }
      },
    });
  }
}

function editarUsuario(id) {
  const action = "editarUsuario";
  $.ajax({
    url: "ajax.php",
    type: "GET",
    async: true,
    data: {
      editarUsuario: action,
      id: id,
    },
    success: function (response) {
      const datos = JSON.parse(response);
      $("#nombre").val(datos.nombre);
      $("#rol").val(datos.rol);
      $("#correo").val(datos.correo);
      $("#id").val(datos.id);
      $("#btnAccion").val("Modificar");
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function editarPlato(id) {
  const action = "editarProducto";
  $.ajax({
    url: "ajax.php",
    type: "GET",
    async: true,
    data: {
      editarProducto: action,
      id: id,
    },
    success: function (response) {
      const datos = JSON.parse(response);
      $("#plato").val(datos.nombre);
      $("#precio").val(datos.precio);
      $("#foto_actual").val(datos.foto_actual);
      $("#id").val(datos.id);
      $("#btnAccion").val("Modificar");
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function limpiar() {
  $("#formulario")[0].reset();
  $("#id").val("");
  $("#btnAccion").val("Registrar");
}

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
