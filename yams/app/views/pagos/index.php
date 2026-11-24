<div class="container-fluid p-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-credit-card me-2"></i>Control de Pagos</h2>
        <div class="d-flex gap-3 mb-3 align-items-center">
          
          <input 
            type="text" 
            id="buscarPagoInput"
            class="form-control"
            placeholder="Buscar por nombre de cliente..."
            style="max-width: 300px;"
          >

          <select id="filtroEstadoPago" class="form-select" style="max-width: 180px;">
            <option value="">Todos</option>
            <option value="Pagado">Pagado</option>
            <option value="Por Vencer">Por Vencer</option>
            <option value="Vencido">Vencido</option>
          </select>

        </div>
        
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPago">
            <i class="fas fa-plus me-2"></i>Registrar Pago
        </button>
    </div>

    <div class="card table-container">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>Cliente</th>
                            <th>Monto</th>        
                            <th>Fecha Pago</th>
                            <th>Próximo Pago</th>
                            <th>Estado</th>
                            <th>Días Restantes</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pagos)): ?>
                            <?php foreach($pagos as $p): ?>
                            <tr class="<?= $p['proximo_pago'] < date('Y-m-d') ? 'payment-expired' : ($p['proximo_pago'] <= date('Y-m-d', strtotime('+3 days')) ? 'payment-due-soon' : 'payment-current') ?>">
                                <td><?= htmlspecialchars($p['cliente']) ?></td>
                                <td>$<?= number_format($p['monto'],2) ?></td>
                                <td><?= $p['fecha_pago'] ?></td>
                                <td><?= $p['proximo_pago'] ?></td>
                                <td>
                                    <span class="badge 
                                        <?= $p['estado']=='Pagado' ? 'bg-success' : 
                                            ($p['estado']=='Vencido' ? 'bg-danger' : 'bg-warning') ?>">
                                        <?= $p['estado'] ?>
                                    </span>
                                </td>
                                <td class="<?= $p['proximo_pago'] < date('Y-m-d') ? 'text-danger fw-bold' : 'text-success fw-bold' ?>">
                                    <?= (intval((strtotime($p['proximo_pago'])-time())/86400)) ?> días
                                </td>
                                <td>
                                    <!-- Botón verde (editar / marcar pagado) -->
                                    <button class="btn btn-sm btn-success btn-edit-pago" 
                                            data-id="<?= $p['id'] ?>" 
                                            title="Editar / Marcar como Pagado">
                                        <i class="fas fa-check"></i>
                                    </button>

                                    <!-- Botón amarillo (recordatorio) -->
                                    <?php if($p['estado'] == 'Por Vencer' || $p['estado'] == 'Vencido'): ?>
                                        <button class="btn btn-sm btn-warning btn-reminder" 
                                                data-id="<?= $p['id'] ?>" 
                                                data-telefono="<?= htmlspecialchars($p['telefono']) ?>" 
                                                data-estado="<?= htmlspecialchars($p['estado']) ?>" 
                                                data-cliente="<?= htmlspecialchars($p['cliente']) ?>"
                                                title="Enviar Recordatorio">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                    <?php endif; ?>

                                    <!-- Botón azul (historial) -->
                                    <?php if($p['estado'] == 'Pagado'): ?>
                                        <button class="btn btn-sm btn-info btn-historial" 
                                                data-cliente="<?= $p['cliente_id'] ?>" 
                                                title="Ver Historial">
                                            <i class="fas fa-history"></i>
                                        </button>
                                    <?php endif; ?>

                                    <button
                                      type="button"
                                      class="btn btn-info btn-sm btn-horarios-pago"
                                      title="Ver días y horarios"
                                      data-cliente="<?= (int)$p['cliente_id'] ?>"
                                    >
                                      <i class="fa fa-clock"></i>
                                    </button>

                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay pagos registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Modal Add Pago -->
    <!--<div class="modal fade" id="modalAddPago" tabindex="-1">-->
    <div class="modal fade" id="modalAddPago" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">

        <div class="modal-dialog">
            <div class="modal-content">
                <form id="formAddPago" method="POST" action="index.php?route=pago/store">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                          <label>Cliente</label>
                          <div class="input-group">
                            <input type="hidden" name="cliente_id" id="clienteId">
                            <input type="text" id="clienteNombre" class="form-control" placeholder="Selecciona un cliente..." readonly required>
                            <button type="button" id="btnBuscarClienteAdd" class="btn btn-outline-primary">
                              <i class="fas fa-search"></i>
                            </button>
                          </div>
                        </div>
                        <div class="mb-3">
                            <label>Monto</label>
                            <input name="monto" type="number" step="0.01" class="form-control" required>
                        </div>
                      <!-- 🔽 Nuevo campo -->
                      <div class="mb-3">
                        <label>Forma de Pago</label>
                          <!-- Modal Add Pago -->
                          <select name="forma_pago" id="addPagoFormaPago" class="form-control" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Transferencia">Transferencia</option>
                            <option value="Tarjeta">Tarjeta</option>
                          </select>
                      </div>                        
                        <div class="mb-3">
                            <label>Fecha Pago</label>
                            <input type="date" name="fecha_pago" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Próximo Pago</label>
                            <input type="date" name="proximo_pago" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Estado</label>
                            <select name="estado" class="form-control">
                                <option>Por Vencer</option>
                                <option>Vencido</option>
                                <option>Pagado</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Pago -->
<!--<div class="modal fade" id="modalEditPago" tabindex="-1">-->
<div class="modal fade" id="modalEditPago" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditPago" method="POST" action="index.php?route=pago/update">
        <div class="modal-header">
          <h5 class="modal-title">Editar Pago</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editPagoId">

          <div class="mb-3">
            <label>Cliente</label>
            <div class="input-group">
              <input type="hidden" name="cliente_id" id="editPagoClienteId">
              <input type="text" id="editPagoClienteNombre" class="form-control" placeholder="Selecciona un cliente..." readonly required>
              <button type="button" id="btnBuscarClienteEdit" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBuscarCliente">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label>Monto</label>
            <input name="monto" id="editPagoMonto" type="number" step="0.01" class="form-control" required>
          </div>

          <!-- 🔽 Nuevo campo -->
          <div class="mb-3">
            <label>Forma de Pago</label>
              <select name="forma_pago" id="editPagoFormaPago" class="form-control" required>
                <option value="Efectivo">Efectivo</option>
                <option value="Transferencia">Transferencia</option>
                <option value="Tarjeta">Tarjeta</option>
              </select>
          </div>
          
          <div class="mb-3">
            <label>Fecha Pago</label>
            <input type="date" name="fecha_pago" id="editPagoFecha" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Próximo Pago</label>
            <input type="date" name="proximo_pago" id="editPagoProximo" class="form-control" required>
          </div>

          <div class="mb-3">
            <label>Estado</label>
            <select name="estado" id="editPagoEstado" class="form-control">
              <option>Por Vencer</option>
              <option>Vencido</option>
              <option>Pagado</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit">Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Historial de Pagos -->
<div class="modal fade" id="modalHistorialPagos" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Historial de Pagos</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>ID</th>
              <th>Monto</th>
              <th>Fecha Pago</th>
              <th>Próximo Pago</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody id="historialPagosBody">
            <!-- Aquí se cargará dinámicamente -->
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>   

<!-- Modal Buscar Cliente -->
<div class="modal fade" id="modalBuscarCliente" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <!-- Campo de búsqueda -->
        <div class="mb-3">
          <input type="text" id="buscarClienteInput" class="form-control" placeholder="Buscar por nombre o teléfono...">
        </div>

        <table class="table table-striped" id="tablaClientesPago">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Tipo de Clase</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="clientesBody">
            <?php foreach($clientes as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['tipo_clase']) ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary seleccionar-cliente"
                          data-id="<?= $c['id'] ?>"
                          data-nombre="<?= htmlspecialchars($c['nombre']) ?>">
                    <i class="fas fa-check"></i> Seleccionar
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal Horarios -->
<div class="modal fade" id="modalHorariosCliente" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Días y horarios</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="mb-2">
          <strong>Cliente:</strong> <span id="horariosClienteNombre">—</span>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Día</th>
                <th>Hora inicio</th>
                <th>Hora fin</th>
              </tr>
            </thead>
            <tbody id="horariosClienteBody">
              <tr><td colspan="3">Sin horarios</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!-- Modal Ver Horarios Cliente -->
<div class="modal fade" id="modalVerHorariosCliente" tabindex="-1">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title">
          Horarios de <span id="horarioClienteNombre"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Día</th>
              <th>Hora</th>
            </tr>
          </thead>
          <tbody id="horariosClienteBody">
            <tr>
              <td colspan="2" class="text-center text-muted">
                Sin horarios
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Días y horarios desde PAGOS -->
<div class="modal fade" id="modalHorariosPago" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Días y horarios
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <p><strong>Cliente:</strong> <span id="horariosPagoClienteNombre"></span></p>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Día</th>
                <th>Hora inicio</th>
              </tr>
            </thead>
            <tbody id="horariosPagoBody">
              <tr>
                <td colspan="3" class="text-center text-muted">Sin horarios</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>

    </div>
  </div>
</div>


<link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
  $('#tablaClientesPago').DataTable({
    pageLength: 5,
    language: { url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json' }
  });
});
</script>

<style>
.estado-actualizado {
  background-color: #d4edda !important;
  transition: background-color 1s ease;
}
</style>
