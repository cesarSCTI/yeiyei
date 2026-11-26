<div class="container-fluid p-3">
    <h2 class="yams-page-title"><i class="fas fa-arrow-down me-2"></i>Control de Egresos</h2>

    <div class="card yams-panel">
        <div class="yams-panel-header">
            <h5 class="yams-panel-title"><i class="fas fa-file-invoice-dollar"></i>Egresos</h5>
            <button class="btn btn-primary" onclick="showAddEgresoModal()">
                <i class="fas fa-plus me-2"></i>Registrar Egreso
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tablaEgresos">
                    <thead>
                        <tr>
                            <th>Concepto</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($egresos)): ?>
                            <?php foreach($egresos as $e): ?>
                            <tr>
                                <td><?= htmlspecialchars($e['concepto']) ?></td>
                                <td>$<?= number_format($e['monto'],2) ?></td>
                                <td><?= $e['fecha'] ?></td>
                                <td>
                                  <button class="btn btn-sm btn-success btn-edit-egreso" 
                                          data-id="<?= $e['id'] ?>">
                                      <i class="fas fa-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-danger btn-delete-egreso" 
                                          data-id="<?= $e['id'] ?>">
                                      <i class="fas fa-trash"></i>
                                  </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">No hay egresos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal único para Crear/Editar Egreso -->
<div class="modal fade" id="modalEgreso" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEgreso">
        <div class="modal-header">
          <h5 class="modal-title">Egreso</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="egresoId">

        <div class="mb-3">
          <label>Concepto</label>
          <input type="text" name="concepto" id="egresoConcepto" class="form-control" required>
        </div>

        <div class="mb-3">
          <label>Monto</label>
          <input type="number" step="0.01" name="monto" id="egresoMonto" class="form-control" required>
        </div>

        <input type="hidden" name="forma_pago" value="Efectivo">

        <div class="mb-3">
          <label>Fecha</label>
          <input type="date" name="fecha" id="egresoFecha" class="form-control" required>
        </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancelar
          </button>

          <button type="submit" class="btn btn-primary">
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
// Tabla de egresos: paginada, con scroll interno y encabezado fijo
$(function () {
  YamsTabla.init('#tablaEgresos', { buscador: false });
});
</script>
