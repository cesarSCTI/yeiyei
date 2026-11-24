<div class="container-fluid p-3">
  <h2><i class="fas fa-history me-2"></i> Bitácora de Movimientos</h2>
  <div class="card mt-3">
    <div class="card-body">
      <table class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Entidad</th>
            <th>ID Entidad</th>
            <th>Descripción</th>
            <th>Fecha</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($logs as $log): ?>
          <tr>
            <td><?= $log["id"] ?></td>
            <td><?= $log["username"] ?> (<?= $log["name"] ?>)</td>
            <td><?= $log["accion"] ?></td>
            <td><?= $log["entidad"] ?></td>
            <td><?= $log["entidad_id"] ?></td>
            <td><?= htmlspecialchars($log["descripcion"]) ?></td>
            <td><?= $log["created_at"] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
