<h2 class="yams-page-title"><i class="fas fa-user-cog me-2"></i>Usuarios</h2>

<div class="card yams-panel">
  <div class="yams-panel-header">
    <h5 class="yams-panel-title"><i class="fas fa-users-cog"></i>Usuarios del sistema</h5>
    <button class="btn btn-primary" onclick="showAddUserModal()">
      <i class="fas fa-plus me-1"></i> Nuevo Usuario
    </button>
  </div>
  <div class="card-body">
    <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>ID</th>
          <th>Usuario</th>
          <th>Nombre</th>
          <th>Rol</th>
          <th>Creado</th>
          <th>Acciones</th>
        </tr>
      </thead>
        <tbody>
          <?php $users = $users ?? []; ?>
          <?php if (!empty($users)): ?>
            <?php foreach($users as $u): ?>
              <tr>
                <td><?= $u["id"] ?></td>
                <td><?= htmlspecialchars($u["username"]) ?></td>
                <td><?= htmlspecialchars($u["name"]) ?></td>
                <td><?= htmlspecialchars($u["role"]) ?></td>
                <td><?= $u["created_at"] ?></td>
                <td>
                  <button class="btn btn-sm btn-success btn-edit-user" data-id="<?= $u['id'] ?>"><i class="fas fa-edit"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete-user" data-id="<?= $u['id'] ?>"><i class="fas fa-trash"></i></button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="text-center">No hay usuarios</td>
            </tr>
          <?php endif; ?>
        </tbody>

    </table>
    </div>
  </div>
</div>

<!-- Modal Usuario -->
<div class="modal fade" id="modalUser" tabindex="-1">
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
            <label>Usuario</label>
            <input type="text" name="username" id="userUsername" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" name="password" id="userPassword" class="form-control">
          </div>
          <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="name" id="userName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Rol</label>
            <select name="role" id="userRole" class="form-control">
              <option value="Operador">Operador</option>
              <option value="Administrador">Administrador</option>
            </select>
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
