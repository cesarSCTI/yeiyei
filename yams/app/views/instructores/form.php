<!-- Modal Instructor -->
<div class="modal fade" id="instructorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
          <i class="fas fa-chalkboard-teacher me-2"></i> Instructor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formAddInstructor">
        <div class="modal-body">
          <!-- Campo oculto para ID (solo en edición) -->
          <input type="hidden" id="instructorId" name="id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nombreInstructor" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombreInstructor" name="nombre" required>
            </div>
            <div class="col-md-6">
              <label for="telefonoInstructor" class="form-label">Teléfono</label>
              <input type="text" class="form-control" id="telefonoInstructor" name="telefono" required>
            </div>
          </div>

          <div class="row mb-3">  
            <div class="col-md-6">
              <label for="especialidadInstructor">Especialidad</label>
              <select class="form-control" id="especialidadInstructor" name="especialidad" required>
                  <option value="">-- Selecciona una especialidad --</option>
                  <?php foreach ($especialidades as $esp): ?>
                      <option value="<?= htmlspecialchars($esp['nombre']) ?>">
                          <?= htmlspecialchars($esp['nombre']) ?>
                      </option>
                  <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="estadoInstructor" class="form-label">Estado</label>
              <select class="form-select" id="estadoInstructor" name="estado" required>
                <option value="Activo">Activo</option>
                <option value="Suspendido">Suspendido</option>
              </select>
            </div>
          </div>
        </div>

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