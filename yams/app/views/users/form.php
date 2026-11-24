<!-- Modal Usuario -->
<div class="modal fade" id="userModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formUser">
        <div class="modal-header">
          <h5 class="modal-title">Usuario</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="userId">

          <div class="mb-3">
            <label class="form-label">Usuario</label>
            <input type="text" name="username" id="username" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" name="password" id="password" class="form-control">
            <small class="text-muted">⚠️ Si lo dejas vacío al editar, se mantiene la contraseña actual.</small>
          </div>

          <div class="mb-3">
            <label class="form-label">Rol</label>
            <select name="role" id="role" class="form-control" required>
              <option value="Administrador">Administrador</option>
              <option value="Operador">Operador</option>
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>