<!-- Modal Cliente -->
<div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-user me-2"></i> Cliente
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formAddClient">
        <div class="modal-body">
          <!-- Campo oculto para ID (solo en edición) -->
          <input type="hidden" id="clientId" name="id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-6">
              <label for="telefono" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="tipo_clase" class="form-label">Tipo de Clase</label>
              <!-- <input type="text" class="form-control" id="tipo_clase" name="tipo_clase" required>-->
              <select name="tipo_clase" id="tipo_clase" class="form-control" required>
                <option value="">Seleccione tipo de clase</option>
                  <?php 
                    require_once "app/models/TipoClase.php";
                    $tiposClase = TipoClase::all();
                    foreach($tiposClase as $t): 
                  ?>
                    <option value="<?= htmlspecialchars($t['nombre']) ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="estado" class="form-label">Estado</label>
              <select class="form-select" id="estado" name="estado" required>
                <option value="Activo">Activo</option>
                <option value="Suspendido">Suspendido</option>
              </select>
            </div>
          </div>
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Fecha registro</label>
              <input type="date" class="form-control" id="fecha_registro" name="fecha_registro">
            </div>
          </div>
          <hr>
          <h6 class="mb-3">Días y Horarios de Asistencia</h6>

          <div class="row g-2" id="diasHorarios">

            <!-- Lunes -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Lunes" id="diaLunes">
                <label class="form-check-label" for="diaLunes">Lunes</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Lunes" disabled>
            </div>

            <!-- Martes -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Martes" id="diaMartes">
                <label class="form-check-label" for="diaMartes">Martes</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Martes" disabled>
            </div>

            <!-- Miercoles -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Miércoles" id="diaMiércoles">
                <label class="form-check-label" for="diaMiércoles">Miércoles</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Miércoles" disabled>
            </div>

            <!-- Jueves -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Jueves" id="diaJueves">
                <label class="form-check-label" for="diaJueves">Jueves</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Jueves" disabled>
            </div>

            <!-- Viernes -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Viernes" id="diaViernes">
                <label class="form-check-label" for="diaViernes">Viernes</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Viernes" disabled>
            </div>            

            <!-- Sábado -->
            <div class="col-md-6 d-flex align-items-center">
              <div class="form-check me-2">
                <input class="form-check-input dia-check" type="checkbox" value="Sábado" id="diaSábado">
                <label class="form-check-label" for="diaSábado">Sábado</label>
              </div>
              <input type="time" class="form-control hora-dia" data-dia="Sábado" disabled>
            </div>  

          </div>

    </div>
        <!-- Modal Footer -->
        <input type="hidden" name="horarios" id="horarios">
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i> Guardar
          </button>
        </div>
    </form>
    </div>
  </div>
</div>