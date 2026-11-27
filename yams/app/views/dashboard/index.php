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
                <a class="nav-link" href="index.php?route=auth/logout" title="Cerrar Sesión" aria-label="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt me-1"></i><span class="d-none d-sm-inline"> Cerrar Sesión</span>
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
                        <h2 class="yams-page-title">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </h2>

                        <!-- Tarjetas resumen: cada recuadro lleva al módulo relacionado -->
                        <?php $seccionIngresos = $role === 'Administrador' ? 'finanzas' : 'ingresos'; ?>
                        <div class="row mb-4">
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card stats-card dashboard-link" role="button" tabindex="0"
                                     data-section="clientes" data-estado-pago=""
                                     title="Ver clientes">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-users"></i>
                                        <div class="animate-number text-white" id="totalClientes">0</div>
                                        <p class="mb-0">Estudiantes Activos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card stats-card dashboard-link" role="button" tabindex="0"
                                     data-section="clientes" data-estado-pago="Vencido"
                                     title="Ver clientes con pago vencido">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-exclamation-triangle text-warning"></i>
                                        <div class="animate-number text-white" id="totalVencidos">0</div>
                                        <p class="mb-0">Pagos Vencidos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card stats-card dashboard-link" role="button" tabindex="0"
                                     data-section="clientes" data-estado-pago="Por Vencer"
                                     title="Ver clientes con pago por vencer">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-clock"></i>
                                        <div class="animate-number text-white" id="totalPorVencer">0</div>
                                        <p class="mb-0">Pagos por Vencer</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 col-md-3 mb-3">
                                <div class="card stats-card dashboard-link" role="button" tabindex="0"
                                     data-section="<?= $seccionIngresos ?>"
                                     title="Ver ingresos">
                                    <div class="dashboard-stat">
                                        <i class="fas fa-dollar-sign"></i>
                                        <div class="animate-number text-white" id="totalIngresosMes">$0</div>
                                        <p class="mb-0">Ingresos del Mes</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php
                        // ===== Por cobrar: clientes con último pago vencido o por vencer =====
                        // Misma lógica de estado que la tabla de Clientes (Cliente::all()).
                        $porCobrar = array_values(array_filter($clientes ?? [], function ($c) {
                            return in_array($c['estado_pago'] ?? '', ['Por Vencer', 'Vencido'], true);
                        }));
                        // Primero lo más urgente: por vencer (0, 1, 2… días) y luego vencidos recientes (-1, -2…)
                        usort($porCobrar, function ($a, $b) {
                            $da = (int)$a['dias_restantes'];
                            $db = (int)$b['dias_restantes'];
                            $grupoA = $da >= 0 ? 0 : 1;
                            $grupoB = $db >= 0 ? 0 : 1;
                            if ($grupoA !== $grupoB) return $grupoA <=> $grupoB;
                            return $grupoA === 0 ? $da <=> $db : $db <=> $da;
                        });
                        $totalPorVencer = count(array_filter($porCobrar, fn($c) => $c['estado_pago'] === 'Por Vencer'));
                        $totalVencidos  = count($porCobrar) - $totalPorVencer;
                        ?>

                        <div class="row">
                            <!-- ===== Hoy ===== -->
                            <div class="col-lg-4">
                                <div class="card yams-panel">
                                    <div class="yams-panel-header">
                                        <h5 class="yams-panel-title"><i class="fas fa-cash-register"></i>Hoy</h5>
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-section="<?= $seccionIngresos ?>" data-dashboard-ir>
                                            Ver ingresos <i class="fas fa-arrow-right ms-1"></i>
                                        </button>
                                    </div>
                                    <div class="card-body">
                                        <div class="hoy-total">
                                            <span class="hoy-label">Cobrado hoy</span>
                                            <span class="hoy-monto" id="hoyCobrado">$0</span>
                                        </div>
                                        <ul class="hoy-lista">
                                            <li><span><i class="fas fa-money-bill-wave text-success"></i>Efectivo</span><strong id="hoyEfectivo">$0</strong></li>
                                            <li><span><i class="fas fa-credit-card text-primary"></i>Tarjeta</span><strong id="hoyTarjeta">$0</strong></li>
                                            <li><span><i class="fas fa-exchange-alt text-warning"></i>Transferencia</span><strong id="hoyTransferencia">$0</strong></li>
                                            <li class="hoy-sep"><span><i class="fas fa-arrow-down text-danger"></i>Egresos</span><strong id="hoyEgresos" class="text-danger">$0</strong></li>
                                            <li><span><i class="fas fa-scale-balanced"></i>Balance</span><strong id="hoyBalance">$0</strong></li>
                                        </ul>
                                        <small class="text-muted">Incluye pagos e ingresos extra del día.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- ===== Ingresos de los últimos 12 meses ===== -->
                            <div class="col-lg-8">
                                <div class="card yams-panel">
                                    <div class="yams-panel-header">
                                        <h5 class="yams-panel-title"><i class="fas fa-chart-column"></i>Ingresos de los últimos 12 meses</h5>
                                        <small class="text-muted" id="ingresosAnualTotal"></small>
                                    </div>
                                    <div class="card-body">
                                        <div class="grafica-ingresos">
                                            <canvas id="ingresosChart" aria-label="Ingresos por mes de los últimos 12 meses" role="img"></canvas>
                                        </div>
                                        <small class="text-muted">Clic en una barra para ver Ingresos.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ===== Por cobrar ===== -->
                        <div class="card yams-panel">
                            <div class="yams-panel-header">
                                <h5 class="yams-panel-title"><i class="fas fa-hand-holding-dollar"></i>Por cobrar</h5>
                                <div class="btn-group" role="group" aria-label="Filtrar por estado" id="filtroPorCobrar">
                                    <button type="button" class="btn btn-outline-warning" data-estado="Por Vencer">Por vencer <span class="badge bg-warning text-dark ms-1"><?= $totalPorVencer ?></span></button>
                                    <button type="button" class="btn btn-outline-danger" data-estado="Vencido">Vencidos <span class="badge bg-danger ms-1"><?= $totalVencidos ?></span></button>
                                    <button type="button" class="btn btn-outline-secondary" data-estado="">Todos</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="tablaPorCobrar">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Teléfono</th>
                                                <th>Tipo de clase</th>
                                                <th>Próximo pago</th>
                                                <th>Días</th>
                                                <th>Estado</th>
                                                <th class="text-center">Recordatorio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($porCobrar as $c): // filas compactas (ajuste 21)
                                                $dias = (int)$c['dias_restantes'];
                                                $estado = htmlspecialchars($c['estado_pago']);
                                                $nombre = htmlspecialchars($c['nombre'] ?? '');
                                                $telefono = htmlspecialchars($c['telefono'] ?? '');
                                            ?>
<tr><td class="fw-semibold"><?= $nombre ?></td><td><?= $telefono ?></td><td><?= htmlspecialchars($c['tipo_clase'] ?? '') ?></td><td><?= htmlspecialchars(substr((string)($c['proximo_pago'] ?? ''), 0, 10)) ?></td><td data-order="<?= $dias ?>" class="fw-bold <?= $dias < 0 ? 'text-danger' : 'text-warning' ?>"><?= $dias === 0 ? 'Hoy' : ($dias < 0 ? 'Hace ' . abs($dias) . ' d' : 'En ' . $dias . ' d') ?></td><td data-search="<?= $estado ?>"><span class="badge bg-<?= $c['estado_pago'] === 'Vencido' ? 'danger' : 'warning text-dark' ?>"><?= $estado ?></span></td><td class="text-center"><?php if ($telefono !== ''): ?><button type="button" class="btn btn-sm btn-success btn-reminder" data-telefono="<?= $telefono ?>" data-estado="<?= $estado ?>" data-cliente="<?= $nombre ?>" title="Enviar recordatorio por WhatsApp"><i class="fab fa-whatsapp"></i></button><?php else: ?><span class="text-muted small">Sin teléfono</span><?php endif; ?></td></tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
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
                    <h2 class="yams-page-title">
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
                    <h4 class="yams-section-label">
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
                    <div class="card yams-panel mt-4">
                    <div class="yams-panel-header">
                        <h5 class="yams-panel-title">
                        <i class="fas fa-calendar-day"></i>Ingresos por día (mes en curso)
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
                    <div class="card yams-panel">
                        <div class="yams-panel-header">
                        <h5 class="yams-panel-title"><i class="fas fa-plus-circle"></i>Ingresos Extras</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngresoExtra">
                            <i class="fas fa-plus me-1"></i> Registrar Ingreso Extra
                        </button>
                        </div>
                        <div class="card-body">
                        <?php include __DIR__ . '/_filtros_extras.php'; ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle" id="tablaExtras">
                            <thead>
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
                                <h5 class="modal-title" id="tituloModalExtra"><i class="fas fa-plus"></i> Nuevo Ingreso Extra</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Con id = edición (ajuste 19); vacío = alta -->
                                <input type="hidden" name="id" id="extraId">
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
                            <h2 class="yams-page-title">
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
                            <h4 class="yams-section-label">
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

                            <!-- Panel de ingresos extra -->
                            <div class="card yams-panel">
                            <div class="yams-panel-header">
                                <h5 class="yams-panel-title"><i class="fas fa-plus-circle"></i>Ingresos Extras</h5>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalIngresoExtra">
                                    <i class="fas fa-plus me-1"></i> Registrar Ingreso Extra
                                </button>
                            </div>
                            <div class="card-body">
                            <?php include __DIR__ . '/_filtros_extras.php'; ?>
                            <div class="table-responsive">

                            <table class="table table-striped align-middle" id="tablaExtras">
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
                        </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
const formatoMonedaDashboard = new Intl.NumberFormat("es-MX", {
  style: "currency", currency: "MXN", maximumFractionDigits: 0
});

async function actualizarDashboard() {
  try {
    const res = await fetch("index.php?route=dashboard/resumen");
    const data = await res.json();

    if (!data.ok) throw new Error("Respuesta inválida del servidor");

    // Actualizar valores
    document.getElementById("totalClientes").textContent = data.clientes;
    document.getElementById("totalVencidos").textContent = data.vencidos;
    document.getElementById("totalPorVencer").textContent = data.porVencer;
    document.getElementById("totalIngresosMes").textContent = formatoMonedaDashboard.format(data.ingresosMes);

  } catch (err) {
    console.error("Error al actualizar dashboard:", err);
  }
}

// Abre el módulo del recuadro usando el mismo enlace del menú lateral,
// para que se ejecute la misma lógica que al navegar manualmente.
function irDesdeDashboard(card) {
  const section = card.dataset.section;
  const navLink = document.querySelector(`.sidebar .nav-link[onclick*="'${section}'"]`);
  showSection(section, navLink);
  window.scrollTo({ top: 0, behavior: "smooth" });

  if (section === "clientes") aplicarFiltroClientesDesdeDashboard(card.dataset.estadoPago || "");
}

// Deja la tabla de Clientes mostrando solo el estado de pago elegido
// (limpia los demás filtros para que el total coincida con el recuadro).
function aplicarFiltroClientesDesdeDashboard(estadoPago) {
  if (!window.jQuery || !$.fn.DataTable || !$.fn.DataTable.isDataTable("#tablaClientes")) return;

  const tabla = $("#tablaClientes").DataTable();
  $("#filtroTipoClaseClientes").val("");
  $("#searchClientes").val("");
  tabla.search("").column(3).search("");

  // El handler de 'change' del filtro aplica la búsqueda y redibuja
  $("#filtroEstadoPagoClientes").val(estadoPago).trigger("change");
  tabla.columns.adjust();
}

document.querySelectorAll("#dashboardContent .dashboard-link").forEach(card => {
  card.addEventListener("click", () => irDesdeDashboard(card));
  card.addEventListener("keydown", e => {
    if (e.key === "Enter" || e.key === " ") {
      e.preventDefault();
      irDesdeDashboard(card);
    }
  });
});

// Botones "Ver ingresos" y similares dentro de los paneles
document.querySelectorAll("#dashboardContent [data-dashboard-ir]").forEach(btn => {
  btn.addEventListener("click", () => irDesdeDashboard(btn));
});

// ===== Panel "Hoy": mismos totales que la pantalla de Ingresos (día actual) =====
async function actualizarHoyDashboard() {
  try {
    const res = await fetch("index.php?route=finanzas/resumen&scope=day");
    const f = await res.json();
    if (!f.ok) throw new Error("Respuesta inválida del servidor");

    const fmt = v => formatoMonedaDashboard.format(v || 0);
    document.getElementById("hoyCobrado").textContent       = fmt(f.ingresos);
    document.getElementById("hoyEfectivo").textContent      = fmt(f.efectivo + f.extra_efectivo);
    document.getElementById("hoyTarjeta").textContent       = fmt(f.tarjeta + f.extra_tarjeta);
    document.getElementById("hoyTransferencia").textContent = fmt(f.transferencia + f.extra_transferencia);
    document.getElementById("hoyEgresos").textContent       = fmt(f.egresos);

    const balance = document.getElementById("hoyBalance");
    balance.textContent = fmt(f.balance);
    balance.className = f.balance < 0 ? "text-danger" : "text-success";
  } catch (err) {
    console.error("Error al actualizar el panel Hoy:", err);
  }
}

// ===== Gráfica: ingresos de los últimos 12 meses =====
let graficaIngresos = null;

async function cargarGraficaIngresos() {
  const canvas = document.getElementById("ingresosChart");
  if (!canvas || typeof Chart === "undefined") return;

  try {
    const res = await fetch("index.php?route=dashboard/ingresosMensuales");
    const data = await res.json();
    if (!data.ok) throw new Error("Respuesta inválida del servidor");

    const etiquetas = data.meses.map(m => {
      const [anio, mes] = m.mes.split("-").map(Number);
      const txt = new Date(anio, mes - 1, 1).toLocaleDateString("es-MX", { month: "short", year: "2-digit" });
      return txt.charAt(0).toUpperCase() + txt.slice(1);
    });
    const pagos  = data.meses.map(m => m.pagos);
    const extras = data.meses.map(m => m.extras);
    const total12 = data.meses.reduce((t, m) => t + m.pagos + m.extras, 0);
    document.getElementById("ingresosAnualTotal").textContent = "Total: " + formatoMonedaDashboard.format(total12);

    if (graficaIngresos) {
      graficaIngresos.data.labels = etiquetas;
      graficaIngresos.data.datasets[0].data = pagos;
      graficaIngresos.data.datasets[1].data = extras;
      graficaIngresos.update();
      return;
    }

    graficaIngresos = new Chart(canvas, {
      type: "bar",
      data: {
        labels: etiquetas,
        datasets: [
          { label: "Pagos", data: pagos, backgroundColor: "#0066cc", borderRadius: 4, maxBarThickness: 42 },
          { label: "Ingresos extra", data: extras, backgroundColor: "#4dd0e1", borderRadius: 4, maxBarThickness: 42 }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: "index", intersect: false },
        scales: {
          x: { stacked: true, grid: { display: false } },
          y: {
            stacked: true,
            beginAtZero: true,
            ticks: { callback: v => formatoMonedaDashboard.format(v) }
          }
        },
        plugins: {
          legend: { position: "bottom" },
          tooltip: {
            callbacks: {
              label: ctx => `${ctx.dataset.label}: ${formatoMonedaDashboard.format(ctx.parsed.y)}`,
              footer: items => "Total: " + formatoMonedaDashboard.format(items.reduce((t, i) => t + i.parsed.y, 0))
            }
          }
        },
        onHover: (evt, elementos) => {
          evt.native.target.style.cursor = elementos.length ? "pointer" : "default";
        },
        onClick: (evt, elementos) => {
          if (!elementos.length) return;
          irDesdeDashboard({ dataset: { section: "<?= $seccionIngresos ?>" } });
        }
      }
    });
  } catch (err) {
    console.error("Error al cargar la gráfica de ingresos:", err);
  }
}

// ===== Panel "Por cobrar": tabla paginada con filtro por estado =====
function filtrarPorCobrar(estado) {
  const tabla = document.getElementById("tablaPorCobrar");
  if (!tabla || !window.jQuery || !$.fn.DataTable.isDataTable(tabla)) return;

  document.querySelectorAll("#filtroPorCobrar .btn").forEach(b => {
    b.classList.toggle("active", b.dataset.estado === estado);
  });
  const regex = estado ? "^" + $.fn.dataTable.util.escapeRegex(estado) + "$" : "";
  $(tabla).DataTable().column(5).search(regex, true, false).draw();
}

document.querySelectorAll("#filtroPorCobrar .btn").forEach(btn => {
  btn.addEventListener("click", () => filtrarPorCobrar(btn.dataset.estado));
});

// YamsTabla se carga en el footer: inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", () => {
  YamsTabla.init("#tablaPorCobrar", { pageLength: 10, lengthMenu: [10, 25, 50] });
  // Empieza por lo más urgente: "Por vencer" si hay, si no "Vencidos"
  filtrarPorCobrar(<?= $totalPorVencer > 0 ? '"Por Vencer"' : '"Vencido"' ?>);
  cargarGraficaIngresos();
});

// Ejecutar al cargar
actualizarDashboard();
actualizarHoyDashboard();

// Refrescar automáticamente cada 30 segundos (la gráfica cada 5 minutos)
setInterval(actualizarDashboard, 30000);
setInterval(actualizarHoyDashboard, 30000);
setInterval(cargarGraficaIngresos, 300000);
</script>

<style>
/* Panel "Hoy" */
.hoy-total {
  display: flex;
  flex-direction: column;
  padding-bottom: 0.75rem;
  margin-bottom: 0.5rem;
  border-bottom: 1px solid #e3e8ef;
}
.hoy-label {
  font-size: 0.85rem;
  color: #5b6677;
  font-weight: 600;
}
.hoy-monto {
  font-size: 2rem;
  font-weight: 700;
  color: #0b3a75;
  line-height: 1.2;
}
.hoy-lista {
  list-style: none;
  padding: 0;
  margin: 0 0 0.5rem;
}
.hoy-lista li {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.4rem 0;
}
.hoy-lista li span i {
  width: 1.4rem;
}
.hoy-lista li.hoy-sep {
  border-top: 1px dashed #e3e8ef;
  margin-top: 0.25rem;
  padding-top: 0.6rem;
}

/* Gráfica de ingresos */
.grafica-ingresos {
  position: relative;
  height: 280px;
}

/* Recuadros del dashboard que llevan a otro módulo */
.dashboard-link {
  cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.dashboard-link:hover,
.dashboard-link:focus-visible {
  transform: translateY(-4px);
  box-shadow: 0 6px 18px rgba(0,0,0,0.25);
}
.dashboard-link:focus-visible {
  outline: 3px solid #fff;
  outline-offset: 2px;
}
.dashboard-link {
  height: 100%;
}
.dashboard-link .dashboard-stat {
  padding: 1.5rem 0.5rem;
}
/* Evita que cifras y montos se partan en varias líneas o se salgan del recuadro */
.dashboard-link .animate-number {
  font-size: clamp(1.15rem, 2vw, 2.5rem);
  white-space: nowrap;
}

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