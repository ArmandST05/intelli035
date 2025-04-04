<?php

$cantidadEmpleados = EmpresaData::getCantidades();
$empresas = EmpresaData::getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas</title>
    <link rel="stylesheet" href="path_to_bootstrap.css"> <!-- Asegúrate de que la hoja de estilos de Bootstrap esté incluida -->
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <h4>Empresas</h4>
                <p>
                    Indicaciones: En este módulo podrá agregar, editar y eliminar razones sociales. Y podrá administrar a sus empleados depende a la razón social que seleccione.
                </p>
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-column gap-2">
                    <button type="button" class="btn btn-primary" onclick="openModalAddPuesto()">Nueva razón social</button>
                </div>
            </div>
        </div>

       <!-- Card with table -->
<div class="card mt-4" style="width: 90%; margin: auto; margin-top: 20px;">
    <div class="card-body">
        <table class="table table-striped table-hover">
            <thead style="background-color: grey; color: white;">
                <tr>
                    <th>#</th>
                    <th>Logo</th> <!-- Nueva columna para el logo -->
                    <th>Nombre de la razón social</th>
                    <th>Cantidad de personal</th>
                    <th>Comentarios</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($empresas)) {
                    $index = 1;
                    foreach ($empresas as $empresa) {
                        // Si la empresa tiene un logo, lo mostramos; si no, ponemos una imagen por defecto
                        $logoPath = !empty($empresa->logo) ? "uploads/logos/{$empresa->logo}" : "assets/img/default-logo.png";

                        echo "<tr>";
echo "<td>{$index}</td>";
echo "<td><img src='" . (!empty($empresa->logo) ? $empresa->logo : "assets/img/default-logo.png") . "' alt='Logo' style='width: 50px; height: 50px; border-radius: 5px;'></td>"; // Imagen del logo
echo "<td>{$empresa->nombre}</td>";
echo "<td>{$empresa->cantidad_descripcion}</td>"; 
echo "<td>{$empresa->comentarios}</td>";
echo "<td>
        <button class='btn btn-primary btn-sm' onclick='editEmpresa({$empresa->id})'>Editar</button>
        <button class='btn btn-danger btn-sm' onclick='deleteEmpresa({$empresa->id}, \"{$empresa->nombre}\")'>Eliminar</button>
      </td>";
echo "</tr>";
$index++;

                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

            </div>
        </div>

    </div>
</body>
</html>

    <!-- Modal for adding new puesto -->
    <div class="modal fade" id="EmpresaModal" tabindex="-1" role="dialog" aria-labelledby="EmpresaModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLongTitle">Agregar razón social</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se colocarán los campos del formulario -->
                    <form id="addPuesto" action="index.php?action=empresas/add" method="POST"> 
                        <div class="form-group">
                            <label for="razonNombre">Nombre de la razón social</label>
                            <input type="text" class="form-control" name="id_nombre" id="id_nombre" placeholder="Razón social" required>
                        </div>
                        <div class="form-group">
                            <label for="razonComentarios">Comentarios: </label>
                            <textarea name="id_comentarios" id="id_comentarios" class="form-control" placeholder="Comentarios o descripción de la razón social" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="razonCantidad">Número de empleados</label>
                            <select class="form-control" name="id_cantidad" id="id_cantidad" required>
                                <option value="">Seleccione una cantidad</option>
                                <?php 
                                    if(!empty($cantidadEmpleados)){
                                        foreach($cantidadEmpleados as $cantidad){
                                            echo "<option value='{$cantidad->id}'>{$cantidad->descripcion}</option>";
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






    <div class="modal fade" id="EditEmpresaModal" tabindex="-1" role="dialog" aria-labelledby="EditEmpresaModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="exampleModalLongTitle">Editar razón social</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Formulario para editar la empresa -->
                <form id="editPuesto" action="index.php?action=empresas/edit" method="POST">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="form-group">
                        <label for="editRazonNombre">Nombre de la razón social</label>
                        <input type="text" class="form-control" name="id_nombre" id="edit_id_nombre" placeholder="Razón social" required>
                    </div>
                    <div class="form-group">
                        <label for="editRazonComentarios">Comentarios: </label>
                        <textarea name="id_comentarios" id="edit_id_comentarios" class="form-control" placeholder="Comentarios o descripción de la razón social" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editRazonCantidad">Número de empleados</label>
                        <select class="form-control" name="id_cantidad" id="edit_id_cantidad" required>
                            <option value="">Seleccione una cantidad</option>
                            <?php 
                                if (!empty($cantidadEmpleados)) {
                                    foreach ($cantidadEmpleados as $cantidad) {
                                        echo "<option value='{$cantidad->id}'>{$cantidad->descripcion}</option>";
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" id="savePersonalBtn" class="btn btn-primary" onclick="updateEmpresa()">Guardar cambios</button>
            </div>
        </div>
    </div>
</div>
                                
    <script>
        function openModalAddPuesto() {
            $('#EmpresaModal').modal('show');
        }

        var newEmpresaId = null;

        function addPuesto() {
            if (!newEmpresaId) {
                var empresaData = {
                    "id_nombre": $("#id_nombre").val(),
                    "id_comentarios": $("#id_comentarios").val(),
                    "id_cantidad": $("#id_cantidad").val()
                };
                
                $.ajax({
                    url: "./?action=empresas/add",
                    type: 'POST',
                    data: empresaData,
                    success: function(response) {
                        newEmpresaId = response;
                        alert("Puesto agregado correctamente");
                        window.location = "index.php?view=empresas/index";
                    },
                    error: function() {
                        alert("Algo salió mal");
                    }
                });
            } else {
                alert("Este personal ya existe.");
            }
        }
        function editEmpresa(id) {
    $.ajax({
        url: "./?action=empresas/get&id=" + id, // Asegúrate de que esta URL sea correcta
        type: "GET",
        dataType: "json", // Asegura que el JSON se procese correctamente
        success: function(response) {
            try {
                // Verificar que los datos sean válidos antes de asignarlos
                if (response && response.id && response.nombre && response.comentarios !== undefined && response.id_cantidad !== undefined) {
                    $('#edit_id').val(response.id);
                    $('#edit_id_nombre').val(response.nombre);
                    $('#edit_id_comentarios').val(response.comentarios);
                    $('#edit_id_cantidad').val(response.id_cantidad);

                    $('#EditEmpresaModal').modal('show'); // Mostrar el modal
                } else {
                    alert("Error: No se pudieron cargar los datos de la empresa.");
                }
            } catch (e) {
                alert("Error inesperado al procesar los datos.");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en la petición AJAX:", error);
            alert("Hubo un error al obtener los datos de la empresa.");
        }
    });
}
function updateEmpresa() {
    var empresaData = {
        id: $("#edit_id").val(),
        id_nombre: $("#edit_id_nombre").val(),
        id_comentarios: $("#edit_id_comentarios").val(),
        id_cantidad: $("#edit_id_cantidad").val()
    };

    // 🔹 Verificar si los campos están vacíos antes de enviar la petición
    if (!empresaData.id || !empresaData.id_nombre || !empresaData.id_cantidad) {
        alert("Todos los campos son obligatorios.");
        return;
    }

    console.log("Enviando datos al servidor:", empresaData); // 🛠️ Debug 1: Mostrar datos antes de enviarlos

    $.ajax({
        url: "./?action=empresas/update", // 🔹 Verifica que esta URL sea correcta
        type: "POST",
        data: empresaData,
        success: function(response) {
            console.log("Respuesta del servidor:", response); // 🛠️ Debug 2: Ver respuesta del backend

            try {
                if (response.trim() === "success") {
                    alert("✅ Empresa actualizada correctamente.");
                    $('#EditEmpresaModal').modal('hide'); // 🔹 Cerrar el modal
                    location.reload(); // 🔹 Recargar la página para ver los cambios
                } else {
                    alert("❌ Error: No se pudo actualizar la empresa. Respuesta del servidor: " + response);
                }
            } catch (e) {
                console.error("❌ Error inesperado al procesar la respuesta:", e);
                alert("⚠️ Error inesperado al procesar la respuesta.");
            }
        },
        error: function(xhr, status, error) {
            console.error("❌ Error en la petición AJAX:", status, error, xhr.responseText); // 🛠️ Debug 3: Mostrar error detallado
            alert("⚠️ Hubo un error al actualizar los datos de la empresa.");
        }
    });
}



function deleteEmpresa(id, nombre) {
    const swalWithBootstrapButtons = Swal.mixin({
        buttonsStyling: true
    });

    swalWithBootstrapButtons.fire({
        title: '¿Estás seguro de eliminar la empresa ' + nombre + '?',
        text: "¡No podrás revertirlo!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminarlo',
        cancelButtonText: '¡No, cancelarlo!',
        reverseButtons: true
    }).then((result) => {
        if (result.value === true) {
            window.location.href = "index.php?action=empresas/delete&id=" + id + "&nombre=" + nombre;
        }
    });
}

    </script>

</body>
</html>
