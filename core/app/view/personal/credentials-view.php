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
<button onclick="sendWhatsappMassive()" class="btn btn-success">Enviar WhatsApp Masivo</button>

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
      id: row[0], // como corregimos antes
      name: row[1],
      department: row[2],
      username: row[3],
      password: row[4],
      email: row[6],
      phone: row[7]
    });
  });

  if (!users.length) {
    alert('No hay usuarios seleccionados.');
    return;
  }

  // Enviar correos uno por uno para que backend reciba $_POST['id']
  users.forEach(function(user){
    $.ajax({
      url: './?action=notifications/send-massive-mail',
      method: 'POST',
      data: { id: user.id }, // aquí mandamos id simple
      success: function(resp) {
        console.log('Respuesta servidor:', resp);
      },
      error: function(xhr, status, error) {
        console.error('Error al enviar correo:', error);
      }
    });
  });

  alert('Solicitudes de envío de correo enviadas. Revisa consola para detalles.');
}
function sendWhatsappMassive() {
  console.log("Iniciando función sendWhatsappMassive...");

  const users = [];
  $('#lookup tbody input.row-select:checked').each(function(index) {
    const $row = $(this).closest('tr');
    const row = dataTable.row($row).data();

    console.log(`Procesando fila ${index}:`, row);

    if (!row) {
      console.warn(`Fila ${index} no tiene datos en DataTable.`);
      return;
    }

    const id = row[0]; // Asegúrate de que esta posición contiene el ID

    if (!id) {
      console.warn(`Fila ${index} tiene id inválido:`, id);
      return;
    }

    users.push({ id: id }); // Solo enviamos el ID, el backend se encarga del resto
  });

  console.log("Usuarios seleccionados para enviar WhatsApp:", users);

  if (!users.length) {
    alert('No hay usuarios seleccionados.');
    return;
  }

  $.ajax({
    url: './?action=notifications/send-massive-whatsapp',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ users: users }),
    beforeSend: function() {
      console.log("Enviando datos al servidor...");
    },
    success: function(resp) {
      console.log("Respuesta recibida del servidor:", resp);
      if (resp.success) {
        resp.results.forEach(function(userResult) {
          if (userResult.success && userResult.link) {
            window.open(userResult.link, '_blank');
          } else {
            console.error("Error en el resultado de usuario:", userResult);
          }
        });
        alert('Se generaron los links de WhatsApp para los usuarios seleccionados.');
      } else {
        alert('Error del servidor: ' + resp.message);
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.error("Error en la petición AJAX:", textStatus, errorThrown);
      alert('Error en la petición. Intenta de nuevo.');
    },
    complete: function() {
      console.log("Petición AJAX finalizada.");
    }
  });
}



</script>
