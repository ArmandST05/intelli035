<?php 
$departamentos = DepartamentoData::getAll();
$empresas      = EmpresaData::getAll();
?>
<div class="card" style="width:100%; margin-top:20px">
  <div class="card-body">
    <div class="row mb-3">
      <!-- Filtro por departamento -->
      <div class="col-md-4">
        <label for="filter-department">Departamento:</label>
        <select id="filter-department" class="form-control">
          <option value="">Todos</option>
        </select>
      </div>
      <!-- Búsqueda personalizada -->
      <div class="col-md-4">
        <label for="custom-search">Buscar:</label>
        <input type="text" id="custom-search" class="form-control" placeholder="Buscar...">
      </div>
      <!-- Filtro por empresa -->
      <div class="col-md-4">
        <label for="filter-company">Empresa:</label>
        <select id="filter-company" class="form-control">
          <option value="">Todas</option>
          <?php foreach($empresas as $empresa): ?>
            <option value="<?= $empresa->id ?>"><?= htmlspecialchars($empresa->nombre) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Selector de cantidad de registros -->
      <div class="col-md-4 mt-3">
        <label for="custom-length">Registros por página:</label>
        <select id="custom-length" class="form-control">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
    </div>

    <button class="btn btn-primary" onclick="sendMail()">Enviar credenciales por correo</button>
    <button class="btn btn-primary">Enviar credenciales por Whatsapp</button>

    <br><br>

    <table id="lookup" class="table table-striped table-hover">
      <thead style="background:#484848; color:#fff;">
        <tr>
          <th><input type="checkbox" id="select-all"></th>
          <th>#</th>
          <th>Nombre</th>
          <th>Departamento / Puesto</th>
          <th>Usuario</th>
          <th>Clave</th>
          <th>Correo</th>
          <th>Teléfono</th>
        </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>

<script>
  // Hacemos dataTable global para usarlo en filtros
  var dataTable;

  $(document).ready(function() {
    // Cargar departamentos
    $.getJSON('./?action=departamentos/get-all', function(depts) {
      depts.forEach(function(d){
        $('#filter-department')
          .append('<option value="'+d.id+'">'+d.nombre+'</option>');
      });
    });

    // Inicializar DataTable
    dataTable = $('#lookup').DataTable({
      language: {
        sProcessing:   "Procesando...",
        sZeroRecords:  "No se encontraron resultados",
        sEmptyTable:   "Ningún dato disponible en esta tabla",
        sInfo:         "Mostrando _START_ a _END_ de _TOTAL_ registros",
        sInfoFiltered: "(filtrado de _MAX_ registros totales)",
        sLoadingRecords: "Cargando...",
        oPaginate: {
          sFirst:    "Primero",
          sLast:     "Último",
          sNext:     "Siguiente",
          sPrevious: "Anterior"
        }
      },
      processing: true,
      serverSide: true,
      ordering:   false,
      responsive: true,
      scrollX:    true,
      dom:        '<"datatable-content"t><"datatable-footer"ip>',

      ajax: {
        url: './?action=personal/get-all-notifications',
        type: 'POST',
        data: function(d) {
          d.department_filter = $('#filter-department').val();
          d.company_filter    = $('#filter-company').val();
          d.custom_search     = $('#custom-search').val();
          d.length            = $('#custom-length').val();
        }
      },

      columns: [
        { // checkbox
          data: 0,
          orderable: false,
          render: function(id) {
            return '<input type="checkbox" class="row-select" value="'+id+'">';
          }
        },
        { data: 1 }, // id
        { data: 2 }, // nombre
        { data: 3 }, // departamento
        { data: 4 }, // usuario
        { data: 5 }, // clave
        { data: 6 }, // correo
        { data: 7 }  // teléfono
      ]
    });

    // “Select all” checkbox
    $('#select-all').on('change', function(){
      var checked = $(this).prop('checked');
      $('.row-select').prop('checked', checked);
    });

    // Resetear select-all tras cada draw
    dataTable.on('draw', function(){
      $('#select-all').prop('checked', false);
    });
  });

  // Recargar DataTable cuando cambian los filtros
  $('#filter-department, #filter-company, #custom-search, #custom-length')
    .on('change keyup', function(){
      dataTable.ajax.reload();
    });

  // Función de envío masivo
  function sendMail() {
    var users = [];
    $('#lookup tbody input.row-select:checked').each(function(){
      var row = dataTable.row($(this).closest('tr')).data();
      users.push({
        id:       row[1],
        name:     row[2],
        department: row[3],
        username: row[4],
        password: row[5],
        email:    row[6],
        phone:    row[7]
      });
    });

    if (!users.length) {
      return alert('No hay usuarios seleccionados.');
    }

    $.ajax({
      url: './?action=notifications/send-massive-mail',
      method: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ users: users }),
      success: function(resp) {
        var res = JSON.parse(resp);
        alert(res.success 
              ? 'Correos enviados exitosamente.' 
              : 'Errores al enviar:\n' + res.errors.join('\n'));
      },
      error: function() {
        alert('Error en la petición. Intenta de nuevo.');
      }
    });
  }
</script>
