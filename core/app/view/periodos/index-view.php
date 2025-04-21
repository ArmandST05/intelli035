<?php

$periodos = PeriodoData::getAll(); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Periodos</title>
</head>

<style>
    /* Estilo del botón de los tres puntos */
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-toggle {
        background: none;
        border: none;
        
        cursor: pointer;
        padding: 0;
        
    }

    .dots {
        font-size: 24px;
    }

    /* Estilo del menú desplegable */
    .dropdown-menu {
        display: none;
        position: absolute;
        background-color: white;
        min-width: 120px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
        padding: 10px 0;
        border-radius: 5px;
    }

    .dropdown-item {
        padding: 8px 16px;
        text-decoration: none;
        color: #333;
        display: block;
        font-size: 14px;
    }

    .dropdown-item:hover {
        background-color: #f1f1f1;
    }

    /* Mostrar el menú cuando el botón es clickeado */
    .dropdown.show .dropdown-menu {
        display: block;
    }
    thead{
        background-color: black;
        color: white;
    }
</style>
<body>
 <!-- Botón para abrir el modal -->
<button type="button" class="btn btn-primary" onclick="openModalNuevoPeriodo()">Nuevo Periodo</button>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre del Periodo</th>
            <th>Fecha de Inicio</th>
            <th>Fecha de Fin</th>
            <th>Empresa asignda</th>
            <th>Status</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($periodos as $periodo): ?>
            <tr>
                <td><?php echo htmlspecialchars($periodo->id); ?></td>
                <td><?php echo htmlspecialchars($periodo->name); ?></td>
                <td><?php echo htmlspecialchars($periodo->start_date); ?></td>
                <td><?php echo htmlspecialchars($periodo->end_date); ?></td>
                <td><?php echo htmlspecialchars($periodo->empresa_id); ?></td>
                <td><?php echo htmlspecialchars($periodo->status); ?></td>
                <!-- Aquí, dentro de la tabla, agregamos el botón de editar -->
                <td>
                    <div class="dropdown">
                        <button class="dropdown-toggle">
                            <span class="dots">...</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0)" onclick="editPeriod(<?php echo $periodo->id; ?>)" class="dropdown-item">
                                Editar
                            </a>
                            <a href="javascript:void(0)" onclick="deletePeriod(<?php echo $periodo->id; ?>, '<?php echo $periodo->name; ?>', event)" class="dropdown-item">
                                Eliminar
                            </a>

                            <a href="javascript:void(0)" onclick="openModalAssign(<?php echo $periodo->id; ?>, <?php echo $periodo->empresa_id; ?>)" class="dropdown-item">
                                Asignar empresa
                            </a>

                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<!-- Modal Nuevo Periodo -->
<div class="modal fade" id="NuevoPeriodoModal" tabindex="-1" role="dialog" aria-labelledby="NuevoPeriodoModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="NuevoPeriodoModalTitle">Nuevo Periodo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <form id="nuevoPeriodoForm" action="index.php?action=periodos/add-period" method="POST">
    <div class="form-group">
        <label for="period-name">Nombre del Periodo</label>
        <input type="text" class="form-control" id="name" name="name" placeholder="Ingrese el nombre del periodo" required>
    </div>
    <div class="form-group">
        <label for="start-date">Fecha de Inicio</label>
        <input type="date" id="start-date" name="start_date" class="form-control" required>
    </div>
    <div class="form-group">
        <label for="end-date">Fecha de Fin</label>
        <input type="date" class="form-control" id="end-date" name="end_date" required>
    </div>
    <div class="form-group">
        <label for="status">Estado</label>
        <select class="form-control" id="status" name="status" required>
            <option value="activo">Activo</option>
            <option value="inactivo">Inactivo</option>
        </select>
    </div>
    <div class="form-group">
        <label for="empresa">Empresa asignada</label>
        <select name="empresa_id" id="empresa_id" class="form-control" required>
            <option value="">Selecciona la empresa a la que se va a asignar</option>
            <?php
                $empresas = EmpresaData::getAll();
                foreach ($empresas as $empresa) {
                    echo '<option value="' . $empresa->id . '">' . $empresa->nombre . '</option>'; 
                }
            ?>
        </select>
    </div>
        </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="submitNuevoPeriodoForm()">Guardar Periodo</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar Periodo -->
<div class="modal fade" id="EditPeriodModal" tabindex="-1" role="dialog" aria-labelledby="EditPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="EditPeriodModalLabel">Editar Periodo</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPeriodForm">
                    <div class="form-group">
                        <input type="hidden" id="edit-period-id" name="id">
                        <label for="edit-period-name">Nombre del Periodo</label>
                        <input type="text" class="form-control" id="edit-period-name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="edit-start-date">Fecha de Inicio</label>
                        <input type="date" class="form-control" id="edit-start-date" name="start_date">
                    </div>
                    <div class="form-group">
                        <label for="edit-end-date">Fecha de Fin</label>
                        <input type="date" class="form-control" id="edit-end-date" name="end_date">
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Estado</label>
                        <select class="form-control" id="edit-status" name="status">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal Asignar Encuestas y encuestas a un periodo -->
<div class="modal fade" id="assignSurveysModal" tabindex="-1" role="dialog" aria-labelledby="assignSurveysModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assignSurveysModal">Asignar Encuestas</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPeriodForm" action="index.php?action=periodos/assign-survey" method="POST">
                    <input type="hidden" id="empresa-id" name="empresa_id">
                    <input type="hidden" id="period-id" name="period_id">

                    <div class="form-group">
                        <label for="edit-period-name">Encuestas</label>
                        <?php
                            // Obtener todas las encuestas desde la clase EncuestaData
                            $encuestas = EncuestaData::getAll();

                            // Verificar si hay encuestas disponibles
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
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>


  // Función para manejar la apertura del menú
  document.querySelectorAll('.dropdown-toggle').forEach(function(button) {
        button.addEventListener('click', function() {
            var dropdown = button.parentElement;
            dropdown.classList.toggle('show'); // Alternar el estado del dropdown
        });
    });

    // Cerrar el menú si se hace clic fuera de él
    window.addEventListener('click', function(event) {
        document.querySelectorAll('.dropdown').forEach(function(dropdown) {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
    });

    window.onload = function() {
        // Crear un objeto Date para obtener la fecha actual
        var today = new Date();

        // Formatear la fecha a YYYY-MM-DD
        var dd = String(today.getDate()).padStart(2, '0');
        var mm = String(today.getMonth() + 1).padStart(2, '0'); // Enero es 0
        var yyyy = today.getFullYear();

        today = yyyy + '-' + mm + '-' + dd;  // Formato YYYY-MM-DD

        // Asignar la fecha de hoy al campo de fecha
        document.getElementById("start-date").value = today;
    };

    function openModalNuevoPeriodo() {
    // Muestra el modal de nuevo periodo
    $('#NuevoPeriodoModal').modal('show');
}
function openModalAssign(periodId, empresaId) {
    document.getElementById("period-id").value = periodId;
    document.getElementById("empresa-id").value = empresaId;  // Asigna el ID de la empresa al campo oculto
    $('#assignSurveysModal').modal('show');
}


function openModalEditarPeriodo() {
    // Muestra el modal de nuevo periodo
    $('#EditPeriodModal').modal('show');
}

// Función para obtener la lista actualizada de periodos
function refreshPeriodosList() {
    $.ajax({
        url: './?action=periodos/get-all-periods', // Ruta que devuelve la lista de periodos
        type: 'GET', // Método de envío
        dataType: 'json', // Esperamos una respuesta JSON
        success: function(response) {
            console.log('Lista de periodos actualizada:', response); // Verifica la respuesta
            if (response.status === 'success') {
                // Llamar a la función para actualizar la tabla con los nuevos datos
                updatePeriodosTable(response.data);
            } else {
                alert('Error al obtener la lista de periodos.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error de AJAX al obtener la lista de periodos:', xhr.responseText);
            alert('Hubo un error al obtener la lista de periodos.');
        }
    });
}
function submitNuevoPeriodoForm() {
    $.ajax({
        url: './?action=periodos/add-period',
        type: 'POST',
        data: $('#nuevoPeriodoForm').serialize(),  // Captura los datos del formulario
        dataType: 'json',
        success: function(response) {
            console.log("Todo salió bien:", response);
            
            // Cierra el modal
            $('#NuevoPeriodoModal').modal('hide');

            // Muestra un mensaje de éxito
            alert('Periodo guardado correctamente');

            // Opcional: recargar la página o actualizar la lista de periodos
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Error al guardar el periodo:', xhr.responseText);
            alert('Hubo un error al guardar el periodo.');
        }
    });
}

// Función para actualizar la tabla de periodos con los datos recibidos
function updatePeriodosTable(periodos) {
    var tableBody = $('#periodosTable tbody'); // Asume que tienes una tabla con ID 'periodosTable'
    tableBody.empty(); // Limpiar la tabla antes de agregar los nuevos registros

    // Iterar sobre los periodos y agregar cada uno como una fila de la tabla
    periodos.forEach(function(periodo) {
        var row = '<tr>' +
            '<td>' + periodo.name + '</td>' +
            '<td>' + periodo.start_date + '</td>' +
            '<td>' + periodo.end_date + '</td>' +
            '<td>' + periodo.status + '</td>' +
            '<td>' + periodo.empresa_nombre + '</td>' + // Asume que "empresa_nombre" es el nombre de la empresa
            '</tr>';
        tableBody.append(row); // Agregar la nueva fila a la tabla
    });
}


// Escuchar eventos para la edición y eliminación de periodos
$(document).ready(function () {
    // Manejar clic en los botones del menú desplegable
    $('.dropdown-menu a').on('click', function (e) {
        e.preventDefault(); // Prevenir la acción por defecto del enlace

        // Extraer el atributo 'onclick' del enlace
        const action = $(this).attr('onclick');

        // Evaluar la función correspondiente
        eval(action);
    });
});

function editPeriod(periodId) {
    console.log("ID enviado: ", periodId); // Verifica en consola

    $.ajax({
        url: './?action=periodos/get-period-data',
        type: 'GET',
        data: { id: periodId },
        success: function(response) {
            try {
                const data = JSON.parse(response);

                if (data.status === 'success') {
                    console.log("Valor actual de #edit-period-id:", $('#edit-period-id').val());

                    $('#edit-period-id').val(periodId);

                    // Asignar valores al formulario
                    $('#edit-period-name').val(data.data.name);
                    $('#edit-start-date').val(data.data.start_date);
                    $('#edit-end-date').val(data.data.end_date);
                    $('#edit-status').val(data.data.status);

                    // Mostrar el modal
                    $('#EditPeriodModal').modal('show');
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error al procesar la respuesta del servidor.');
            }
        },
        error: function() {
            alert('Error al obtener los datos del periodo.');
        }
    });
}



$('#editPeriodForm').on('submit', function(e) {
    e.preventDefault(); 

    const id = $('#edit-period-id').val(); // Ahora sí se obtiene el ID
    const name = $('#edit-period-name').val();
    const startDate = $('#edit-start-date').val();
    const endDate = $('#edit-end-date').val();
    const status = $('#edit-status').val();

    console.log("ID enviado en la solicitud POST:", id); // Verificar en consola

    $.ajax({
        url: './?action=periodos/update',
        type: 'POST',
        data: {
            id: id, // Ahora el ID se enviará correctamente
            name: name,
            start_date: startDate,
            end_date: endDate,
            status: status
        },
        success: function(response) {
            console.log("Respuesta del servidor:", response);
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Periodo actualizado correctamente.');
                    location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                alert('Error al procesar la respuesta del servidor.');
            }
        },
        error: function() {
            alert('Error al enviar los datos.');
        }
    });
});

function deletePeriod(periodoId, periodoName) {
   

    const swalWithBootstrapButtons = Swal.mixin({
        buttonsStyling: true
    });

    swalWithBootstrapButtons.fire({
        title: `¿Estás seguro de eliminar el periodo "${periodoName}"?`,
        text: "¡No podrás revertirlo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: 'No, cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.value === true) {
            // Redirigir a la acción de eliminación de puesto
            window.location.href = "index.php?action=periodos/delete&id=" + periodoId;
        }
    });
}


</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
