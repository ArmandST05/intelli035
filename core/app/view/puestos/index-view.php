<?php

$encuestas = EncuestaData::getAll();
$departamentos = DepartamentoData::getAll();
$allPuestos = PuestoData::getAll();
?>

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
        min-width: 220px;
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

    thead {
        background-color: black;
        color: white;
    }

    table {
        width: 80%;
        margin-top: 20px;
    }
</style>
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h4>Puestos</h4>
            <p>
            Indicaciones: En este módulo podrá agregar, editar y eliminar puestos.
             Al añadir un puesto nuevo el sistema asignará en automático las encuestas
              que correspondan al cumplimiento de la Norma en base a la cantidad de
               empleados asignada a la razón social seleccionada. Usted podrá editar 
               las encuestas del puesto, considerando que cada vez que registre un 
               personal nuevo y seleccione su puesto se le añadirán las encuestas que 
               tenga seleccionadas el puesto, esto únicamente dentro de la razón social
                que se tenga seleccionada al momento.
            </p>
        </div>
        <div class="col-md-4">
            <div class="d-flex flex-column gap-2">
                <button type="button" class="btn btn-primary" onclick="openModalAddPuesto()">Nuevo Puesto</button>
                
        </div>
    </div>

</div>
        <input type="text" id="searchInput"  class="form-control mt-2" placeholder="Buscar por nombre o departamento..." onkeyup="filterTable()">
</div>
<table class="table table-bordered" id="puestosTable">
    <thead>
        <tr>
            <th>#</th>
            <th>Nombre del puesto</th>
            <th>Departamento</th>
            <th>Opciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allPuestos as $puesto): ?>
            <tr>
                <td><?php echo htmlspecialchars($puesto->id); ?></td>
                <td><?php echo htmlspecialchars($puesto->nombre); ?></td>
                <td><?php echo htmlspecialchars($puesto->nombre_departamento); ?></td>
                <td>
                    <div class="dropdown">
                        <button class="dropdown-toggle">
                            <span class="dots">...</span>
                        </button>
                        <div class="dropdown-menu">
                            <a href="javascript:void(0)" onclick="editPuesto(<?php echo $puesto->id; ?>)" class="dropdown-item">
                                Editar
                            </a>
                            <a href="javascript:void(0)" onclick="deletePuesto(<?php echo $puesto->id; ?>, '<?php echo $puesto->nombre; ?>')" class="dropdown-item">
                                Eliminar
                            </a>
                            <hr>
                            <a href="javascript:void(0)" onclick="openAssignSurveyModal(<?php echo $puesto->id; ?>)" class="dropdown-item">
                                Asignar encuesta
                            </a>

                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<!-- Modal para asignar encuestas -->
<div class="modal fade" id="assignSurveyModal" tabindex="-1" aria-labelledby="assignSurveyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="assignSurveyModalLabel">Asignar Encuesta</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="assignSurveyForm" method="POST" action="index.php?action=puestos/assign-surveys-role">
                    <!-- Campo oculto para el ID del puesto -->
                    <input type="hidden" id="puestoId" name="puestoId" value="">

                    <!-- Lista de encuestas -->
                    <?php
                    $encuestas = EncuestaData::getAll(); // Obtener todas las encuestas
                    if ($encuestas && count($encuestas) > 0): 
                        foreach ($encuestas as $survey): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="surveyIds[]" value="<?= htmlspecialchars($survey->id) ?>" id="survey<?= htmlspecialchars($survey->id) ?>">
                                <label class="form-check-label" for="survey<?= htmlspecialchars($survey->id) ?>">
                                    <?= htmlspecialchars($survey->title) ?>
                                </label>
                            </div>
                        <?php endforeach;
                    else: ?>
                        <p>No se encontraron encuestas disponibles.</p>
                    <?php endif; ?>
                    
                    <!-- Botón para asignar -->
                    <button type="submit" class="btn btn-primary mt-3">Asignar Encuestas</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function openAssignSurveyModal(puestoId) {
    
    document.querySelector('#assignSurveyModal #puestoId').value = puestoId;
    $('#assignSurveyModal').modal('show');
}
    $(document).ready(function() {
        // Agregar un evento de clic para mostrar y ocultar el menú desplegable
        $('.dropdown-toggle').on('click', function() {
            var dropdown = $(this).closest('.dropdown');
            dropdown.toggleClass('show');
            dropdown.find('.dropdown-menu').toggle();
        });

        // Cerrar el menú si se hace clic fuera de él
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.dropdown').length) {
                $('.dropdown').removeClass('show');
                $('.dropdown-menu').hide();
            }
        });
    });

function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('puestosTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let match = false;

        for (let j = 1; j < cells.length - 1; j++) {
            if (cells[j]) {
                const text = cells[j].textContent || cells[j].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
        }

        rows[i].style.display = match ? '' : 'none';
    }
}
function assignSurveyForRole(puestoId, puestoNombre) {
    // Crear una lista de las encuestas seleccionadas (esto lo puedes modificar dependiendo de cómo se seleccionen las encuestas)
    var selectedSurveys = []; // Ejemplo: [1, 2, 3] si las encuestas 1, 2 y 3 son las seleccionadas

    // Verificamos si hay encuestas seleccionadas
    if (selectedSurveys.length === 0) {
        alert("Por favor, seleccione al menos una encuesta.");
        return;
    }

    // Enviar solicitud AJAX para asignar las encuestas a todos los empleados del puesto
    $.ajax({
        url: './?action=puestos/assign-surveys-role', // Archivo PHP que manejará la lógica del backend
        method: 'POST',
        data: {
            puestoId: puestoId,
            surveyIds: selectedSurveys
        },
        success: function(response) {
            alert("Encuestas asignadas correctamente a todo el personal del puesto " + puestoNombre);
            location.reload();  // Recarga la página después de que se asignan las encuestas
        },
        error: function(xhr, status, error) {
            console.error("Error en la asignación de encuestas: " + error);
            alert("Hubo un error al asignar las encuestas.");
        }
    });
}



</script>

 <!-- Modal for adding new puesto -->
 <div class="modal fade" id="PuestoModal" tabindex="-1" role="dialog" aria-labelledby="PuestoModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">Agregar Nuevo Puesto</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se colocarán los campos del formulario -->
                    <form id="addPuesto" action="index.php?action=puesto/add" method="POST"> 
                        <div class="form-group">
                            <label for="roleName">Nombre del Puesto</label>
                            <input type="text" class="form-control" id="roleName" placeholder="Ingrese el nombre">
                        </div>
                        <div class="form-group">
                            <label for="roleDepartment">Departamento</label>
                            <select class="form-control" id="id_departamento" name="id_departamento" required>
                                <option value="">Seleccione un departamento</option>
                                <?php 
                                    if(!empty($departamentos)){
                                        foreach($departamentos as $departamento){
                                            echo "<option value='{$departamento->idDepartamento}'>{$departamento->nombre}</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="roleEncuesta">Encuesta</label>
                            <select class="form-control" name="id_encuesta" id="id_encuesta">
                                <option value="">Seleccione una encuesta</option>
                                <?php 
                                    if(!empty($encuestas)){
                                        foreach($encuestas as $encuesta){
                                            echo "<option value='{$encuesta->id}'>{$encuesta->title}</option>";
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" id="savePersonalBtn" class="btn btn-primary" onclick="addPuesto()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
         function openModalAddPuesto() {
            $('#PuestoModal').modal('show');
        }

        var newPuestoId = null;
         function addPuesto() {
            if (!newPuestoId) {
                var puestoData = {
                    "roleName": $("#roleName").val(),
                    "id_departamento": $("#id_departamento").val(),
                    "id_encuesta": $("#id_encuesta").val()
                };
                $.ajax({
                    url: "./?action=puestos/add",
                    type: 'POST',
                    data: puestoData,
                    success: function(response) {
                        newPuestoId = response;
                        alert("Puesto agregado correctamente");
                        window.location = "index.php?view=puestos/index";
                    },
                    error: function() {
                        alert("Algo salió mal");
                    }
                });
            } else {
                alert("Este personal ya existe.");
            }
        }

    </script>
<!-- Modal para editar puesto -->

<div class="modal fade" id="EditPuestoModal" tabindex="-1" role="dialog" aria-labelledby="EditPuestoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditPuestoModalLabel">Editar Puesto</h5>
                <input type="hidden" id="editPuestoId" name="id">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editPuestoForm">
                    <div class="form-group">
                        <label for="editRoleName">Nombre del Puesto</label>
                        <input type="text" class="form-control" id="editRoleName" name="nombre" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="editRoleDepartment">Departamento</label>
                        <select class="form-control" id="editRoleDepartment" name="id_departamento" required>
                            <option value="">Seleccione un departamento</option>
                            <?php 
                                if (!empty($departamentos)) {
                                    foreach ($departamentos as $departamento) {
                                        echo "<option value='{$departamento->idDepartamento}'>{$departamento->nombre}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="editRoleEncuesta">Encuesta</label>
                        <select class="form-control" id="editRoleEncuesta" name="id_encuesta">
                            <option value="">Seleccione una encuesta</option> <!-- Opción por defecto -->

                            <?php 
                            if (!empty($encuestas) && is_array($encuestas)) {
                                foreach ($encuestas as $encuesta) {
                                    echo "<option value='{$encuesta->id}'>{$encuesta->title}</option>";
                                }
                            } 
                            ?>
                        </select>
                    </div>


                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="updatePuestoBtn" class="btn btn-primary" onclick="updatePuesto()">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
function editPuesto(id) {
    $.ajax({
        url: "./?action=puestos/get&id=" + id,
        type: "GET",
        dataType: "json", // Asegura que el JSON se procese correctamente
        success: function(response) {
            try {

                // Verificar que los datos sean válidos antes de asignarlos
               
                if (response && response.nombre && response.id_departamento !== undefined && response.id_encuesta !== undefined) {
                    $('#editPuestoId').val(response.id);
                    $('#editRoleName').val(response.nombre);
                    $('#editRoleDepartment').val(response.id_departamento);
                    $('#editRoleEncuesta').val(response.id_encuesta);
                    $('#EditPuestoModal').modal('show'); // Mostrar el modal
                } else {
                    
                    alert("Error: No se pudieron cargar los datos del puesto.");
                }
            } catch (e) {
                alert("Error inesperado al procesar los datos.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:", error);
            alert("Hubo un error al obtener los datos del puesto.");
        }
    });
}



    // Función para actualizar el puesto
    function updatePuesto() {
        var updatedPositionData = {
            "id": $("#editPuestoId").val(),
            "nombre": $('#editRoleName').val(),
            "id_departamento": $('#editRoleDepartment').val(),
            "id_encuesta": $('#editRoleEncuesta').val()
        };

        $.ajax({
            url: "./?action=puestos/update",
            type: 'POST',
            data: updatedPositionData,
            success: function(response) {
               
                    alert("Puesto actualizado correctamente.");
                    window.location.reload(); 
                
            },
            error: function() {
                alert("Hubo un error al actualizar los datos del puesto.");
            }
        });
    }

    // Función para eliminar el puesto
    function deletePuesto(id, nombre) {
        const swalWithBootstrapButtons = Swal.mixin({
            buttonsStyling: true
        });

        swalWithBootstrapButtons.fire({
            title: '¿Estás seguro de eliminar el puesto ' + nombre + '?',
            text: "¡No podrás revertirlo!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: '¡No, cancelarlo!',
            reverseButtons: true
        }).then((result) => {
            if (result.value === true) {
                window.location.href = "index.php?action=puestos/delete&id=" + id;
            }
        });
    }
</script>
