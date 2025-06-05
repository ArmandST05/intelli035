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
<div class="modal fade" id="assignSurveyModal" tabindex="-1" aria-labelledby="assignSurveyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Asignar Encuesta</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="assignSurveyForm" method="POST" action="./?action=personal/assign-surveys">
          <!-- Campo oculto para múltiples IDs -->
<input type="hidden" id="selectedEmployees" name="selectedEmployees" value="">

          <!-- Lista de encuestas (generada con PHP) -->
          <?php
          $encuestas = EncuestaData::getAll();
          if ($encuestas && count($encuestas) > 0) {
              foreach ($encuestas as $survey) {
                  echo '
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="surveys[]" value="' . $survey->id . '" id="survey' . $survey->id . '">
                    <label class="form-check-label" for="survey' . $survey->id . '">
                      ' . htmlspecialchars($survey->title) . ' - <small>' . htmlspecialchars($survey->description) . '</small>
                    </label>
                  </div>';
              }
          } else {
              echo '<p>No se encontraron encuestas disponibles.</p>';
          }
          ?>

          <button type="submit" class="btn btn-primary mt-3">Asignar</button>
        </form>
      </div>
    </div>
  </div>
</div>

    <button class="btn btn-primary" onclick="sendMail()">Enviar credenciales por correo</button>
<button onclick="sendWhatsappMassive()" class="btn btn-success">Enviar WhatsApp Masivo</button>
<button type="button" class="btn btn-warning" onclick="openAssignSurveyModal()">Asignar Encuestas</button>

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
function openAssignSurveyModal() {
    let selected = [];

    // ✅ Captura solo el valor del checkbox (el ID)
    $('input[type="checkbox"].row-select:checked').each(function () {
        const id = $(this).val(); // ✅ aquí está el error: asegúrate de usar `.val()` y no `$(this)`
        console.log('Checkbox empleado seleccionado con valor:', id);
        selected.push(id);
    });

    console.log("Lista de empleados seleccionados en openAssignSurveyModal:", selected);

    // ✅ Asigna el array como string separado por comas
    $('#selectedEmployees').val(selected.join(','));
    console.log("Input oculto #selectedEmployees actualizado con:", $('#selectedEmployees').val());

    $('#assignSurveyModal').modal('show');
}

$(document).ready(function () {
    console.log('Documento listo, configurando eventos');

    // Abrir modal cuando se da click al botón
    $('#btnAssignSurveys').click(function () {
        console.log('Botón btnAssignSurveys clickeado');

        let selectedIds = [];
        // Obtener IDs de empleados seleccionados en la tabla (checkboxes marcados)
        $('#lookup tbody input[type="checkbox"]:checked').each(function () {
            let idEmpleado = $(this).closest('tr').find('td:nth-child(2)').text().trim();
            console.log('Empleado seleccionado (ID extraído de la tabla):', idEmpleado);
            selectedIds.push(idEmpleado);
        });

        console.log('Lista completa de empleados seleccionados:', selectedIds);

        if (selectedIds.length === 0) {
            alert('Por favor selecciona al menos un empleado.');
            console.warn('No hay empleados seleccionados al intentar abrir modal');
            return;
        }

        // Guardar IDs en el input oculto que el backend espera
$('#selectedEmployees').val(selected.join(','));
console.log("Lista de empleados seleccionados en openAssignSurveyModal:", selected);
console.log("Input oculto #selectedEmployees actualizado con:", $('#selectedEmployees').val());

        // Mostrar modal
        $('#assignSurveyModal').modal('show');
        console.log('Modal assignSurveyModal mostrado');
    });

    // Enviar formulario por AJAX
    $('#assignSurveyForm').submit(function (e) {
        e.preventDefault();
        console.log('Formulario assignSurveyForm enviado');

        const formData = $(this).serialize();
        console.log('Datos serializados para enviar:', formData);

        $.ajax({
            url: './?action=encuestas/assign-surveys',  // Cambia la URL si es necesario
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta recibida del servidor:', response);

                if (response.status === 'success') {
                    alert(response.message);
                    console.log('Encuestas asignadas correctamente');
                    $('#assignSurveyModal').modal('hide');

                    // Opcional: desmarcar checkboxes después de asignar
                    $('#lookup tbody input[type="checkbox"]:checked').prop('checked', false);
                    console.log('Checkboxes desmarcados después de asignar');
                } else {
                    alert(response.message);
                    console.warn('Error recibido del backend:', response.message);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                alert('Error en la solicitud, intenta de nuevo.');
            }
        });
    });
});



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
               render: function(checkboxHtml) {
      return checkboxHtml; // simplemente devuelves el checkbox que envía el backend
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
