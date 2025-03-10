<?php 
$departamentos = DepartamentoData::getAll();
?>
<div class="card" style="width: 100%; margin-top: 20px">
    <div class="card-body">
        <div class="row mb-3">
            <!-- Filtro por departamento -->
            <div class="col-md-4">
                <label for="filter-department">Departamento:</label>
                <select id="filter-department" class="form-control">
                    <option value="">Todos</option>
                    <!-- Opciones cargadas dinámicamente por AJAX -->
                </select>
            </div>

            <!-- Búsqueda personalizada -->
            <div class="col-md-4">
                <label for="custom-search">Buscar:</label>
                <input type="text" id="custom-search" class="form-control" placeholder="Buscar...">
            </div>

            <!-- Selector de cantidad de registros -->
            <div class="col-md-4">
                <label for="custom-length">Registros por página:</label>
                <select id="custom-length" class="form-control">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
    <br>
    <button type="button" class="btn btn-primary" id="btncorreo" onclick="sendMail()">Enviar credenciales por correo</button>
    <button type="button" class="btn btn-primary" id="btnWhatsapp">Enviar credenciales por Whatsapp</button>

    <br>
        <!-- Tabla para mostrar los resultados -->
        <table id="lookup" class="table table-striped table-hover">
            <thead style="background-color: #484848; color: white; border-radius: 5px;">
                <tr>
                <th><input type="checkbox" id="select-all"></th> <!-- Checkbox para seleccionar todos -->
                <th>#</th>
                <th>Nombre</th>
                <th>Departamento / Puesto</th>
                <th>Usuario</th>
                <th>Clave</th>
                <th>Correo</th>
                <th>Teléfono</th>
                </tr>
            </thead>
            <tbody>
                <!-- Gestionado dinámicamente por DataTables -->
            </tbody>
        </table>
    </div>
</div>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    var dataTable = $('#lookup').DataTable({
        "language": {
            "sProcessing": "Procesando...",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            }
        },
        "ordering": false,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "./?action=personal/get-all-credentials", // Endpoint del backend
            type: "POST",
            data: function(d) {
                // Añadir filtros dinámicos
                d.department_filter = $('#filter-department').val(); // Filtro por departamento
                d.custom_search = $('#custom-search').val(); // Búsqueda personalizada
                d.length = $('#custom-length').val(); // Tamaño de paginación
            },
            dataSrc: function(json) {
                if (json.data) {
                    return json.data; // DataTables espera un array en 'data'
                } else {
                    console.error("Respuesta inválida del servidor: ", json);
                    return [];
                }
            },
            error: function(xhr, error, code) {
                console.error("Error al cargar datos: ", error, code);
            }
        },
        "responsive": true,
        "scrollX": true,
        "dom": '<"datatable-content"t><"datatable-footer"ip>', // Diseño de tabla
    });

    // Cargar opciones de departamentos dinámicamente
    $.ajax({
        url: './?action=departamentos/get-all', // Endpoint para obtener departamentos
        method: 'GET',
        success: function(data) {
            var departmentSelect = $('#filter-department');
            data.forEach(function(department) {
                departmentSelect.append('<option value="' + department.id + '">' + department.nombre + '</option>');
            });
        },
        error: function(xhr, error, code) {
            console.error("Error al cargar departamentos: ", error, code);
        }
    });
});
function sendMail() {
    var selectedUsers = [];
    $('#lookup tbody input[type="checkbox"]:checked').each(function() {
        var rowData = dataTable.row($(this).closest('tr')).data();
        selectedUsers.push({
            name: rowData[1], // Nombre
            department: rowData[2], // Departamento / Puesto
            username: rowData[3], // Usuario
            password: rowData[4], // Clave
            email: rowData[5], // Correo
            phone: rowData[6], // Teléfono
        });
    });

    if (selectedUsers.length === 0) {
        alert('No hay usuarios seleccionados.');
        return;
    }

    $.ajax({
        url: './?action=notifications/send-massive-mail',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ users: selectedUsers }),
        success: function(response) {
            var res = JSON.parse(response);
            if (res.success) {
                alert('Correos enviados exitosamente.');
            } else {
                alert('Ocurrieron errores al enviar algunos correos:\n' + res.errors.join('\n'));
            }
        },
        error: function(xhr, error, code) {
            console.error('Error al enviar correos:', error, code);
            alert('Ocurrió un error al intentar enviar los correos.');
        }
    });
}


</script>
