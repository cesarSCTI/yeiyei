    document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // VARIABLES GLOBALES
    // Variable global para saber desde qué modal se abrió la búsqueda
    let origenBusquedaCliente = null;

    // 🔧 Variable  global compartida entre modales
    window.origenBusquedaCliente = null;

    // =========================
    // EVENTOS

    // Evita que Bootstrap mantenga el "fade" bloqueado al cerrar un modal dentro de otro
    document.addEventListener("hidden.bs.modal", function () {
    document.body.classList.remove("modal-open");
    document.querySelectorAll(".modal-backdrop").forEach(b => b.remove());
    });


    // Cuando se abre el modal desde "Agregar Pago"
    document.addEventListener("click", function (e) {
    if (e.target.closest("#btnBuscarClienteAdd")) {
        origenBusquedaCliente = "add";
    }
    });

    // Cuando se abre el modal desde "Editar Pago"
    document.addEventListener("click", function (e) {
    if (e.target.closest("#btnBuscarClienteEdit")) {
        origenBusquedaCliente = "edit";
    }
    });



    // =========================
    // Utilidades
    // =========================
    window.qs = function(sel, root = document) { return root.querySelector(sel); };
    window.qsa = function(sel, root = document) { return Array.from(root.querySelectorAll(sel)); };

    // === LOGIN ===
    const loginForm = document.getElementById("loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const data = new FormData(this);

            fetch("index.php?route=auth/signin", {
                method: "POST",
                body: data
            })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    window.location.href = res.redirect;
                } else {
                    alert(res.message || "Usuario o contraseña incorrectos");
                }
            })
            .catch(err => {
                console.error("Error en login:", err);
                alert("Error de conexión con el servidor.");
            });
        });
    }

    // =========================
    // CAMBIO SECCIÓN PRINCIPAL
    // =========================
    let calendarInstance = null;

    window.showSection = function(section, el = null) {
    const sections = [
        "dashboardContent", "clientesContent", "instructoresContent",
        "pagosContent", "finanzasContent", "ingresosContent", "egresosContent",
        "usuariosContent", "clasesContent", "claseAlumnosContent",
        "tipoClasesContent", "especialidadesContent"
    ];

    // Ocultar todas las secciones
    sections.forEach(id => {
        const div = document.getElementById(id);
        if (div) div.style.display = "none";
    });

    // Mostrar solo la sección seleccionada
    const active = document.getElementById(section + "Content");
    if (active) active.style.display = "block";

    // Marcar enlace activo
    qsa(".nav-link").forEach(link => link.classList.remove("active"));
    if (el) el.classList.add("active");

    // Celular: el menú es una fila deslizable; deja visible la opción activa (ajuste 20)
    if (el && window.innerWidth < 768) {
        el.scrollIntoView({ block: "nearest", inline: "center" });
        window.scrollTo(0, 0);
    }

    // Inicializar calendario si estamos en Clases
    if (section === "clases") {
        setTimeout(() => {
        if (typeof initCalendar === "function") {
            if (!calendarInstance) {
            calendarInstance = initCalendar();
            } else {
            calendarInstance.render();
            }
        }
        }, 300);
    } else {
        if (calendarInstance) {
        calendarInstance.destroy();
        calendarInstance = null;
        }
    }

    // (Se quitó el registro de 'shown.bs.modal' que se repetía en cada cambio de
    // sección y acumulaba listeners; Bootstrap ya pone modal-open por sí mismo.)
    };

    // =========================
    // Restaurar última sección activa (post-guardado)
    // =========================
    const lastSection = localStorage.getItem('lastSection');
    if (lastSection) {
        // Ej: 'clientes'
        showSection(lastSection);
        localStorage.removeItem('lastSection');
    }


    // =========================
    // ANIMACIONES
    // =========================

    function animateNumbers() {
        qsa(".animate-number").forEach(num => {
            const finalNumber = num.textContent;
            num.textContent = "0";
            setTimeout(() => { num.textContent = finalNumber; }, 500);
        });
    }

    // =========================
    // CLIENTES (CRUD)
    // =========================
    window.showAddClientModal = function() {
        const form = document.getElementById("formAddClient");
        if (form) form.reset();
        const id = document.getElementById("clientId");
        if (id) id.value = "";
        const modalEl = document.getElementById("clientModal");
        if (modalEl) new bootstrap.Modal(modalEl).show();
    };

    const formAddClient = document.getElementById("formAddClient");
    if (formAddClient) {
    formAddClient.addEventListener("submit", function (e) {
        e.preventDefault();

        // construimos JSON y lo dejamos en el hidden
        prepararHorariosCliente();

        const data = new FormData(this);
        const url = data.get("id")
        ? "index.php?route=cliente/update"
        : "index.php?route=cliente/store";

        fetch(url, { method: "POST", body: data })
        .then((r) => r.json())
        .then((res) => {
            if (res.ok) {
            // ✅ Cerrar modal correctamente
            const modalEl = document.getElementById("clientModal");
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();

            // ✅ Limpiar backdrop y restaurar scroll
            document.querySelectorAll(".modal-backdrop").forEach((b) => b.remove());
            document.body.classList.remove("modal-open");
            document.body.style.overflow = "auto";

            // ✅ Resetear formulario
            formAddClient.reset();

            // Recordar que estábamos en Clientes
            localStorage.setItem('lastSection', 'clientes');

            // ✅ Recargar lista sin forzar reload total (más suave)
            setTimeout(() => location.reload(), 200);
            } else {
            alert("Error al guardar cliente ❌");
            }
        })
        .catch((err) => {
            console.error("Error al guardar cliente:", err);
            alert("Error de conexión con el servidor ❌");
        });
    });
    }

    document.addEventListener("click", function(e){
        const clientesArea = document.getElementById("clientesContent");
        if (!clientesArea) return;

        // =========================
        // EDITAR CLIENTE (ya existía)
        // =========================
        if (clientesArea.contains(e.target) && e.target.closest(".btn-edit")) {
            const id = e.target.closest(".btn-edit").dataset.id;

            fetch("index.php?route=cliente/find", {
                method: "POST",
                body: new URLSearchParams({ id })
            })
            .then(r => r.json())
            .then(data => {
                // Datos generales
                qs("#clientId").value    = data.id;
                qs("#nombre").value      = data.nombre;
                qs("#telefono").value    = data.telefono;
                qs("#tipo_clase").value  = data.tipo_clase;
                qs("#estado").value      = data.estado;

                // Fecha de registro
                const fechaInput = qs("#fecha_registro");
                if (fechaInput) {
                    if (data.fecha_registro) {
                        const soloFecha = data.fecha_registro.split(" ")[0]; // "YYYY-MM-DD"
                        fechaInput.value = soloFecha;
                    } else {
                        fechaInput.value = "";
                    }
                }

                // === DÍAS Y HORAS (para el modal de edición) ===
                const cont = document.getElementById("diasHorarios");
                if (cont) {
                    // Limpiar
                    cont.querySelectorAll(".dia-check").forEach(chk => chk.checked = false);
                    cont.querySelectorAll(".hora-dia").forEach(input => {
                        input.disabled = true;
                        input.value = "";
                    });

                    // Pintar desde backend
                    if (Array.isArray(data.horarios)) {
                        data.horarios.forEach(h => {
                            const dia    = h.dia;
                            const horaBD = h.hora || ""; // 'HH:MM:SS'

                            const horaInput = cont.querySelector(`.hora-dia[data-dia="${dia}"]`);
                            if (!horaInput) return;

                            horaInput.disabled = false;
                            horaInput.value = horaBD.substring(0, 5);

                            const col = horaInput.closest(".col-md-6, .col-12");
                            if (!col) return;
                            const chk = col.querySelector(".dia-check");
                            if (chk) chk.checked = true;
                        });
                    }
                }

                // Mostrar modal de edición
                new bootstrap.Modal(document.getElementById("clientModal")).show();
            });
            return;
        }

        // =========================
        // 🔵 NUEVO: VER DÍAS Y HORARIOS (solo lectura)
        // =========================
        if (clientesArea.contains(e.target) && e.target.closest(".btn-horarios")) {
            const id = e.target.closest(".btn-horarios").dataset.id;

            fetch("index.php?route=cliente/find", {
                method: "POST",
                body: new URLSearchParams({ id })
            })
            .then(r => r.json())
            .then(data => {
                // Nombre en el título del modal
                const nombreEl = document.getElementById("horarioClienteNombre");
                if (nombreEl) nombreEl.textContent = data.nombre || "";

                // Cuerpo de la tabla
                const tbody = document.getElementById("horariosClienteBody");
                if (!tbody) return;

                tbody.innerHTML = "";

                if (Array.isArray(data.horarios) && data.horarios.length) {
                    data.horarios.forEach(h => {
                        const dia  = h.dia || "";
                        const hora = (h.hora || "").substring(0, 5); // HH:MM

                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${dia}</td>
                            <td>${hora}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                Sin horarios asignados
                            </td>
                        </tr>
                    `;
                }

                // Mostrar modal de solo lectura
                const modalEl = document.getElementById("modalVerHorariosCliente");
                if (modalEl) {
                    new bootstrap.Modal(modalEl).show();
                }
            })
            .catch(err => {
                console.error("Error al cargar horarios del cliente:", err);
                alert("Error al cargar los horarios del cliente ❌");
            });

            return;
        }

        // =========================
        // ELIMINAR CLIENTE (ya existía)
        // =========================
        if (clientesArea.contains(e.target) && e.target.closest(".btn-delete")) {
            const id = e.target.closest(".btn-delete").dataset.id;
            if (confirm("¿Eliminar cliente #" + id + "?")) {
                fetch("index.php?route=cliente/delete", {
                    method: "POST",
                    body: new URLSearchParams({ id })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.ok) {
                        localStorage.setItem('lastSection', 'clientes');
                        location.reload();
                    } else {
                        alert("Error al eliminar cliente");
                    }
                });
            }
        }
    });




    // ====================================================================
    // CLIENTES – Días y Horarios de Asistencia
    // ====================================================================
    function initClienteDiasHorarios() {
    const contenedor = document.getElementById('diasHorarios');
    if (!contenedor) return; // por si el modal aún no existe en el DOM

    contenedor.querySelectorAll('.dia-check').forEach(chk => {
        chk.addEventListener('change', function () {
        const col = this.closest('.col-md-6');
        if (!col) return;
        const horaInput = col.querySelector('.hora-dia');
        if (!horaInput) return;

        horaInput.disabled = !this.checked;
        if (!this.checked) {
            horaInput.value = '';
        }
        });
    });
    }

    //==========================================
    //Armar el JSON de horarios antes de guardar
    //==========================================
    function prepararHorariosCliente() {
    const horarios = [];
    const contenedor = document.getElementById('diasHorarios');
    if (!contenedor) return;

    contenedor.querySelectorAll('.hora-dia').forEach(input => {
        if (!input.disabled && input.value) {
        horarios.push({
            dia: input.dataset.dia,
            hora: input.value
        });
        }
    });

    const hidden = document.getElementById('horarios');
    if (hidden) {
        hidden.value = JSON.stringify(horarios);
    }
    }

    // ===============================
    // CLIENTES - Días y Horarios
    // Activar/desactivar input hora
    // ===============================
    document.addEventListener('change', function (e) {
    // Solo nos interesa cuando cambia un checkbox de día
    if (!e.target.classList.contains('dia-check')) return;

    // Buscamos el contenedor donde está también el input hora
    const contenedor = e.target.closest('.col-md-6, .col-12');
    if (!contenedor) return;

    const horaInput = contenedor.querySelector('.hora-dia');
    if (!horaInput) return;

    // Si el checkbox está marcado, habilitamos la hora
    horaInput.disabled = !e.target.checked;

    // Si se desmarca, limpiamos el valor
    if (!e.target.checked) {
        horaInput.value = '';
    }
    });


    // =========================
    // INSTRUCTORES (CRUD)
    // =========================
    window.showAddInstructorModal = function() {
        const form = document.getElementById("formAddInstructor");
        if (form) form.reset();
        const id = document.getElementById("instructorId");
        if (id) id.value = "";
        const modalEl = document.getElementById("instructorModal");
        if (modalEl) new bootstrap.Modal(modalEl).show();
    };

    const formAddInstructor = document.getElementById("formAddInstructor");
    if (formAddInstructor) {
        formAddInstructor.addEventListener("submit", function (e) {
            e.preventDefault();
            const data = new FormData(this);
            const url = data.get("id") ? "index.php?route=instructor/update" : "index.php?route=instructor/store";
            fetch(url, { method: "POST", body: data })
            .then(r => r.json())
            .then(res => res.ok ? location.reload() : alert("Error al guardar instructor"));
        });
    }

    document.addEventListener("click", function(e){
        const instArea = document.getElementById("instructoresContent");

        const btnEditInst = e.target.closest(".btn-edit-instructor") || e.target.closest("#instructoresContent .btn-edit");
        if (instArea && instArea.contains(e.target) && btnEditInst) {
            const id = btnEditInst.dataset.id;
            fetch("index.php?route=instructor/find", { method: "POST", body: new URLSearchParams({id}) })
            .then(r=>r.json())
            .then(data => {
                qs("#instructorId").value = data.id;
                qs("#nombreInstructor").value = data.nombre;
                qs("#telefonoInstructor").value = data.telefono;
                (qs("#especialidadInstructor") || qs("#especialidad")).value = data.especialidad;
                qs("#estadoInstructor").value = data.estado;
                new bootstrap.Modal(document.getElementById("instructorModal")).show();
            });
        }

        const btnDelInst = e.target.closest(".btn-delete-instructor") || e.target.closest("#instructoresContent .btn-delete");
        if (instArea && instArea.contains(e.target) && btnDelInst) {
            const id = btnDelInst.dataset.id;
            const row = btnDelInst.closest("tr");

            if (confirm("¿Desactivar instructor #" + id + "?")) {
                btnDelInst.disabled = true;

                fetch("index.php?route=instructor/delete", {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: new URLSearchParams({ id })
                })
                .then(r => r.json())
                .then(res => {
                    if (res.ok) {
                        if (row) row.remove();

                        const tbody = instArea.querySelector("table tbody");
                        if (tbody && tbody.querySelectorAll("tr").length === 0) {
                            tbody.innerHTML = `
                                <tr>
                                    <td colspan="7" class="text-center">No hay instructores registrados</td>
                                </tr>
                            `;
                        }
                    } else {
                        btnDelInst.disabled = false;
                        alert(res.msg || "Error al desactivar instructor");
                    }
                })
                .catch(err => {
                    console.error("Error al desactivar instructor:", err);
                    btnDelInst.disabled = false;
                    alert("Error de conexión al desactivar instructor");
                });
            }
        }
    });

    // =========================
    // PAGOS (CRUD + Historial + WhatsApp)
    // =========================

    // Editar Pago (botón verde)
    document.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-edit-pago");
    if (!btn) return;

    const id = btn.dataset.id;
    fetch("index.php?route=pago/find", { 
        method: "POST", 
        body: new URLSearchParams({ id }) 
    })
    .then(r => r.json())
    .then(data => {
        qs("#editPagoId").value            = data.id;
        qs("#editPagoClienteId").value     = data.cliente_id;
        qs("#editPagoClienteNombre").value = data.cliente || "";
        qs("#editPagoMonto").value         = data.monto;

        new bootstrap.Modal(document.getElementById("modalEditPago")).show();

        setTimeout(() => {
        const selectForma = qs("#editPagoFormaPago");
        const forma = (data.forma_pago || "").trim().toLowerCase();

        if (forma === "efectivo") selectForma.value = "Efectivo";
        else if (forma === "transferencia") selectForma.value = "Transferencia";
        else if (forma === "tarjeta") selectForma.value = "Tarjeta";
        else selectForma.value = "";

        console.log("Forma de pago mostrada en modal:", selectForma.value);
        }, 300);

        qs("#editPagoFecha").value         = data.fecha_pago;
        qs("#editPagoProximo").value       = data.proximo_pago;
        qs("#editPagoEstado").value       = data.estado;

        // 🔹 Mostrar el modal con los datos cargados
        new bootstrap.Modal(document.getElementById("modalEditPago")).show();
    })
    .catch(err => {
        console.error("Error al obtener pago:", err);
        alert("No se pudo cargar la información del pago ❌");
    });
    });


    // =========================
    // NUEVO PAGO
    // =========================
    const formAddPago = document.getElementById("formAddPago");
    if (formAddPago) {
        formAddPago.addEventListener("submit", function(e) {
            e.preventDefault();
            const data = new FormData(this);

            fetch("index.php?route=pago/store", { method: "POST", body: data })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    // cerrar modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById("modalAddPago"));
                    if (modal) modal.hide();
                    
                    // limpiar formulario después de guardar
                    formAddPago.reset();
                    document.getElementById("clienteId").value = "";
                    document.getElementById("clienteNombre").value = "";

                    // refrescar tabla pagos
                    reloadPagos();

                    // actualizar resumen finanzas
                    if (res.finanzas) updateFinanzasDom(res.finanzas);

                    alert("Pago registrado ✅");
                } else {
                    alert("Error al registrar pago ❌");
                }
            })
            .catch(err => {
                console.error("Error en pago:", err);
                alert("Error de conexión al registrar pago ❌");
            });
        });
    }

    // ===========================================================
    // LIMPIAR FORMULARIO AL CERRAR "Registrar Pago"
    // ===========================================================
    const modalAddPago = document.getElementById("modalAddPago");
    if (modalAddPago) {
    modalAddPago.addEventListener("hidden.bs.modal", function () {
        const form = modalAddPago.querySelector("form");
        if (form) form.reset();

        // Limpiar campos especiales
        const idInput = document.getElementById("clienteId");
        const nombreInput = document.getElementById("clienteNombre");
        if (idInput) idInput.value = "";
        if (nombreInput) nombreInput.value = "";
    });
    }
    
    // Función para recargar tabla de pagos
    function reloadPagos() {
        fetch("index.php?route=pago/index")
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, "text/html");
            const newBody = doc.querySelector("#tablaPagos tbody");
            if (newBody) {
                YamsTabla.reemplazarFilas("#tablaPagos", newBody.innerHTML, { buscador: false });
                if (typeof aplicarFiltrosPagos === "function") aplicarFiltrosPagos();
            }
        });
    }

    // Guardar edición de pago (actualiza solo la fila)
    const formEditPago = document.getElementById("formEditPago");
    if (formEditPago) {
    formEditPago.addEventListener("submit", function (e) {
        e.preventDefault();
        const data = new FormData(this);

        fetch("index.php?route=pago/update", { method: "POST", body: data })
        .then((r) => r.json())
        .then((res) => {
            if (!res.ok) return alert("Error al actualizar el pago ❌");

            const pago = res.pago;

            // Buscar la fila correspondiente
            const row = document.querySelector(
            `button.btn-edit-pago[data-id="${pago.id}"]`
            )?.closest("tr");

            if (row) {
            // Días restantes calculados por el servidor (misma regla que la tabla)
            const dias = parseInt(pago.dias_restantes, 10);

            // ✅ Mostrar solo la fecha (sin hora)
            const fechaSolo = pago.fecha_pago.split(" ")[0];

            // Actualizamos las celdas (Cliente, Monto, Fecha, Próximo Pago, Estado, Días)
            row.querySelector("td:nth-child(1)").textContent = pago.cliente;
            row.querySelector("td:nth-child(2)").textContent =
                "$" + parseFloat(pago.monto).toFixed(2);
            row.querySelector("td:nth-child(2)").dataset.monto = parseFloat(pago.monto) || 0;
            row.querySelector("td:nth-child(3)").textContent = fechaSolo;
            row.querySelector("td:nth-child(4)").textContent = pago.proximo_pago;
            row.querySelector("td:nth-child(5)").innerHTML = `
                <span class="badge ${
                pago.estado == "Pagado"
                    ? "bg-success"
                    : pago.estado == "Vencido"
                    ? "bg-danger"
                    : "bg-warning"
                }">${pago.estado}</span>
            `;
            row.querySelector("td:nth-child(6)").textContent =
                isNaN(dias) ? "--" : (dias === 0 ? "Hoy vence" : `${dias} días`);
            row.querySelector("td:nth-child(6)").className =
                dias < 0 ? "text-danger fw-bold" : "text-success fw-bold";

            // 🔥 Regeneramos la celda de acciones según el estado
            let accionesHTML = `
                <button class="btn btn-sm btn-success btn-edit-pago" 
                        data-id="${pago.id}" 
                        title="Editar / Marcar como Pagado">
                <i class="fas fa-check"></i>
                </button>
            `;

            if (pago.estado === "Por Vencer" || pago.estado === "Vencido") {
                accionesHTML += `
                <button class="btn btn-sm btn-warning btn-reminder"
                        data-id="${pago.id}"
                        data-telefono="${pago.telefono || ""}"
                        data-estado="${pago.estado}"
                        data-cliente="${pago.cliente}">
                    <i class="fas fa-envelope"></i>
                </button>
                `;
            }

            if (pago.estado === "Pagado") {
                accionesHTML += `
                <button class="btn btn-sm btn-info btn-historial" 
                        data-cliente="${pago.cliente_id}" 
                        title="Ver Historial">
                    <i class="fas fa-history"></i>
                </button>
                `;
            }

            accionesHTML += `
            <button class="btn btn-sm btn-info btn-horarios-pago"
                    title="Ver días y horarios"
                    data-cliente="${pago.cliente_id}">
                <i class="fa fa-clock"></i>
            </button>
            `;

            // Forma de pago (ajuste 06)
            const forma = pago.forma_pago || "";
            const celdaForma = row.querySelector("td:nth-child(7)");
            celdaForma.dataset.search = forma;
            celdaForma.innerHTML = "";
            const badgeForma = document.createElement("span");
            badgeForma.className = "badge yams-forma yams-forma-" + forma.toLowerCase();
            badgeForma.textContent = forma || "—";
            celdaForma.appendChild(badgeForma);

            row.querySelector("td:nth-child(8)").innerHTML = accionesHTML;

            // La tabla está paginada: avisar a DataTables del cambio en la fila
            YamsTabla.refrescar("#tablaPagos", row);
            }

            // Cerrar modal
            bootstrap.Modal.getInstance(document.getElementById("modalEditPago")).hide();
            alert("Pago actualizado ✅");
            if (typeof aplicarFiltrosPagos === "function") aplicarFiltrosPagos();
        })
        .catch((err) => {
            console.error("Error en update pago:", err);
            alert("Error de conexión al actualizar pago ❌");
        });
    });
    }
    // ===========================================================
    // LIMPIAR FORMULARIO AL CERRAR "Editar Pago"
    // ===========================================================
    const modalEditPago = document.getElementById("modalEditPago");
    if (modalEditPago) {
    modalEditPago.addEventListener("hidden.bs.modal", function () {
        const form = modalEditPago.querySelector("form");
        if (form) form.reset();

        // Limpiar manualmente los campos cliente
        const idInput = document.getElementById("editPagoClienteId");
        const nombreInput = document.getElementById("editPagoClienteNombre");
        if (idInput) idInput.value = "";
        if (nombreInput) nombreInput.value = "";
    });
    }


    // Historial de Pagos (botón azul)
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-historial");
        if (!btn) return;

        let clienteId = btn.dataset.cliente;
        if (!clienteId) {
            return alert("No se pudo identificar el cliente para historial.");
        }

        fetch("index.php?route=pago/historial", {
            method: "POST",
            body: new URLSearchParams({ cliente_id: clienteId })
        })
        .then(r => r.json())
        .then(historial => {
            const tbody = document.getElementById("historialPagosBody");
            tbody.innerHTML = "";
            if (historial.length) {
                historial.forEach(p => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${p.id}</td>
                            <td>$${parseFloat(p.monto).toFixed(2)}</td>
                            <td>${p.fecha_pago}</td>
                            <td>${p.proximo_pago}</td>
                            <td>${p.estado}</td>
                        </tr>
                    `;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center">Sin historial</td></tr>`;
            }
            new bootstrap.Modal(document.getElementById("modalHistorialPagos")).show();
        });
    });

    // Recordatorio WhatsApp (botón amarillo)
    document.addEventListener("click", function(e) {
    const btn = e.target.closest(".btn-reminder");
    if (!btn) return;

    let telefono = btn.dataset.telefono?.trim() || "";
    let estado = btn.dataset.estado?.trim() || "";
    let cliente = btn.dataset.cliente?.trim() || "Cliente";

    if (!telefono) {
        return alert("Este cliente no tiene número de teléfono registrado.");
    }

    // Normalizar el teléfono (solo números, agregar +52 si falta)
    telefono = telefono.replace(/\D/g, "");
    if (!telefono.startsWith("52")) telefono = "52" + telefono;

    // ✅ Generar mensaje según el estado (más tolerante a espacios o variaciones)
    let mensaje = "";
    const estadoLimpio = estado.normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim().toLowerCase();

    if (estadoLimpio.includes("por vencer")) {
    mensaje = `Hola ${cliente}, te recordamos que tu pago está *próximo a vencer*. Por favor revisa tu cuenta para evitar retrasos.`;
    } else if (estadoLimpio.includes("vencido")) {
    mensaje = `Hola ${cliente}, tu pago se encuentra *vencido*. Te invitamos a regularizarlo lo antes posible.`;
    } else {
    mensaje = `Hola ${cliente}, este es un mensaje de seguimiento respecto a tu pago.`;
    }


    // ✅ Forzar limpieza de caracteres especiales invisibles
    mensaje = mensaje.replace(/\s+/g, " ").trim();

    // ✅ Abrir correctamente en WhatsApp (usando encodeURIComponent)
    const enlace = `https://wa.me/${telefono}?text=${encodeURIComponent(mensaje)}`;
    console.log("Enlace generado:", enlace);
    window.open(enlace, "_blank");
    });

    // =========================
    // EGRESOS (CRUD con un solo modal)
    // =========================
    window.showAddEgresoModal = function () {
        const form = document.getElementById("formEgreso");
        if (form) form.reset();
        qs("#egresoId").value = "";
        new bootstrap.Modal(document.getElementById("modalEgreso")).show();
    };

    const formEgreso = document.getElementById("formEgreso");
    if (formEgreso) {
        formEgreso.addEventListener("submit", function (e) {
            e.preventDefault();
            const data = new FormData(this);
            const url = data.get("id") ? "index.php?route=egreso/update" : "index.php?route=egreso/store";

            fetch(url, { method: "POST", body: data })
            .then(r => r.json())
            .then(res => {
                if (res.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById("modalEgreso"));
                    if (modal) modal.hide();

                    reloadEgresos();
                    if (res.finanzas) updateFinanzasDom(res.finanzas);

                    alert(data.get("id") ? "Egreso actualizado ✅" : "Egreso registrado ✅");
                } else {
                    alert("Error al guardar egreso ❌");
                }
            })
            .catch(err => {
                console.error("Error en egreso:", err);
                alert("Error en la conexión con el servidor ❌");
            });
        });
    }


    document.addEventListener("click", function(e) {
        const egresoArea = document.getElementById("egresosContent");
        if (!egresoArea) return;

        if (egresoArea.contains(e.target) && e.target.closest(".btn-edit-egreso")) {
            const id = e.target.closest(".btn-edit-egreso").dataset.id;
            fetch("index.php?route=egreso/find", { method: "POST", body: new URLSearchParams({id}) })
            .then(r=>r.json())
            .then(data => {
                qs("#egresoId").value       = data.id;
                qs("#egresoConcepto").value = data.concepto;
                qs("#egresoMonto").value    = data.monto;
                if (qs("#egresoFormaPago")) {
                    qs("#egresoFormaPago").value = data.forma_pago || "Efectivo";
                }                
                qs("#egresoFecha").value    = data.fecha;
                new bootstrap.Modal(document.getElementById("modalEgreso")).show();
            });
        }

        if (egresoArea.contains(e.target) && e.target.closest(".btn-delete-egreso")) {
            const id = e.target.closest(".btn-delete-egreso").dataset.id;
            if (confirm("¿Eliminar egreso #" + id + "?")) {
                fetch("index.php?route=egreso/delete", { method: "POST", body: new URLSearchParams({ id }) })
                .then(r => r.json())
                .then(res => res.ok ? reloadEgresos() : alert("Error al eliminar egreso"));
            }
        }
    });

    function reloadEgresos() {
        fetch("index.php?route=egreso/index")
        .then(r => r.text())
        .then(html => {
            const doc = new DOMParser().parseFromString(html, "text/html");
            const newBody = doc.querySelector("#tablaEgresos tbody");
            if (newBody) YamsTabla.reemplazarFilas("#tablaEgresos", newBody.innerHTML, { buscador: false });
            if (typeof prepararFiltrosEgresos === "function") prepararFiltrosEgresos();
        });
    }

    // Refrescar EGRESOS al entrar a la sección (importante para Operador)
    const egresosSection = document.getElementById("egresosContent");
    if (egresosSection) {
    const obsEgr = new MutationObserver(() => {
        if (egresosSection.style.display !== "none") {
        reloadEgresos();
        }
    });
    obsEgr.observe(egresosSection, { attributes: true, attributeFilter: ["style"] });

    // Si por alguna razón inicia visible, también refrescamos
    if (egresosSection.style.display !== "none") {
        reloadEgresos();
    }
    }

    // =========================
    // FINANZAS
    // =========================
    function updateFinanzasDom(f) {
        if (!f) return;

        // Tarjetas de ingresos normales
        const efEl   = document.getElementById("ingresoEfectivo");
        const tarEl  = document.getElementById("ingresoTarjeta");
        const trEl   = document.getElementById("ingresoTransferencia");
        const totEl  = document.getElementById("ingresoTotal");
        const egrEl  = document.getElementById("egresosTotal");
        const balEl  = document.getElementById("balanceTotal");

        // Tarjetas de INGRESOS EXTRA
        const exEfEl  = document.getElementById("extraEfectivo");
        const exTarEl = document.getElementById("extraTarjeta");
        const exTrEl  = document.getElementById("extraTransferencia");
        const exTotEl = document.getElementById("extraTotal");

        // Helper para animar número
        const setValor = (el, val, extraClass = "") => {
            if (!el) return;
            el.textContent = "$" + parseFloat(val || 0).toFixed(2);
            el.classList.add("animate");
            if (extraClass) el.classList.add(extraClass);
            setTimeout(() => {
                el.classList.remove("animate");
                if (extraClass) el.classList.remove(extraClass);
            }, 600);
        };

        // Ingresos normales (pagos)
        setValor(efEl,  f.efectivo);
        setValor(tarEl, f.tarjeta);
        setValor(trEl,  f.transferencia);
        setValor(totEl, f.ingresos);
        setValor(egrEl, f.egresos);

        // Balance con efecto positivo / negativo
        if (balEl) {
            const balance = parseFloat(f.balance || 0);
            setValor(balEl, balance);
            balEl.classList.remove("pulse-positive", "pulse-negative");
            if (balance > 0) {
                balEl.classList.add("pulse-positive");
            } else if (balance < 0) {
                balEl.classList.add("pulse-negative");
            }
        }

        // Ingresos EXTRA por forma de pago
        setValor(exEfEl,  f.extra_efectivo);
        setValor(exTarEl, f.extra_tarjeta);
        setValor(exTrEl,  f.extra_transferencia);
        setValor(exTotEl, f.extra_total);
    }

    // Llamada para refrescar desde el backend
    function actualizarResumenFinanzas(scope = "month") {
        const url = scope === "day"
            ? "index.php?route=finanzas/resumen&scope=day"
            : "index.php?route=finanzas/resumen";

        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.ok) updateFinanzasDom(data);
            })
            .catch(err => console.error("Error al actualizar finanzas:", err));
    }

    // Exponer en window para el botón "Actualizar Totales" del panel
    window.actualizarResumenFinanzas = actualizarResumenFinanzas;

    // Si existe la sección de finanzas o la de ingresos (operador), carga los totales al entrar
    if (document.getElementById("finanzasContent")) {
        // Administrador -> totales SOLO del día actual
        actualizarResumenFinanzas("day");
    }
    if (document.getElementById("ingresosContent")) {
        // Operador -> totales SOLO del día actual
        actualizarResumenFinanzas("day");
    }

    // Refrescar automáticamente cuando se muestre la sección (evita valores "en cero" o desactualizados)
    const finanzasSection = document.getElementById("finanzasContent");
    if (finanzasSection) {
        const obsFin = new MutationObserver(() => {
            if (finanzasSection.style.display !== "none") {
                actualizarResumenFinanzas("day");
            }
        });
        obsFin.observe(finanzasSection, { attributes: true, attributeFilter: ["style"] });
    }

    const ingresosSection = document.getElementById("ingresosContent");
    if (ingresosSection) {
        const obsIng = new MutationObserver(() => {
            if (ingresosSection.style.display !== "none") {
                actualizarResumenFinanzas("day");
            }
        });
        obsIng.observe(ingresosSection, { attributes: true, attributeFilter: ["style"] });
    }

// ===========================================================
// PAGOS – Ver días y horarios del cliente
// ===========================================================
document.addEventListener("click", function (e) {
    const btn = e.target.closest(".btn-horarios-pago");
    if (!btn) return; // si no es el botón de pagos, ignorar

    const clienteId = btn.getAttribute("data-cliente");
    if (!clienteId) {
        alert("No se pudo identificar el cliente.");
        return;
    }

    fetch("index.php?route=cliente/find", {
        method: "POST",
        body: new URLSearchParams({ id: clienteId })
    })
    .then(r => r.json())
    .then(data => {
        // Nombre del cliente
        const nombreSpan = document.getElementById("horariosPagoClienteNombre");
        if (nombreSpan) {
            nombreSpan.textContent = data.nombre || "";
        }

        // Cuerpo de tabla
        const tbody = document.getElementById("horariosPagoBody");
        if (!tbody) return;

        tbody.innerHTML = "";

        if (Array.isArray(data.horarios) && data.horarios.length) {
            data.horarios.forEach(h => {
                const dia  = h.dia  || "";
                const hora = (h.hora || "").substring(0, 5); // HH:MM

                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td>${dia}</td>
                    <td>${hora}</td>
                    <td></td> <!-- Hora fin (por ahora vacía) -->
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted">
                        Sin horarios asignados
                    </td>
                </tr>
            `;
        }

        const modalEl = document.getElementById("modalHorariosPago");
        if (modalEl) {
            new bootstrap.Modal(modalEl).show();
        }
    })
    .catch(err => {
        console.error("Error al cargar horarios desde Pagos:", err);
        alert("Error al cargar los horarios del cliente ❌");
    });
});


}); // <- este cierre ya existía, respétalo
