<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<style>
  #calendar {
    max-width: 100%;
    margin: 0 auto;
  }

  /* Asegura que el contenedor del calendario crezca con el viewport */
  .fc {
    font-size: 0.9rem;
  }

  /* Ajuste en pantallas pequeñas */
  @media (max-width: 768px) {
    .fc-toolbar-title {
      font-size: 1rem !important;
    }
    .fc .fc-daygrid-event {
      font-size: 0.75rem;
      padding: 2px;
    }
  }
</style>


<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <i class="fas fa-calendar-alt me-2"></i> Calendario de Clases
  </div>
  <div class="card-body">
    <div id="calendar"></div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const calendarEl = document.getElementById('calendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'listWeek',
    locale: 'es',
    height: 'auto',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,listWeek'
    },
    events: [
      <?php foreach($clases as $c): ?>
      {
        id: "<?= $c['id'] ?>",
        title: "<?= $c['nombre'] ?> (<?= $c['categoria'] ?>)",
        startTime: "<?= $c['hora_inicio'] ?>",
        endTime: "<?= $c['hora_fin'] ?>",
        daysOfWeek: [<?= implode(',', array_map(fn($d) => 
            ['Domingo'=>0,'Lunes'=>1,'Martes'=>2,'Miércoles'=>3,'Jueves'=>4,'Viernes'=>5,'Sábado'=>6][$d] ?? 0, 
            explode(',', $c['dias'])
        )) ?>],
        color: "<?= $c['estado']=='Activa' ? '#28a745' : ($c['estado']=='Cancelada' ? '#dc3545' : '#ffc107') ?>"
      },
      <?php endforeach; ?>
    ],
    eventClick: function(info) {
      const claseId = info.event.id;
      fetch('index.php?route=clasealumno/getByClase', {
        method: 'POST',
        body: new URLSearchParams({ clase_id: claseId })
      })
      .then(r => r.json())
      .then(data => {
        let alumnosHtml = data.length ? 
          data.map(a => `<li>${a.cliente}</li>`).join('') : '<li>No hay alumnos asignados</li>';
        document.getElementById('modalClaseAlumnosBody').innerHTML = `<ul>${alumnosHtml}</ul>`;
        new bootstrap.Modal(document.getElementById('modalClaseAlumnos')).show();
      });
    }
  });
  calendar.render();
  return calendar;
  window.addEventListener('resize', () => {
    calendar.updateSize();
  });
});
</script>

<!-- Modal para alumnos de clase -->
<div class="modal fade" id="modalClaseAlumnos" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Alumnos Asignados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="modalClaseAlumnosBody"></div>
    </div>
  </div>
</div>


<div class="d-flex justify-content-between mb-3">
  <h2><i class="fas fa-swimming-pool"></i> Clases</h2>
  <button class="btn btn-primary" onclick="showAddClaseModal()">
    <i class="fas fa-plus"></i> Nueva Clase
  </button>
</div>

<div class="card">
  <div class="card-body">
    <table class="table table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Instructor</th>
          <th>Días</th>
          <th>Horario</th>
          <th>Estado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($clases as $c): ?>
        <tr>
          <td><?= $c["id"] ?></td>
          <td><?= $c["nombre"] ?></td>
          <td><?= $c["categoria"] ?></td>
          <td><?= $c["instructor"] ?></td>
          <td><?= $c["dias"] ?></td>
          <td><?= $c["hora_inicio"] ?> - <?= $c["hora_fin"] ?></td>
          <td><?= $c["estado"] ?></td>
          <td>
            <button class="btn btn-sm btn-success btn-edit-clase" data-id="<?= $c['id'] ?>"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger btn-delete-clase" data-id="<?= $c['id'] ?>"><i class="fas fa-trash"></i></button>
            <a href="index.php?route=clasealumno/index&clase_id=<?= $c['id'] ?>" class="btn btn-sm btn-info"><i class="fas fa-users"></i> Alumnos</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal Clase -->
<div class="modal fade" id="modalClase" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formClase">
        <div class="modal-header">
          <h5 class="modal-title">Clase</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="claseId">
          <!--<div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nombre" id="claseNombre" class="form-control" required>
          </div>-->
          <div class="mb-3">
            <label>Cliente (Alumno)</label>
            <div class="input-group">
              <input type="hidden" name="cliente_id" id="claseClienteId">
              <!--<input type="text" id="claseClienteNombre" class="form-control" placeholder="Selecciona un cliente..." readonly required>-->
              <input type="text" id="claseNombre" name="nombre" class="form-control" placeholder="Selecciona un cliente..." readonly required>
              <button type="button" id="btnBuscarClienteClase" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBuscarClienteClase">
                <i class="fas fa-search"></i>
              </button>
            </div>
          </div>

          <div class="mb-3">
            <label>Categoría</label>
            <select name="categoria" id="claseCategoria" class="form-control">
              <option value="Adulto">Adulto</option>
              <option value="Adolescente">Adolescente</option>
              <option value="Niño">Niño</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Instructor</label>
            <select name="instructor_id" id="claseInstructor" class="form-control">
              <?php foreach($instructores as $i): ?>
                <option value="<?= $i['id'] ?>"><?= $i['nombre'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-3">
            <label>Días</label>
            <input type="text" name="dias" id="claseDias" class="form-control" placeholder="Ej: Lunes,Miércoles,Viernes">
          </div>
          <div class="mb-3">
            <label>Hora Inicio</label>
            <input type="time" name="hora_inicio" id="claseInicio" class="form-control">
          </div>
          <div class="mb-3">
            <label>Hora Fin</label>
            <input type="time" name="hora_fin" id="claseFin" class="form-control">
          </div>
          <div class="mb-3">
            <label>Estado</label>
            <select name="estado" id="claseEstado" class="form-control">
              <option value="Activa">Activa</option>
              <option value="Cancelada">Cancelada</option>
              <option value="Reprogramada">Reprogramada</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Buscar Cliente para Clases -->
<div class="modal fade" id="modalBuscarClienteClase" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Seleccionar Cliente para Clase</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <input type="text" id="buscarClienteClaseInput" class="form-control" placeholder="Buscar por nombre o teléfono...">
        </div>

        <table class="table table-striped" id="tablaClientesClase">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Teléfono</th>
              <th>Tipo de Clase</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="clientesClaseBody">
            <?php foreach($clientes as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nombre']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['tipo_clase']) ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary seleccionar-cliente-clase"
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
