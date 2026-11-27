<div class="d-flex justify-content-between mb-3">
  <h2><i class="fas fa-users"></i> Alumnos en la Clase</h2>
  <button class="btn btn-primary" onclick="showAddClaseAlumnoModal(<?= $clase_id ?>)">
    <i class="fas fa-plus"></i> Asignar Alumno
  </button>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Alumno</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($alumnos as $a): ?>
        <tr>
          <td><?= $a["id"] ?></td>
          <td><?= $a["cliente"] ?></td>
          <td><?= $a["estado"] ?></td>
          <td>
            <?php if (yams_puede_modificar()): ?>
            <button class="btn btn-sm btn-success btn-edit-clasealumno" data-id="<?= $a['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger btn-delete-clasealumno" data-id="<?= $a['id'] ?>"><i class="fas fa-trash"></i></button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Asignación -->
<div class="modal fade" id="modalClaseAlumno" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formClaseAlumno">
        <div class="modal-header">
          <h5 class="modal-title">Asignar Alumno</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="caId">
          <input type="hidden" name="clase_id" id="caClaseId" value="<?= $clase_id ?>">
          <div class="mb-3">
            <label>Alumno</label>
            <select name="cliente_id" id="caCliente" class="form-control">
              <?php foreach($clientes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Estado</label>
            <select name="estado" id="caEstado" class="form-control">
              <option value="Activo">Activo</option>
              <option value="Cancelado">Cancelado</option>
              <option value="Movido">Movido</option>
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
