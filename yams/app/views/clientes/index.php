<?php $puedeModificar = yams_puede_modificar(); // Operador: solo consulta y registra (ajustes 14 y 15) ?>
<h2 class="yams-page-title"><i class="fas fa-users me-2"></i>Gestión de Clientes</h2>

<!--  Panel Clientes  -->
<div class="card yams-panel">
<div class="yams-panel-header">
  <h5 class="yams-panel-title"><i class="fas fa-address-book"></i>Clientes</h5>

<div class="yams-toolbar-actions">

  <div class="d-flex align-items-center gap-2">
    <label for="filtroTipoClaseClientes" class="form-label mb-0 fw-semibold">
      Tipo de clase:
    </label>

    <select id="filtroTipoClaseClientes" class="form-select">
      <option value="">Todos</option>
      <?php foreach (($tipos ?? []) as $tipoFiltro): ?>
        <option value="<?= htmlspecialchars($tipoFiltro["nombre"] ?? "") ?>">
          <?= htmlspecialchars($tipoFiltro["nombre"] ?? "") ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="d-flex align-items-center gap-2">
    <label for="filtroEstadoPagoClientes" class="form-label mb-0 fw-semibold">
      Estado de pago:
    </label>

    <select id="filtroEstadoPagoClientes" class="form-select">
      <option value="">Todos</option>
      <option value="Pagado">Pagado</option>
      <option value="Por Vencer">Por Vencer</option>
      <option value="Vencido">Vencido</option>
      <option value="Sin registro">Sin registro</option>
    </select>
  </div>

  <button type="button" class="btn btn-primary" onclick="showAddClientModal()">
    <i class="fas fa-plus me-1"></i> Nuevo Cliente
  </button>
</div>

</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaClientes">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Tipo de Clase</th>
                        <th>Fecha Registro</th>
                        <th>Estado Pago</th>
                        <th>Días Restantes</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clientes) && is_array($clientes)): ?>
                        <?php
                        // Filas compactas: con miles de clientes cada byte de sangría se repite
                        // miles de veces en la página (ajuste 21).
                        $badgesPago = ['Pagado' => 'success', 'Por Vencer' => 'warning', 'Vencido' => 'danger'];
                        foreach ($clientes as $c):
                            $id         = (int)$c["id"];
                            $nombre     = htmlspecialchars($c["nombre"] ?? '');
                            $telefono   = htmlspecialchars($c["telefono"] ?? '');
                            $estadoPago = $c["estado_pago"] ?? "Sin registro";
                            $estadoHtml = htmlspecialchars($estadoPago);
                            if ($estadoPago === "Sin registro" || $c["dias_restantes"] === null) {
                                $diasHtml = '<span class="text-muted">--</span>';
                            } else {
                                $dias = (int)$c["dias_restantes"];
                                $colorDias = $dias < 0 ? 'text-danger' : ($dias <= 5 ? 'text-warning' : 'text-success');
                                $diasHtml = '<span class="' . $colorDias . ' fw-bold">' . $dias . '</span>';
                            }
                        ?>
<tr><td><?= $id ?></td><td><?= $nombre ?></td><td><?= $telefono ?></td><td><?= htmlspecialchars($c["tipo_clase"] ?? '') ?></td><td><?= htmlspecialchars($c["fecha_registro"] ?? '') ?></td><td data-search="<?= $estadoHtml ?>"><span class="badge bg-<?= $badgesPago[$estadoPago] ?? 'secondary' ?>"><?= $estadoHtml ?></span></td><td><?= $diasHtml ?></td><td><?php if ($puedeModificar): ?><button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="<?= $id ?>"><i class="fas fa-edit"></i></button> <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $id ?>"><i class="fas fa-trash"></i></button> <?php endif; ?><button type="button" class="btn btn-sm btn-outline-info btn-horarios" data-id="<?= $id ?>" title="Ver días y horarios"><i class="far fa-clock"></i></button><?php if ($telefono !== ''): ?> <button type="button" class="btn btn-sm btn-outline-success btn-reminder" data-telefono="<?= $telefono ?>" data-estado="<?= $estadoHtml ?>" data-cliente="<?= $nombre ?>" title="Enviar aviso por WhatsApp"><i class="fab fa-whatsapp"></i></button><?php endif; ?></td></tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No hay clientes registrados.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Ver días y horarios del cliente -->
<div class="modal fade" id="modalVerHorariosCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          Días y horarios de:
          <span id="horarioClienteNombre" class="fw-bold"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-sm table-striped mb-0">
            <thead>
              <tr>
                <th>Día</th>
                <th>Hora</th>
              </tr>
            </thead>
            <tbody id="horariosClienteBody">
              <tr>
                <td colspan="2" class="text-center text-muted">
                  Sin horarios asignados
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Cerrar
        </button>
      </div>
    </div>
  </div>
</div>


<!--  Panel Tipos de Clase  -->
<div class="card yams-panel">
  <div class="yams-panel-header">
    <h5 class="yams-panel-title"><i class="fas fa-list"></i>Tipos de Clase</h5>
    <button class="btn btn-primary" onclick="showAddTipoClaseModal()">
      <i class="fas fa-plus me-1"></i> Nuevo Tipo de Clase
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($tipos ?? [] as $t): ?>
        <tr>
          <td><?= $t["id"] ?></td>
          <td><?= $t["nombre"] ?></td>
          <td><?= $t["descripcion"] ?></td>
          <td>
            <?php if ($puedeModificar): ?>
            <button class="btn btn-sm btn-success btn-edit-tipoclase" data-id="<?= $t['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger btn-delete-tipoclase" data-id="<?= $t['id'] ?>"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>

<!-- Modal Tipo de Clase -->
<div class="modal fade" id="modalTipoClase" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTipoClase">
        <div class="modal-header">
          <h5 class="modal-title">Tipo de Clase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="tipoClaseId">
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" id="tipoClaseNombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" id="tipoClaseDescripcion" class="form-control"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>


<?php include "app/views/clientes/form.php"; ?>

<!-- jQuery (requerido antes que DataTables) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS (opcional, si lo usas) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>


<script>
$(document).ready(function() {
  // Paginación + scroll interno con encabezado fijo (ver YamsTabla en app.core.js)
  const tabla = YamsTabla.init('#tablaClientes');

  // Filtro por Tipo de Clase
  $('#filtroTipoClaseClientes').on('change', function() {
    const tipoClase = $.fn.dataTable.util.escapeRegex($(this).val());

    // Columna 3 = Tipo de Clase
    tabla.column(3).search(tipoClase ? '^' + tipoClase + '$' : '', true, false).draw();
  });

  // Filtro por Estado de Pago (también lo usa el Dashboard al redirigir)
  // Columna 5 = Estado Pago; se filtra sobre el atributo data-search de la celda
  $('#filtroEstadoPagoClientes').on('change', function() {
    const estado = $.fn.dataTable.util.escapeRegex($(this).val());
    tabla.column(5).search(estado ? '^' + estado + '$' : '', true, false).draw();
  });

  // Vincula el buscador externo
  $('#searchClientes').on('keyup', function() {
    tabla.search(this.value).draw();
  });
});
</script>
