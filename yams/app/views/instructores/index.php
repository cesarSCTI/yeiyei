<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-chalkboard-teacher me-2"></i>Gestión de Instructores</h2>
    <button class="btn btn-primary" onclick="showAddInstructorModal()">
        <i class="fas fa-plus me-2"></i>Nuevo Instructor
    </button>
</div>

<div class="card table-container">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Especialidad</th>
                        <th>Fecha Registro</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                    <tbody>
                        <?php if (!empty($instructores)): ?>
                            <?php foreach($instructores as $i): ?>
                            <tr>
                                <td><?= $i["id"] ?></td>
                                <td><?= $i["nombre"] ?></td>
                                <td><?= $i["telefono"] ?></td>
                                <td><?= $i["especialidad"] ?></td>
                                <td><?= $i["fecha_registro"] ?></td>
                                <td>
                                    <span class="badge bg-<?= $i["estado"]=="Activo" ? "success" : "warning" ?>">
                                        <?= $i["estado"] ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary btn-edit-instructor" data-id="<?= $i["id"] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger btn-delete-instructor" data-id="<?= $i["id"] ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No hay instructores registrados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "app/views/instructores/form.php"; ?>

<hr>
<h3><i class="fas fa-list me-2"></i>Especialidades</h3>
<button class="btn btn-primary mb-3" onclick="showAddEspecialidadModal()">
    <i class="fas fa-plus"></i> Agregar Especialidad
</button>

<div class="card">
  <div class="card-body">
    <table class="table table-striped">
      <thead class="table-info">
        <tr>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($especialidades)): ?>
          <?php foreach($especialidades as $esp): ?>
            <tr>
              <td><?= htmlspecialchars($esp['nombre']) ?></td>
              <td><?= htmlspecialchars($esp['descripcion']) ?></td>
              <td>
                <button class="btn btn-sm btn-success btn-edit-especialidad" data-id="<?= $esp['id'] ?>"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger btn-delete-especialidad" data-id="<?= $esp['id'] ?>"><i class="fas fa-trash"></i></button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="3" class="text-center">No hay especialidades</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Especialidad -->
<div class="modal fade" id="modalEspecialidad" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEspecialidad">
        <div class="modal-header">
          <h5 class="modal-title">Especialidad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="especialidadId">
          <div class="mb-3">
            <label>Nombre</label>
            <input name="nombre" id="especialidadNombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Descripción</label>
            <textarea name="descripcion" id="especialidadDescripcion" class="form-control"></textarea>
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