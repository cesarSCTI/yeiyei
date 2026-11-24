<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <h2 class="m-0"><i class="fas fa-users me-2"></i>Gestión de Clientes</h2>

<div class="d-flex align-items-center gap-2 flex-wrap">

  <div class="d-flex align-items-center gap-2">
    <label for="filtroTipoClaseClientes" class="form-label mb-0 fw-semibold">
      Tipo de clase:
    </label>

    <select id="filtroTipoClaseClientes" class="form-select form-select-sm" style="min-width: 220px;">
      <option value="">Todos</option>
      <?php foreach (($tipos ?? []) as $tipoFiltro): ?>
        <option value="<?= htmlspecialchars($tipoFiltro["nombre"] ?? "") ?>">
          <?= htmlspecialchars($tipoFiltro["nombre"] ?? "") ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <button type="button" class="btn btn-sm btn-primary" onclick="showAddClientModal()">
    <i class="fas fa-plus me-1"></i> Nuevo Cliente
  </button>
</div>

</div>

<!--  Tabla Clientes  -->
<div class="card table-container">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="tablaClientes">
                <thead class="table-primary">
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
                        <?php foreach ($clientes as $c): ?>
                            <tr>
                                <td><?= htmlspecialchars($c["id"]) ?></td>
                                <td><?= htmlspecialchars($c["nombre"]) ?></td>
                                <td><?= htmlspecialchars($c["telefono"]) ?></td>
                                <td><?= htmlspecialchars($c["tipo_clase"]) ?></td>
                                <td><?= htmlspecialchars($c["fecha_registro"]) ?></td>

                                <!-- Estado del pago -->
                                <td>
                                    <?php
                                        $estadoPago = $c["estado_pago"] ?? "Sin registro";

                                        if ($estadoPago === "Pagado") {
                                            $badgePago = "success";
                                        } elseif ($estadoPago === "Por Vencer") {
                                            $badgePago = "warning";
                                        } elseif ($estadoPago === "Vencido") {
                                            $badgePago = "danger";
                                        } else {
                                            $badgePago = "secondary";
                                        }
                                    ?>
                                    <span class="badge bg-<?= $badgePago ?>">
                                        <?= htmlspecialchars($estadoPago) ?>
                                    </span>
                                </td>

                                <!-- Días restantes -->
                                <td>
                                    <?php if (($c["estado_pago"] ?? "") === "Sin registro" || $c["dias_restantes"] === null): ?>
                                        <span class="text-muted">--</span>
                                    <?php else: ?>
                                        <?php
                                            $dias = (int)$c["dias_restantes"];

                                            if ($dias < 0) {
                                                echo '<span class="text-danger fw-bold">' . $dias . '</span>';
                                            } elseif ($dias <= 5) {
                                                echo '<span class="text-warning fw-bold">' . $dias . '</span>';
                                            } else {
                                                echo '<span class="text-success fw-bold">' . $dias . '</span>';
                                            }
                                        ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-edit" data-id="<?= $c["id"] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $c["id"] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info btn-horarios" data-id="<?= $c['id']; ?>"
                                            title="Ver días y horarios">
                                        <i class="far fa-clock"></i>
                                    </button>
                                </td>
                            </tr>
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


<hr class="mt-5 mb-4">
<h4><i class="fas fa-list"></i> Tipos de Clase</h4>
<button class="btn btn-primary mb-3" onclick="showAddTipoClaseModal()">
  <i class="fas fa-plus"></i> Nuevo Tipo de Clase
</button>

<div class="card">
  <div class="card-body">
    <table class="table table-hover">
      <thead class="table-dark">
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
            <button class="btn btn-sm btn-success btn-edit-tipoclase" data-id="<?= $t['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger btn-delete-tipoclase" data-id="<?= $t['id'] ?>"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
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
  const tabla = $('#tablaClientes').DataTable({
    language: { url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json' },
    pageLength: 10,
    ordering: true,
    responsive: true,
    autoWidth: false
  });

  // Filtro por Tipo de Clase
  $('#filtroTipoClaseClientes').on('change', function() {
    const tipoClase = $.fn.dataTable.util.escapeRegex($(this).val());

    // Columna 3 = Tipo de Clase
    tabla.column(3).search(tipoClase ? '^' + tipoClase + '$' : '', true, false).draw();
  });

  // Vincula el buscador externo
  $('#searchClientes').on('keyup', function() {
    tabla.search(this.value).draw();
  });
});
</script>
