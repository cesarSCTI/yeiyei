<?php if (!isset($_SESSION['user'])): ?>
    <script>window.location.href="index.php?route=auth/login";</script>
<?php endif; ?>
<?php $role = $_SESSION['user']['role'] ?? ''; ?>


<div id="dashboardSection">
    <!-- Barra superior -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fas fa-swimmer me-2"></i>
                <span>Yei Yei Academy</span>
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php?route=auth/logout">
                    <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 p-0">
                <div class="sidebar">
                    <div class="nav flex-column">
                        <a class="nav-link active" href="#" onclick="showSection('dashboard', this)">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('clientes', this)">
                            <i class="fas fa-users me-2"></i>Clientes
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('instructores', this)">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Instructores
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('pagos', this)">
                            <i class="fas fa-credit-card me-2"></i>Pagos
                        </a>
                        <?php if ($role === 'Administrador'): ?>
                        <a class="nav-link" href="#" onclick="showSection('finanzas', this)">
                            <i class="fas fa-chart-line me-2"></i>Ingresos
                        </a>
                    <?php elseif ($role === 'Operador'): ?>
                        <a class="nav-link" href="#" onclick="showSection('ingresos', this)">
                            <i class="fas fa-coins me-2"></i>Ingresos
                        </a>
                    <?php endif; ?>
                        <!--
                        <a class="nav-link" href="#" onclick="showSection('clases', this)">
                            <i class="fas fa-calendar-alt me-2"></i> Clases
                        </a>
                        -->
                        <?php if (in_array($_SESSION["user"]["role"], ["Administrador", "Operador"])): ?>
                        
                            <a class="nav-link" href="#" onclick="showSection('egresos', this)">
                                <i class="fas fa-arrow-down me-2"></i> Egresos
                            </a>
                        <?php endif; ?>                        
                        <?php if ($_SESSION["user"]["role"] === "Administrador"): ?>                        
                            <a class="nav-link" href="#" onclick="showSection('usuarios', this)">
                                <i class="fas fa-user-cog me-2"></i> Usuarios
                            </a>                                                                               
                        <?php endif; ?>                        
                    </div>
                </div>  
            </div>  

            <!-- Contenido principal -->
            <div class="col-md-9">
                <div class="content-area">

                    <!-- ========== DASHBOARD ========== -->
                    <div id="dashboardContent">
                        <h2 class="mb-4">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </h2>

                        <!-- Tarjetas resumen -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card stats-card">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-users"></i>
                                        <div class="animate-number text-white" id="totalClientes">0</div>
                                        <p class="mb-0">Estudiantes Activos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stats-card">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        <div class="animate-number text-white" id="totalVencidos">0</div>
                                        <p class="mb-0">Pagos Vencidos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stats-card">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-dollar-sign"></i>
                                        <div class="animate-number text-white" id="totalPorVencer">0</div>
                                        <p class="mb-0">Pagos por Vencer</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stats-card">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-swimming-pool"></i>
                                        <div class="animate-number text-white" id="totalClases">0</div>
                                        <p class="mb-0">Clases</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfica + Notificaciones -->
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-chart-area me-2"></i>Ingresos por Mes</h5>
                                    </div>
                                    <div class="card-body">
                                        <canvas id="ingresosChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5><i class="fas fa-bell me-2"></i>Notificaciones</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- Mensajes -->
                                        <!-- <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            8 pagos vencidos requieren atención
                                        </div>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            15 pagos vencen en 3 días
                                        </div>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            Clase de natación infantil completa
                                        </div>-->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!-- /dashboardContent -->

                    <!-- ========== CLIENTES ========== -->
                    <div id="clientesContent" style="display:none;">
                        <?php $clientes = $clientes ?? []; ?>
                        <?php include "app/views/clientes/index.php"; ?>
                    </div>

                    <!-- ========== INSTRUCTORES ========== -->
                    <div id="instructoresContent" style="display:none;">
                        <?php $instructores = $instructores ?? []; ?>
                        <?php include "app/views/instructores/index.php"; ?>
                    </div>

                    <!-- ========== PAGOS ========== -->
                    <div id="pagosContent" style="display:none;">
                        <?php $pagos = $pagos ?? []; ?>
                        <?php include "app/views/pagos/index.php"; ?>
                    </div>
                    <!-- Egresos -->
                    <div id="egresosContent" style="display:none;">
                        <?php $egresos = $egresos ?? []; ?>
                        <?php include "app/views/egresos/index.php"; ?>
                    </div>  

                    <!-- Usuarios -->
                    <div id="usuariosContent" style="display:none;">
                        <?php $usuarios = $usuarios ?? []; ?>
                        <?php include "app/views/users/index.php"; ?>
                    </div>
                    <!-- Clases -->
                    <div id="clasesContent" style="display:none;">
                        <?php $clases = $clases ?? []; ?>
                        <?php include "app/views/clases/index.php"; ?>
                    </div>

                    <!-- Alumnos de Clase -->
                    <div id="claseAlumnosContent" style="display:none;">
                        <?php $alumnos = $alumnos ?? []; ?>
                        <?php include "app/views/clases/alumnos.php"; ?>
                    </div>     
                    <!-- Finanzas -->
                    <?php if ($_SESSION["user"]["role"] === "Administrador"): ?>
                    <div id="finanzasContent" style="display:none;">
                    <h2 class="mb-4 text-primary">
                        <i class="fas fa-chart-line me-2"></i>Ingresos y Egresos
                    </h2>

                    <!-- ========== FILA 1: INGRESOS NORMALES (PAGOS) ========== -->
                    <div class="row text-center mb-4 finance-cards">
                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-success">
                            <div class="card-body">
                            <h6 class="text-light">Efectivo</h6>
                            <h4 id="ingresoEfectivo">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-primary">
                            <div class="card-body">
                            <h6 class="text-light">Tarjeta</h6>
                            <h4 id="ingresoTarjeta">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-warning">
                            <div class="card-body">
                            <h6 class="text-dark">Transferencia</h6>
                            <h4 id="ingresoTransferencia" class="text-dark">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-info">
                            <div class="card-body">
                            <h6 class="text-light">Total Ingresos</h6>
                            <h4 id="ingresoTotal">$0.00</h4>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- ========== FILA 2: INGRESOS EXTRA POR TIPO DE PAGO ========== -->
                    <h4 class="mt-3 mb-3 text-primary">
                        <i class="fas fa-plus-circle me-2"></i>Ingresos Extra
                    </h4>

                    <div class="row text-center mb-4 finance-cards">
                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-success">
                            <div class="card-body">
                            <h6 class="text-light">Extra Efectivo</h6>
                            <h4 id="extraEfectivo" class="monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-primary">
                            <div class="card-body">
                            <h6 class="text-light">Extra Tarjeta</h6>
                            <h4 id="extraTarjeta" class="monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-warning">
                            <div class="card-body">
                            <h6 class="text-dark">Extra Transferencia</h6>
                            <h4 id="extraTransferencia" class="text-dark monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-info">
                            <div class="card-body">
                            <h6 class="text-light">Total Ingresos Extra</h6>
                            <h4 id="extraTotal" class="monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- ========== FILA 3: EGRESOS + BALANCE (ABAJO DE TODO) ========== -->
                    <div class="row text-center mb-4 finance-cards justify-content-center">
                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-danger">
                            <div class="card-body">
                            <h6 class="text-light">Total Egresos</h6>
                            <h4 id="egresosTotal" class="monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>

                        <div class="col-md-3 mb-3">
                        <div class="card card-finanza bg-gradient-dark">
                            <div class="card-body">
                            <h6 class="text-light">Balance</h6>
                            <h4 id="balanceTotal" class="fw-bold monto-animable">$0.00</h4>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Botón actualizar -->
                    <div class="text-center mt-3 mb-4">
                        <button class="btn btn-lg btn-outline-primary shadow-sm" onclick="actualizarResumenFinanzas()">
                        <i class="fas fa-sync-alt me-2"></i>Actualizar Totales
                        </button>
                    </div>

                    <!-- Resumen diario del mes actual -->
                    <div class="card mt-4 shadow-sm border-0">
                    <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2 "></i>Ingresos por día (mes en curso)
                        </h5>
                        <small class="text-muted" id="labelMesIngresosDiarios"></small>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                        <table class="table table-bordered table-sm text-center align-middle tabla-ingresos-diarios">
                        <thead class="tabla-ingresos-diarios-head text-center">
                            <tr>
                            <th>Semana</th>
                            <th>Lunes</th>
                            <th>Martes</th>
                            <th>Miércoles</th>
                            <th>Jueves</th>
                            <th>Viernes</th>
                            <th>Sábado</th>
                            <th>Domingo</th>
                            </tr>
                        </thead>
                        <tbody id="tablaIngresosDiariosBody">
                            <tr>
                            <td colspan="8" class="text-center text-muted sin-datos">
                                No hay datos para el mes actual.
                            </td>
                            </tr>
                        </tbody>
                        </table>
                        </div>
                        <small class="text-muted d-block mt-2">
                        * Cada celda muestra el total de ingresos (pagos + ingresos extras) por día.
                        </small>
                    </div>
                    </div>

                    <!-- ========= TABLA + MODAL DE INGRESOS EXTRAS ========= -->
                    <div class="card mt-4 shadow-sm border-0">
                        <div class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Ingresos Extras</h5>
                        <button class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#modalIngresoExtra">
                            <i class="fas fa-plus"></i> Nuevo
                        </button>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                            <thead class="table-primary">
                                <tr>
                                <th>Evento</th>
                                <th>Fecha</th>
                                <th>Costo</th>
                                <th>Forma de Pago</th>
                                <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tablaIngresosExtras">
                                <tr>
                                <td colspan="5" class="text-center text-muted">No hay ingresos extras registrados.</td>
                                </tr>
                            </tbody>
                            </table>
                        </div>
                        </div>
                    </div>

                    <!-- Modal: Nuevo Ingreso Extra -->
                    <div class="modal fade" id="modalIngresoExtra" tabindex="-1">
                        <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="formIngresoExtra">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title"><i class="fas fa-plus"></i> Nuevo Ingreso Extra</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                <label for="evento" class="form-label">Evento</label>
                                <input type="text" class="form-control" id="evento" name="evento" placeholder="Ej. Fiesta de Fin de Año" required>
                                </div>
                                <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required>
                                </div>
                                <div class="mb-3">
                                <label for="costo" class="form-label">Costo</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="costo" name="costo" placeholder="Ej. 1200.00" required>
                                </div>
                                <div class="mb-3">
                                <label for="forma_pago_extra" class="form-label">Forma de pago</label>
                                <select class="form-select" id="forma_pago_extra" name="forma_pago" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                            </form>
                        </div>
                        </div>
                    </div>

                    </div>
                    <?php endif; ?>
                    <?php if ($role === 'Operador'): ?>
                        <div id="ingresosContent" style="display:none;">
                            <h2 class="mb-4 text-primary">
                                <i class="fas fa-coins me-2"></i>Ingresos
                            </h2>

                            <!-- TARJETAS RESUMEN (mismas que Finanzas, pero con datos del día) -->
                            <div class="row text-center mb-4 finance-cards">
                                <!-- INGRESOS normales -->
                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-success">
                                        <div class="card-body">
                                            <h6 class="text-light">Efectivo</h6>
                                            <h4 id="ingresoEfectivo">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-primary">
                                        <div class="card-body">
                                            <h6 class="text-light">Tarjeta</h6>
                                            <h4 id="ingresoTarjeta">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-warning">
                                        <div class="card-body">
                                            <h6 class="text-dark">Transferencia</h6>
                                            <h4 id="ingresoTransferencia" class="text-dark">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-info">
                                        <div class="card-body">
                                            <h6 class="text-light">Total Ingresos</h6>
                                            <h4 id="ingresoTotal">$0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- INGRESOS EXTRA -->
                            <h4 class="mt-3 mb-3 text-primary">
                                <i class="fas fa-plus-circle me-2"></i>Ingresos Extra</h4>

                            <div class="row text-center mb-4 finance-cards">
                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-success">
                                        <div class="card-body">
                                            <h6 class="text-light">Extra Efectivo</h6>
                                            <h4 id="extraEfectivo" class="monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-primary">
                                        <div class="card-body">
                                            <h6 class="text-light">Extra Tarjeta</h6>
                                            <h4 id="extraTarjeta" class="monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-warning">
                                        <div class="card-body">
                                            <h6 class="text-dark">Extra Transferencia</h6>
                                            <h4 id="extraTransferencia" class="text-dark monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-info">
                                        <div class="card-body">
                                            <h6 class="text-light">Total Ingresos Extra</h6>
                                            <h4 id="extraTotal" class="monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EGRESOS + BALANCE -->
                            <div class="row text-center mb-4 finance-cards justify-content-center">
                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-danger">
                                        <div class="card-body">
                                            <h6 class="text-light">Total Egresos</h6>
                                            <h4 id="egresosTotal" class="monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <div class="card card-finanza bg-gradient-dark">
                                        <div class="card-body">
                                            <h6 class="text-light">Balance</h6>
                                            <h4 id="balanceTotal" class="monto-animable">$0.00</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón + tabla (lo que ya tenías) -->
                            <div class="d-flex justify-content-end mb-3">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngresoExtra">
                                    <i class="fas fa-plus me-2"></i>Registrar Ingreso Extra
                                </button>
                            </div>

                            <div class="table-responsive">
                                <!-- aquí sigue tu tabla tal cual ya estaba -->

                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th>Fecha</th>
                                        <th>Costo</th>
                                        <th>Forma de pago</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaIngresosExtras">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Cargando...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Modal Ingreso Extra -->
                        <div class="modal fade" id="modalIngresoExtra" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="formIngresoExtra">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Registrar Ingreso Extra</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-2">
                                                <label class="form-label">Evento</label>
                                                <input type="text" class="form-control" name="evento" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Fecha</label>
                                                <input type="date" class="form-control" name="fecha" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Costo</label>
                                                <input type="number" step="0.01" class="form-control" name="costo" required>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Forma de pago</label>
                                                <select class="form-select" name="forma_pago" required>
                                                    <option value="Efectivo">Efectivo</option>
                                                    <option value="Tarjeta">Tarjeta</option>
                                                    <option value="Transferencia">Transferencia</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                            <button type="submit" class="btn btn-primary">Guardar</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                    <?php endif; ?>


                </div><!-- /content-area -->
            </div><!-- /col-md-9 -->
        </div><!-- /row -->
    </div><!-- /container-fluid -->
</div><!-- /dashboardSection -->
<script>
async function actualizarDashboard() {
  try {
    const res = await fetch("index.php?route=dashboard/resumen");
    const data = await res.json();

    if (!data.ok) throw new Error("Respuesta inválida del servidor");

    // Actualizar valores
    document.getElementById("totalClientes").textContent = data.clientes;
    document.getElementById("totalVencidos").textContent = data.vencidos;
    document.getElementById("totalPorVencer").textContent = data.porVencer;
    document.getElementById("totalClases").textContent = data.clases;

  } catch (err) {
    console.error("Error al actualizar dashboard:", err);
  }
}

// Ejecutar al cargar
actualizarDashboard();

// Refrescar automáticamente cada 30 segundos
setInterval(actualizarDashboard, 30000);
</script>

<style>
/* Gradientes llamativos pero elegantes */
.bg-gradient-success {
  background: linear-gradient(135deg, #4CAF50, #81C784);
}
.bg-gradient-primary {
  background: linear-gradient(135deg, #2196F3, #64B5F6);
}
.bg-gradient-warning {
  background: linear-gradient(135deg, #FFEB3B, #FBC02D);
}
.bg-gradient-info {
  background: linear-gradient(135deg, #00BCD4, #4DD0E1);
}
.bg-gradient-danger {
  background: linear-gradient(135deg, #F44336, #E57373);
}
.bg-gradient-dark {
  background: linear-gradient(135deg, #212121, #424242);
}

/* Tarjetas animadas */
.card-finanza {
  border: none;
  border-radius: 12px;
  color: white;
  transition: all 0.3s ease;
  box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
.card-finanza:hover {
  transform: translateY(-5px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.25);
}

/* Animación del número */
.finance-cards h4 {
  transition: transform 0.4s ease, color 0.3s;
}
.finance-cards h4.animate {
  transform: scale(1.2);
}

/* Balance pulsante */
@keyframes pulsePositive {
  0% { color: #4CAF50; text-shadow: 0 0 5px #A5D6A7; }
  50% { color: #81C784; text-shadow: 0 0 20px #C8E6C9; }
  100% { color: #4CAF50; text-shadow: 0 0 5px #A5D6A7; }
}
@keyframes pulseNegative {
  0% { color: #F44336; text-shadow: 0 0 5px #FFCDD2; }
  50% { color: #E57373; text-shadow: 0 0 20px #FFEBEE; }
  100% { color: #F44336; text-shadow: 0 0 5px #FFCDD2; }
}
.pulse-positive {
  animation: pulsePositive 1.5s infinite;
}
.pulse-negative {
  animation: pulseNegative 1.5s infinite;
}

/* ===== TABLA INGRESOS DIARIOS (BOOTSTRAP ESTABLE) ===== */

.tabla-ingresos-diarios {
  font-size: 0.85rem;
  border-radius: 8px;
  overflow: hidden;
}

/* Header */
.tabla-ingresos-diarios thead th {
  background-color: #0d6efd; /* Bootstrap primary */
  color: #fff;
  font-weight: 600;
  border-color: #0b5ed7;
  vertical-align: middle;
}

/* Columna Semana */
.tabla-ingresos-diarios tbody td.col-semana {
  background-color: #f1f3f5;
  font-weight: 700;
}

/* Filas alternadas */
.tabla-ingresos-diarios tbody tr:nth-child(odd) {
  background-color: #f8f9fa;
}

/* Hover */
.tabla-ingresos-diarios tbody tr:hover {
  background-color: #e7f1ff;
}

/* Montos */
.tabla-ingresos-diarios tbody td[data-monto] {
  font-weight: 600;
  color: #0d6efd;
}

/* Resalta la columna del día actual */
.tabla-ingresos-diarios .col-hoy {
  background-color: #fff3cd; /* warning suave */
}

/* Resalta encabezado del día actual */
.tabla-ingresos-diarios thead th.col-hoy {
  background-color: #0b5ed7; /* azul más fuerte */
  color: #fff;
}

/* Celdas con 0 */
.tabla-ingresos-diarios td.monto-cero {
  color: #6c757d !important;  /* gris Bootstrap */
  font-weight: 500;
  opacity: 0.85;
}

/* Celdas con monto */
.tabla-ingresos-diarios td.monto-ok {
  color: #0d6efd !important;
  font-weight: 700;
}

.monto-cero {
  color: #9E9E9E;
  font-style: italic;
}

.monto-ok {
  color: #0D47A1;
  font-weight: 700;
}

</style>