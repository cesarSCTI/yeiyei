
    // ===========================================================
    // INGRESOS EXTRAS
    // ===========================================================

    // Renderiza la tabla de ingresos extras
    function renderIngresosExtras(lista) {
    const tbody = document.getElementById("tablaIngresosExtras");
    if (!tbody) return;

    // Se arma el HTML completo y se entrega a la tabla paginada (YamsTabla)
    let html = "";
    (lista || []).forEach(item => {
        html += `
        <tr>
            <td>${escaparHtml(item.evento)}</td>
            <td>${escaparHtml(item.fecha)}</td>
            <td data-monto="${parseFloat(item.costo) || 0}">$${parseFloat(item.costo).toFixed(2)}</td>
            <td>${escaparHtml(item.forma_pago || "")}</td>
            <td class="text-center">
            <button type="button"
                    class="btn btn-sm btn-outline-danger btn-delete-extra"
                    data-id="${item.id}">
                <i class="fas fa-trash"></i>
            </button>
            </td>
        </tr>
        `;
    });

    YamsTabla.reemplazarFilas(tbody.closest("table"), html, { buscador: false });
    prepararFiltrosExtras();
    }

    function escaparHtml(v) {
    return String(v ?? "").replace(/[&<>"']/g, c => (
        { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]
    ));
    }

    // Carga la lista desde el servidor
    function cargarIngresosExtras() {
    fetch("index.php?route=finanzas/extrasList")
        .then(r => r.json())
        .then(res => {
        if (!res.ok) {
            console.error("Error al cargar ingresos extras:", res);
            return;
        }
        renderIngresosExtras(res.data || []);
        })
        .catch(err => {
        console.error("Error de red al cargar ingresos extras:", err);
        });
    }

    // Alta de ingreso extra
    document.addEventListener("submit", function (e) {
    const form = e.target.closest("#formIngresoExtra");
    if (!form) return;

    e.preventDefault();
    const data = new FormData(form);

    fetch("index.php?route=finanzas/extrasStore", {
        method: "POST",
        body: data
    })
        .then(r => r.json())
        .then(res => {
        if (!res.ok) {
            alert(res.msg || "Error al guardar ingreso extra ❌");
            return;
        }

        // Cerrar modal
        const modalEl = document.getElementById("modalIngresoExtra");
        const modal = bootstrap.Modal.getInstance(modalEl);
        if (modal) modal.hide();

        // Limpiar formulario
        form.reset();

        // Recargar tabla
        cargarIngresosExtras();

        alert("Ingreso extra guardado ✅");
        })
        .catch(err => {
        console.error("Error de red al guardar ingreso extra:", err);
        alert("Error de conexión al guardar ingreso extra ❌");
        });
    });

    // Eliminar ingreso extra
    document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-delete-extra");
    if (!btn) return;

    const id = btn.dataset.id;
    if (!id) return;

    if (!confirm("¿Eliminar este ingreso extra?")) return;

    const body = new URLSearchParams({ id });

    fetch("index.php?route=finanzas/extrasDelete", {
        method: "POST",
        body
    })
        .then(r => r.json())
        .then(res => {
        if (!res.ok) {
            alert(res.msg || "No se pudo eliminar el ingreso extra ❌");
            return;
        }

        // Recargar tabla
        cargarIngresosExtras();

        alert("Ingreso extra eliminado ✅");
        })
        .catch(err => {
        console.error("Error de red al eliminar ingreso extra:", err);
        alert("Error de conexión al eliminar ingreso extra ❌");
        });
    });

    // Cargar ingresos extras al entrar al dashboard (una sola vez)
    document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("tablaIngresosExtras")) {
        cargarIngresosExtras();
    }
    });



// =========================    
// ESPECIALIDADES (CRUD)
// =========================
window.showAddEspecialidadModal = function () {
    const form = document.getElementById("formEspecialidad");
    if (form) form.reset();
    qs("#especialidadId").value = "";
    new bootstrap.Modal(document.getElementById("modalEspecialidad")).show();
};

const formEspecialidad = document.getElementById("formEspecialidad");
if (formEspecialidad) {
    formEspecialidad.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);
        const url = data.get("id") ? "index.php?route=especialidad/update" : "index.php?route=especialidad/store";

        fetch(url, { method: "POST", body: data })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                bootstrap.Modal.getInstance(document.getElementById("modalEspecialidad")).hide();
                location.reload(); // recarga tabla
            } else {
                alert("Error al guardar especialidad");
            }
        });
    });
}

document.addEventListener("click", function(e) {
    if (e.target.closest(".btn-edit-especialidad")) {
        const id = e.target.closest(".btn-edit-especialidad").dataset.id;
        fetch("index.php?route=especialidad/find", {
            method: "POST", body: new URLSearchParams({ id })
        })
        .then(r => r.json())
        .then(data => {
            qs("#especialidadId").value = data.id;
            qs("#especialidadNombre").value = data.nombre;
            qs("#especialidadDescripcion").value = data.descripcion;
            new bootstrap.Modal(document.getElementById("modalEspecialidad")).show();
        });
    }

    if (e.target.closest(".btn-delete-especialidad")) {
        const id = e.target.closest(".btn-delete-especialidad").dataset.id;
        if (confirm("¿Eliminar especialidad #" + id + "?")) {
            fetch("index.php?route=especialidad/delete", {
                method: "POST", body: new URLSearchParams({ id })
            })
            .then(r => r.json())
            .then(res => res.ok ? location.reload() : alert("Error al eliminar especialidad"));
        }
    }
});

// =========================
// USUARIOS (CRUD)
// =========================
window.showAddUserModal = function () {
    const form = document.getElementById("formUser");
    if (form) form.reset();
    qs("#userId").value = "";
    new bootstrap.Modal(document.getElementById("modalUser")).show();
};

const formUser = document.getElementById("formUser");
if (formUser) {
    formUser.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);
        const url = data.get("id") ? "index.php?route=user/update" : "index.php?route=user/store";

        fetch(url, { method: "POST", body: data })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                bootstrap.Modal.getInstance(document.getElementById("modalUser")).hide();
                location.reload();
            } else {
                alert("Error al guardar usuario ❌");
            }
        })
        .catch(err => console.error("Error usuario:", err));
    });
}

document.addEventListener("click", function (e) {
    // Editar usuario
    if (e.target.closest(".btn-edit-user")) {
        const id = e.target.closest(".btn-edit-user").dataset.id;
        fetch("index.php?route=user/find", { 
            method: "POST", 
            body: new URLSearchParams({ id }) 
        })
        .then(r => r.json())
        .then(data => {
            qs("#userId").value = data.id;
            qs("#userUsername").value = data.username;
            qs("#userName").value = data.name;
            qs("#userRole").value = data.role;
            qs("#userPassword").value = ""; // vacío al editar
            new bootstrap.Modal(document.getElementById("modalUser")).show();
        });
    }

    // Eliminar usuario
    if (e.target.closest(".btn-delete-user")) {
        const id = e.target.closest(".btn-delete-user").dataset.id;
        if (confirm("¿Eliminar usuario #" + id + "?")) {
            fetch("index.php?route=user/delete", { 
                method: "POST", 
                body: new URLSearchParams({ id }) 
            })
            .then(r => r.json())
            .then(res => res.ok ? location.reload() : alert("Error al eliminar usuario ❌"));
        }
    }
});

// =========================
// CLASES (CRUD)
// =========================
window.showAddClaseModal = function() {
    const form = document.getElementById("formClase");
    if (form) form.reset();
    const id = document.getElementById("claseId");
    if (id) id.value = "";
    new bootstrap.Modal(document.getElementById("modalClase")).show();
};

const formClase = document.getElementById("formClase");
if (formClase) {
    formClase.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);
        const url = data.get("id") ? "index.php?route=clase/update" : "index.php?route=clase/store";
        fetch(url, { method: "POST", body: data })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                bootstrap.Modal.getInstance(document.getElementById("modalClase")).hide();
                location.reload();
            } else {
                alert("Error al guardar la clase ❌");
            }
        });
    });
}

document.addEventListener("click", function(e){
    const claseArea = document.getElementById("clasesContent");
    if (!claseArea) return;

    // =====================================================
    // 🟢 EDITAR CLASE
    // =====================================================
    const btnEdit = e.target.closest(".btn-edit-clase");
    if (btnEdit) {
        const id = btnEdit.dataset.id;

        fetch("index.php?route=clase/find", { 
            method: "POST", 
            body: new URLSearchParams({ id }) 
        })
        .then(r => r.json())
        .then(data => {
            // Campos base
            qs("#claseId").value        = data.id;
            qs("#claseNombre").value    = data.nombre;
            qs("#claseCategoria").value = data.categoria;
            qs("#claseInstructor").value= data.instructor_id;
            qs("#claseInicio").value    = data.hora_inicio;
            qs("#claseFin").value       = data.hora_fin;
            qs("#claseEstado").value    = data.estado;

            // ✅ Marcar los días según lo guardado en la BD
            document.querySelectorAll('input[name="dias[]"]').forEach(chk => chk.checked = false);
            if (data.dias) {
                const diasSeleccionados = data.dias.split(",");
                document.querySelectorAll('input[name="dias[]"]').forEach(chk => {
                    if (diasSeleccionados.includes(chk.value)) chk.checked = true;
                });
            }

            // Mostrar modal
            new bootstrap.Modal(document.getElementById("modalClase")).show();
        })
        .catch(err => {
            console.error("Error al cargar clase:", err);
            alert("❌ Error al cargar los datos de la clase");
        });
    }

    // =====================================================
    // 🔴 ELIMINAR CLASE
    // =====================================================
    const btnDel = e.target.closest(".btn-delete-clase");
    if (btnDel) {
        const id = btnDel.dataset.id;
        if (confirm("¿Eliminar clase #" + id + "?")) {
            fetch("index.php?route=clase/delete", { 
                method: "POST", 
                body: new URLSearchParams({ id }) 
            })
            .then(r => r.json())
            .then(res => res.ok ? location.reload() : alert("❌ Error al eliminar clase"))
            .catch(err => console.error("Error al eliminar clase:", err));
        }
    }
});


// =========================
// CLASE - ALUMNOS (Asignación)
// =========================
window.showAddClaseAlumnoModal = function(claseId) {
    const form = document.getElementById("formClaseAlumno");
    if (form) form.reset();
    qs("#caId").value = "";
    qs("#caClaseId").value = claseId;
    new bootstrap.Modal(document.getElementById("modalClaseAlumno")).show();
};

const formClaseAlumno = document.getElementById("formClaseAlumno");
if (formClaseAlumno) {
    formClaseAlumno.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);
        const url = data.get("id") ? "index.php?route=clasealumno/update" : "index.php?route=clasealumno/assign";
        fetch(url, { method: "POST", body: data })
        .then(r=>r.json())
        .then(res=>{
            if (res.ok) {
                bootstrap.Modal.getInstance(document.getElementById("modalClaseAlumno")).hide();
                location.reload();
            } else {
                alert("Error al asignar alumno ❌");
            }
        });
    });
}

document.addEventListener("click", function(e){
    const area = document.getElementById("claseAlumnosContent");
    if (!area) return;

    // Editar asignación
    const btnEdit = e.target.closest(".btn-edit-clasealumno");
    if (btnEdit) {
        const id = btnEdit.dataset.id;
        fetch("index.php?route=clasealumno/find", { method:"POST", body:new URLSearchParams({id}) })
        .then(r=>r.json())
        .then(data=>{
            qs("#caId").value      = data.id;
            qs("#caClaseId").value = data.clase_id;
            qs("#caCliente").value = data.cliente_id;
            qs("#caEstado").value  = data.estado;
            new bootstrap.Modal(document.getElementById("modalClaseAlumno")).show();
        });
    }

    // Eliminar asignación
    const btnDel = e.target.closest(".btn-delete-clasealumno");
    if (btnDel) {
        const id = btnDel.dataset.id;
        if (confirm("¿Quitar alumno asignado #" + id + "?")) {
            fetch("index.php?route=clasealumno/delete", { method:"POST", body:new URLSearchParams({id}) })
            .then(r=>r.json())
            .then(res => res.ok ? location.reload() : alert("Error al eliminar asignación ❌"));
        }
    }
});

// =========================
// TIPOS DE CLASE (CRUD)
// =========================
window.showAddTipoClaseModal = function() {
    const form = document.getElementById("formTipoClase");
    if (form) form.reset();
    qs("#tipoClaseId").value = "";
    new bootstrap.Modal(document.getElementById("modalTipoClase")).show();
};

const formTipoClase = document.getElementById("formTipoClase");
if (formTipoClase) {
    formTipoClase.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);
        const url = data.get("id")
            ? "index.php?route=tipoclase/update"
            : "index.php?route=tipoclase/store";

        fetch(url, { method: "POST", body: data })
            .then(r => r.text()) // ⬅️ primero como texto
            .then(texto => {
                console.log("Respuesta cruda tipoclase:", texto);

                let res;
                try {
                    res = JSON.parse(texto);
                } catch (err) {
                    alert("Error en la respuesta del servidor (tipos de clase). Revisa la consola.");
                    console.error("Error al parsear JSON tipoclase:", err);
                    return;
                }

                if (res.ok) {
                    const modal = bootstrap.Modal.getInstance(
                        document.getElementById("modalTipoClase")
                    );
                    if (modal) modal.hide();

                    // recarga tabla (como ya lo hacías)
                    location.reload();
                } else {
                    alert("Error al guardar tipo de clase ❌");
                }
            })
            .catch(err => {
                console.error("Error en fetch tipoclase:", err);
                alert("Error de conexión con el servidor ❌");
            });
    });
}


document.addEventListener("click", function(e) {
    if (e.target.closest(".btn-edit-tipoclase")) {
        const id = e.target.closest(".btn-edit-tipoclase").dataset.id;
        fetch("index.php?route=tipoclase/find", {
            method: "POST",
            body: new URLSearchParams({ id })
        })
        .then(r => r.json())
        .then(data => {
            qs("#tipoClaseId").value = data.id;
            qs("#tipoClaseNombre").value = data.nombre;
            qs("#tipoClaseDescripcion").value = data.descripcion;

            const estadoEl = qs("#tipoClaseEstado");
            if (estadoEl) estadoEl.value = data.estado || "Activo";            
            new bootstrap.Modal(document.getElementById("modalTipoClase")).show();
        })
        .catch(err => {
            console.error("Error al cargar Tipo de Clase", err);
            alert("Error al cargar Tipo de Clase ❌");
        });
    }

    if (e.target.closest(".btn-delete-tipoclase")) {
        const id = e.target.closest(".btn-delete-tipoclase").dataset.id;
        if (confirm("¿Eliminar tipo de clase #" + id + "?")) {
            fetch("index.php?route=tipoclase/delete", {
                method: "POST",
                body: new URLSearchParams({ id })
            })
            .then(r => r.json())
            .then(res => res.ok ? location.reload() : alert("Error al eliminar tipo de clase ❌"));
        }
    }
});

// ===========================================================
// PAGOS: ABRIR MODAL BUSCAR CLIENTE SIN MODALES ANIDADOS
// (evita que se cierre el modal padre o falle "a veces")
// ===========================================================
window.origenBusquedaCliente = window.origenBusquedaCliente || null;

// ===========================================================
// SELECCIONAR CLIENTE (mantiene abierto el modal de pago al volver)
// ===========================================================
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".seleccionar-cliente");
  if (!btn) return;

  const clienteId = btn.dataset.id;
  const clienteNombre = btn.dataset.nombre;

  // Determinar origen antes de cerrar el modal
  const origen = window.origenBusquedaCliente || (
    document.getElementById("modalEditPago").classList.contains("show")
      ? "edit"
      : "add"
  );

  // Obtener referencia segura al modal de clientes
  const modalBuscarEl = document.getElementById("modalBuscarCliente");
  let modalBuscar = bootstrap.Modal.getInstance(modalBuscarEl);
  if (!modalBuscar) {
    modalBuscar = new bootstrap.Modal(modalBuscarEl);
  }

  // Escucha ÚNICA: cuando termine de cerrarse, reabrimos el correcto
  const handleHidden = function () {
    modalBuscarEl.removeEventListener("hidden.bs.modal", handleHidden);

    if (origen === "add") {
      qs("#clienteId").value = clienteId;
      qs("#clienteNombre").value = clienteNombre;
      bootstrap.Modal.getOrCreateInstance(
        document.getElementById("modalAddPago")
      ).show();
    } else if (origen === "edit") {
      qs("#editPagoClienteId").value = clienteId;
      qs("#editPagoClienteNombre").value = clienteNombre;
      bootstrap.Modal.getOrCreateInstance(
        document.getElementById("modalEditPago")
      ).show();
    }

    // Efecto visual opcional
    const field = origen === "add" ? qs("#clienteNombre") : qs("#editPagoClienteNombre");
    field.classList.add("highlight-green");
    setTimeout(() => field.classList.remove("highlight-green"), 1200);

    window.origenBusquedaCliente = null;
  };

  modalBuscarEl.addEventListener("hidden.bs.modal", handleHidden, { once: true });

  // Ahora sí, cerrar modal de clientes (forzado)
  modalBuscar.hide();
});

// ===========================================================
// BÚSQUEDA EN VIVO DE CLIENTES EN EL MODAL
// ===========================================================
document.addEventListener("keyup", function (e) {
  const input = e.target.closest("#buscarClienteInput");
  if (!input) return;

  const filter = input.value.toLowerCase();
  const filas = document.querySelectorAll("#clientesBody tr");

  filas.forEach(row => {
    const texto = row.innerText.toLowerCase();
    row.style.display = texto.includes(filter) ? "" : "none";
  });
});

// ===========================================================
// ACTUALIZAR ESTADOS Y DÍAS RESTANTES (automático y sincronizado)
// ===========================================================

// ✅ Corrige el error de -1 día (interpreta fechas en hora local)
// Convierte cadena "YYYY-MM-DD" a fecha local (medianoche)
function toLocalDateOnly(str) {
  if (!str) return null;
  const [datePart] = String(str).trim().split(" ");
  const [y, m, d] = datePart.split("-").map(Number);
  return new Date(y, (m || 1) - 1, d || 1);
}

// ===========================================================
// CALCULAR DÍAS RESTANTES (sin horas, sin UTC)
// ===========================================================
function calcularDiasRestantes(fechaProximoStr) {
  if (!fechaProximoStr) return 0;

  // 🔹 Tomamos solo la parte de la fecha (ignoramos la hora)
  const [anio, mes, dia] = fechaProximoStr.split(" ")[0].split("-").map(Number);

  // 🔹 Creamos una fecha local sin zona horaria (medianoche)
  const proximo = new Date(anio, mes - 1, dia);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  proximo.setHours(0, 0, 0, 0);

  // 🔹 Calculamos la diferencia en días enteros
  const diffMs = proximo - hoy;
  const diffDias = Math.round(diffMs / (1000 * 60 * 60 * 24));

  return diffDias;
}

// ===========================================================
// ACTUALIZAR ESTADOS Y DÍAS RESTANTES EN TIEMPO REAL
// ===========================================================
function actualizarEstadosPagos() {
  // Todas las filas, no solo la página visible de la tabla paginada
  const filas = YamsTabla.filas("#tablaPagos");
  if (!filas.length) return;

  filas.forEach(row => {
    const idPago = row.querySelector(".btn-edit-pago")?.dataset.id;
    const fechaProximo = row.querySelector("td:nth-child(4)")?.textContent.trim();
    const estadoCell = row.querySelector("td:nth-child(5)");
    const diasCell = row.querySelector("td:nth-child(6)");
    if (!idPago || !fechaProximo || !estadoCell || !diasCell) return;

    const dias = calcularDiasRestantes(fechaProximo);
    let nuevoEstado = "Pagado";

    if (dias < 0) nuevoEstado = "Vencido";
    else if (dias <= 5) nuevoEstado = "Por Vencer";

    const estadoActual = estadoCell.textContent.trim();

    // 🔄 Actualizar siempre los valores visibles
    estadoCell.innerHTML = `
      <span class="badge ${
        nuevoEstado === "Pagado"
          ? "bg-success"
          : nuevoEstado === "Vencido"
          ? "bg-danger"
          : "bg-warning"
      }">${nuevoEstado}</span>
    `;

    // Mostrar "Hoy vence" si quedan 0 días
    diasCell.textContent = dias === 0 ? "Hoy vence" : `${dias} días`;
    diasCell.className = dias < 0 ? "text-danger fw-bold" : "text-success fw-bold";

    // ⚡ Solo sincronizar si cambió el estado en la base de datos
    if (nuevoEstado !== estadoActual) {
      fetch("index.php?route=pago/update", {
        method: "POST",
        body: new URLSearchParams({
          id: idPago,
          estado: nuevoEstado,
          accion: "autoUpdateEstado"
        })
      })
      .then(r => r.json())
      .then(res => {
        if (res.ok) {
          console.log(`✅ Estado sincronizado (${idPago} → ${nuevoEstado})`);
        } else {
          console.warn(`⚠️ No se pudo actualizar estado del pago ID ${idPago}`);
        }
      })
      .catch(err => console.error("❌ Error al sincronizar estado:", err));
    }
  });

  // Actualiza búsqueda/orden de DataTables con los estados recalculados
  YamsTabla.refrescar("#tablaPagos");
  if (typeof aplicarFiltrosPagos === "function") aplicarFiltrosPagos();
}

// ===========================================================
// AUTOEJECUCIÓN AL ABRIR SECCIÓN PAGOS
// ===========================================================
document.addEventListener("click", function (e) {
  const link = e.target.closest(".nav-link");
  if (link && link.getAttribute("onclick")?.includes("pagos")) {
    setTimeout(actualizarEstadosPagos, 800);
  }
});

document.addEventListener("DOMContentLoaded", function () {
  if (document.querySelector("#pagosContent table")) {
    setTimeout(actualizarEstadosPagos, 800);
  }
});

// ===========================================================
// SELECCIONAR CLIENTE PARA CLASES
// ===========================================================
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".seleccionar-cliente-clase");
  if (!btn) return;

  const clienteId = btn.dataset.id;
  const clienteNombre = btn.dataset.nombre;

  // Obtener referencia al modal de búsqueda de cliente para clases
  const modalBuscarClaseEl = document.getElementById("modalBuscarClienteClase");
  const modalBuscarClase = bootstrap.Modal.getInstance(modalBuscarClaseEl);

  // Cerrar el modal y volver al modal de clase
  modalBuscarClaseEl.addEventListener("hidden.bs.modal", function handleClose() {
    modalBuscarClaseEl.removeEventListener("hidden.bs.modal", handleClose);

    // Llenar los campos del modal de clase
    document.getElementById("claseClienteId").value = clienteId;
    document.getElementById("claseClienteNombre").value = clienteNombre;
    document.getElementById("claseNombre").value = clienteNombre;

    // Reabrir el modal de clase
    bootstrap.Modal.getOrCreateInstance(document.getElementById("modalClase")).show();

    // Efecto visual breve
    const field = document.getElementById("claseClienteNombre");
    field.classList.add("highlight-green");
    setTimeout(() => field.classList.remove("highlight-green"), 1200);
  });

  modalBuscarClase.hide();
});

// ===========================================================
// BÚSQUEDA EN VIVO DE CLIENTES EN MODAL DE CLASES
// ===========================================================
document.addEventListener("keyup", function (e) {
  const input = e.target.closest("#buscarClienteClaseInput");
  if (!input) return;

  const filter = input.value.toLowerCase();
  const filas = document.querySelectorAll("#clientesClaseBody tr");

  filas.forEach(row => {
    const texto = row.innerText.toLowerCase();
    row.style.display = texto.includes(filter) ? "" : "none";
  });
});

// =========================================
// SELECCIONAR CLIENTE PARA CLASES
// =========================================
document.addEventListener("click", function (e) {
  const btn = e.target.closest(".seleccionar-cliente-clase");
  if (!btn) return;

  const clienteId = btn.dataset.id;
  const clienteNombre = btn.dataset.nombre;

  // Cerrar el modal de búsqueda de cliente
  const modalBuscar = bootstrap.Modal.getInstance(document.getElementById("modalBuscarClienteClase"));
  modalBuscar.hide();

  // Pequeño delay para esperar que el modal de búsqueda termine su animación
  setTimeout(() => {
    // Asignar datos al modal de clase
    document.getElementById("claseClienteId").value = clienteId;
    document.getElementById("claseNombre").value = clienteNombre;

    // Reabrir el modal de clase
    const modalClase = bootstrap.Modal.getOrCreateInstance(document.getElementById("modalClase"));
    modalClase.show();

    // Efecto visual rápido
    const field = document.getElementById("claseNombre");
    field.classList.add("highlight-green");
    setTimeout(() => field.classList.remove("highlight-green"), 1000);
  }, 400); // Delay suficiente para que el modal anterior se cierre visualmente  
});

// =========================================
// FINANZAS (COMPATIBILIDAD)
// =========================================
// En app.part1.js ya existe la lógica completa para actualizar TODAS las tarjetas
// (incluyendo ingresos extra) y además soporta scope=day para Operador.
// Aquí sólo nos aseguramos de refrescar cuando se muestre la sección Finanzas
// sin duplicar funciones ni romper cuando el elemento no existe.

async function cargarIngresosDiariosMesActual() {
  try {
    const resDia  = await fetch("index.php?route=finanzas/ingresosDiarios");
    const dataDia = await resDia.json();
    if (dataDia && dataDia.ok) {
      renderIngresosDiarios(dataDia);
    }
  } catch (e) {
    console.error("Error al cargar ingresos diarios:", e);
  }
}

function renderIngresosDiarios(data) {
  try {
    marcarColumnaHoyIngresosDiarios();    
    const tbody = document.getElementById("tablaIngresosDiariosBody");
    if (!tbody) return;

    const dias = data.dias || [];

    // Etiqueta con el mes y año
    const label = document.getElementById("labelMesIngresosDiarios");
    if (label) {
      const refDate = new Date(data.year, (data.month || 1) - 1, 1);
      let textoMes = refDate.toLocaleString("es-MX", {
        month: "long",
        year: "numeric"
      });
      textoMes = textoMes.charAt(0).toUpperCase() + textoMes.slice(1);
      label.textContent = textoMes;
    }

    if (!dias.length) {
      tbody.innerHTML = `
        <tr>
          <td colspan="8" class="text-center text-muted sin-datos">
            No hay datos para el mes actual.
          </td>
        </tr>`;
      return;
    }

    // Ordenar días por fecha
    const sorted = dias.slice().sort((a, b) => a.fecha.localeCompare(b.fecha));

    // Agrupar en semanas (Semana 1, 2, 3...) con columnas L–D
    let weeks = [];
    let currentWeek = null;
    let currentWeekNum = 1;
    let lastCol = 0;

    sorted.forEach(item => {
      const fecha = item.fecha;
      const total = parseFloat(item.total) || 0;

      const jsDate = new Date(fecha + "T00:00:00");
      let dow = jsDate.getDay();       // 0=Dom ... 6=Sab
      let col = (dow === 0) ? 7 : dow; // 1=Lun ... 7=Dom

      if (!currentWeek) {
        currentWeek = { num: currentWeekNum, dias: {} };
        weeks.push(currentWeek);
      } else if (col < lastCol) {
        // Cuando cambiamos de semana (de Domingo → Lunes)
        currentWeekNum++;
        currentWeek = { num: currentWeekNum, dias: {} };
        weeks.push(currentWeek);
      }

      currentWeek.dias[col] = { fecha, total };
      lastCol = col;
    });

    const hoyStr = new Date().toISOString().slice(0, 10);

    // Pintar tabla
    tbody.innerHTML = "";
    weeks.forEach(week => {
      const tr = document.createElement("tr");

      // Columna "Semana"
      const tdSemana = document.createElement("td");
      tdSemana.textContent = week.num;
      tdSemana.classList.add("fw-bold", "text-center", "col-semana");
      tr.appendChild(tdSemana);

      // Columnas Lunes–Domingo
      for (let col = 1; col <= 7; col++) {
        const td = document.createElement("td");
        td.classList.add("text-center", "align-middle");

        const info = week.dias[col];
        if (info) {
          const dia = info.fecha.substring(8, 10);
          const mes = info.fecha.substring(5, 7);
          const monto = parseFloat(info.total) || 0;

          // 🔹 AQUI conectamos con tu CSS: td[data-monto]
          td.setAttribute("data-monto", monto.toFixed(2));
          aplicarEstiloMontoCelda(td, monto);

          td.innerHTML = `
            <div class="fecha-dia fw-semibold">${dia}/${mes}</div>
            <div class="monto-dia">$${monto.toFixed(2)}</div>
          `;

          // Si es hoy, resaltamos (usa tu clase td-hoy si la definiste)
          if (info.fecha === hoyStr) {
            td.classList.add("td-hoy");
          }
        } else {
          td.innerHTML = `<span class="text-muted">-</span>`;
        }

        tr.appendChild(td);
      }

      tbody.appendChild(tr);
    });
    activarTooltips();
  } catch (e) {
    console.error("Error al renderizar ingresos diarios:", e);
  }
}


// Ejecutar automáticamente cuando se muestre la sección Finanzas (solo Admin)
document.addEventListener("DOMContentLoaded", () => {
  const finanzasSection = document.getElementById("finanzasContent");
  if (!finanzasSection) return; // Si el usuario es Operador, esta sección no existe

  const observer = new MutationObserver(() => {
    if (finanzasSection.style.display !== "none") {
      if (typeof window.actualizarResumenFinanzas === "function") {
        window.actualizarResumenFinanzas("month");
      }
      cargarIngresosDiariosMesActual();
    }
  });
  observer.observe(finanzasSection, { attributes: true, attributeFilter: ["style"] });
});

// =========================
// BUSCADOR POR NOMBRE EN PAGOS
// =========================
document.addEventListener("keyup", function (e) {
  const input = e.target.closest("#buscarPagoInput");
  if (!input) return;

  aplicarFiltrosPagos();
});

// =========================
// FILTRO POR ESTADO EN PAGOS
// =========================
document.addEventListener("change", function (e) {
  const control = e.target.closest("#filtroEstadoPago, #filtroFormaPago, #pagoDesde, #pagoHasta");
  if (!control) return;

  aplicarFiltrosPagos();
});

document.addEventListener("click", function (e) {
  if (!e.target.closest("#limpiarFiltrosPagos")) return;
  ["buscarPagoInput", "filtroEstadoPago", "filtroFormaPago", "pagoDesde", "pagoHasta"].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  aplicarFiltrosPagos();
});

// =========================
// FUNCIÓN CENTRAL DE FILTROS (nombre + estado)
// La tabla de pagos está paginada: se filtra con DataTables para
// buscar en todos los registros y no solo en la página visible.
// =========================
function aplicarFiltrosPagos() {
  const tabla = document.getElementById("tablaPagos");
  if (!tabla || !window.jQuery || !$.fn.DataTable.isDataTable(tabla)) return;

  const texto = document.getElementById("buscarPagoInput")?.value || "";
  const estado = document.getElementById("filtroEstadoPago")?.value || "";
  const forma = document.getElementById("filtroFormaPago")?.value || "";

  const dt = $(tabla).DataTable();
  dt.column(0).search(texto, false, true);
  YamsTabla.buscarExacto(dt, 4, estado);
  YamsTabla.buscarExacto(dt, 6, forma);
  dt.draw(); // el rango de fechas (columna 2) se evalúa en cada dibujado
}

// =========================
// PAGOS: TOTALES POR FORMA DE PAGO (ajuste 06)
// Suma los pagos que pasan los filtros, en todas las páginas.
// =========================
const FORMAS_PAGO = ["Efectivo", "Tarjeta", "Transferencia"];

function formatoMonedaMx(n) {
  return Number(n || 0).toLocaleString("es-MX", { style: "currency", currency: "MXN" });
}

function pintarTotales(contenedorId, filas, colMonto, colForma, etiqueta) {
  const cont = document.getElementById(contenedorId);
  if (!cont) return;

  const sumas = { Efectivo: 0, Tarjeta: 0, Transferencia: 0, Otra: 0 };
  let total = 0;
  filas.forEach(tr => {
    const monto = parseFloat(tr.cells[colMonto]?.dataset.monto || "0") || 0;
    const forma = (tr.cells[colForma]?.textContent || "").trim();
    sumas[FORMAS_PAGO.includes(forma) ? forma : "Otra"] += monto;
    total += monto;
  });

  const chip = (clase, nombre, valor) =>
    `<span class="yams-total yams-total-${clase}"><small>${nombre}</small><strong>${formatoMonedaMx(valor)}</strong></span>`;

  cont.innerHTML =
    chip("efectivo", "Efectivo", sumas.Efectivo) +
    chip("tarjeta", "Tarjeta", sumas.Tarjeta) +
    chip("transferencia", "Transferencia", sumas.Transferencia) +
    (sumas.Otra ? chip("otra", "Sin forma", sumas.Otra) : "") +
    chip("total", `Total · ${filas.length.toLocaleString("es-MX")} ${etiqueta}`, total);
}

function actualizarTotalesPagos() {
  pintarTotales("totalesPagos", YamsTabla.filasFiltradas("#tablaPagos"), 1, 6, "pagos");
}

window.prepararFiltrosPagos = function () {
  const tabla = document.getElementById("tablaPagos");
  if (!tabla || !window.jQuery || !$.fn.DataTable.isDataTable(tabla)) return;
  YamsTabla.rangoFechas(tabla, 2, "#pagoDesde", "#pagoHasta");
  $(tabla).on("draw.dt", actualizarTotalesPagos);
  actualizarTotalesPagos();
};


function marcarColumnaHoyIngresosDiarios() {
  const table = document.querySelector(".tabla-ingresos-diarios");
  if (!table) return;

  // JS: 0=Dom,1=Lun,...6=Sab -> lo convertimos a 1..7 (Lun..Dom)
  const d = new Date();
  const jsDay = d.getDay();
  const colIndex = jsDay === 0 ? 7 : jsDay; // Dom=7, Lun=1...

  // Encabezado
  const th = table.querySelector(`thead th:nth-child(${colIndex + 1})`); 
  // +1 porque la primera columna es "Semana"
  if (th) th.classList.add("col-hoy");

  // Celdas del body
  table.querySelectorAll(`tbody tr`).forEach(tr => {
    const td = tr.querySelector(`td:nth-child(${colIndex + 1})`);
    if (td) td.classList.add("col-hoy");
  });
}

/* =========================
   HELPERS VISUALES
========================= */

function aplicarEstiloMontoCelda(td, monto) {
  td.classList.remove("monto-cero", "monto-ok");
  if (!monto || Number(monto) <= 0) {
    td.classList.add("monto-cero");
  } else {
    td.classList.add("monto-ok");
  }
}

function activarTooltips() {
  const els = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  els.forEach(el => new bootstrap.Tooltip(el));
}

// ===========================================================
// PAGOS – ABRIR BUSCAR CLIENTE (FIX DEFINITIVO)
// ===========================================================
document.addEventListener("click", function (e) {
  const trigger = e.target.closest(
    '#btnBuscarClienteAdd, #btnBuscarClienteEdit, [data-bs-target="#modalBuscarCliente"]'
  );
  if (!trigger) return;

  e.preventDefault();
  e.stopPropagation();

  const modalBuscarEl = document.getElementById("modalBuscarCliente");
  const modalAddEl    = document.getElementById("modalAddPago");
  const modalEditEl   = document.getElementById("modalEditPago");

  if (!modalBuscarEl) {
    console.warn("No existe modalBuscarCliente");
    return;
  }

  const editShown = modalEditEl && modalEditEl.classList.contains("show");
  const addShown  = modalAddEl  && modalAddEl.classList.contains("show");

  // Guardar origen
  window.origenBusquedaCliente = editShown ? "edit" : "add";

  const modalBuscar = bootstrap.Modal.getOrCreateInstance(modalBuscarEl, {
    backdrop: "static",
    keyboard: true
  });

  const modalPadreEl = editShown ? modalEditEl : (addShown ? modalAddEl : null);

  if (!modalPadreEl) {
    modalBuscar.show();
    return;
  }

  const modalPadre = bootstrap.Modal.getOrCreateInstance(modalPadreEl);
  modalPadre.hide();

  modalPadreEl.addEventListener(
    "hidden.bs.modal",
    function onHidden() {
      modalPadreEl.removeEventListener("hidden.bs.modal", onHidden);
      modalBuscar.show();
    },
    { once: true }
  );
});

// =========================
// INGRESOS EXTRAS: BÚSQUEDA Y FILTROS (ajustes 07 y 08)
// Columnas: 0 Evento · 1 Fecha · 2 Costo · 3 Forma de pago · 4 Acciones
// =========================
function prepararFiltrosExtras() {
  const tabla = document.getElementById("tablaExtras");
  if (!tabla || tabla._yamsFiltros || !window.jQuery || !$.fn.DataTable.isDataTable(tabla)) {
    actualizarTotalesExtras();
    return;
  }
  tabla._yamsFiltros = true;
  YamsTabla.rangoFechas(tabla, 1, "#extraDesde", "#extraHasta");
  $(tabla).on("draw.dt", actualizarTotalesExtras);
  aplicarFiltrosExtras();
}

function aplicarFiltrosExtras() {
  const tabla = document.getElementById("tablaExtras");
  if (!tabla || !window.jQuery || !$.fn.DataTable.isDataTable(tabla)) return;

  const texto = document.getElementById("buscarExtra")?.value || "";
  const forma = document.getElementById("filtroFormaExtra")?.value || "";

  const dt = $(tabla).DataTable();
  dt.column(0).search(texto, false, true);
  YamsTabla.buscarExacto(dt, 3, forma);
  dt.draw();
}

function actualizarTotalesExtras() {
  pintarTotales("totalesExtras", YamsTabla.filasFiltradas("#tablaExtras"), 2, 3, "registros");
}

// Fechas en hora local (AAAA-MM-DD), no en UTC
function fechaLocalIso(d) {
  const p = n => String(n).padStart(2, "0");
  return `${d.getFullYear()}-${p(d.getMonth() + 1)}-${p(d.getDate())}`;
}

function rangoDePeriodo(periodo) {
  const hoy = new Date();
  const y = hoy.getFullYear(), m = hoy.getMonth();
  switch (periodo) {
    case "hoy": return [hoy, hoy];
    case "semana": {
      const lunes = new Date(hoy);
      lunes.setDate(hoy.getDate() - ((hoy.getDay() + 6) % 7));
      const domingo = new Date(lunes);
      domingo.setDate(lunes.getDate() + 6);
      return [lunes, domingo];
    }
    case "mes": return [new Date(y, m, 1), new Date(y, m + 1, 0)];
    case "mesAnterior": return [new Date(y, m - 1, 1), new Date(y, m, 0)];
    case "anio": return [new Date(y, 0, 1), new Date(y, 11, 31)];
    default: return null;
  }
}

document.addEventListener("input", function (e) {
  if (e.target.closest("#buscarExtra")) aplicarFiltrosExtras();
});

document.addEventListener("change", function (e) {
  const control = e.target.closest("#filtroFormaExtra, #extraDesde, #extraHasta, #extraPeriodo");
  if (!control) return;

  const periodo = document.getElementById("extraPeriodo");
  if (control.id === "extraPeriodo") {
    const rango = rangoDePeriodo(control.value);
    if (rango) {
      document.getElementById("extraDesde").value = fechaLocalIso(rango[0]);
      document.getElementById("extraHasta").value = fechaLocalIso(rango[1]);
    }
  } else if ((control.id === "extraDesde" || control.id === "extraHasta") && periodo) {
    periodo.value = ""; // fechas a mano = periodo personalizado
  }
  aplicarFiltrosExtras();
});

document.addEventListener("click", function (e) {
  if (!e.target.closest("#limpiarFiltrosExtras")) return;
  ["buscarExtra", "filtroFormaExtra", "extraDesde", "extraHasta", "extraPeriodo"].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = "";
  });
  aplicarFiltrosExtras();
});
