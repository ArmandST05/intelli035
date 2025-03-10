<?php
// Obtener los datos de encuestas asignadas con su estado
$datos = EncuestaData::getAllSurveyStatuses();

// Agrupar los datos en un formato más estructurado
$empleados = [];
$encuestas = [];

foreach ($datos as $dato) {
    $empleados[$dato->personal_id]['nombre'] = $dato->nombre;
    $empleados[$dato->personal_id]['encuestas'][$dato->survey_id] = $dato->completed == 1 ? 'Terminado' : 'Pendiente';
    
    // Guardamos las encuestas únicas para el encabezado de la tabla
    if (!isset($encuestas[$dato->survey_id])) {
        $encuestas[$dato->survey_id] = $dato->title;
    }
}
?>
<button type="button" class="btn btn-primary" onclick="printPersonal()">Imprimir lista</button>

<table border="2" class="table table-bordered"> 
    <thead>
        <tr>
            <th>Empleado</th>
            <?php foreach ($encuestas as $surveyId => $title): ?>
                <th><?php echo htmlspecialchars($title); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?php echo htmlspecialchars($empleado['nombre']); ?></td>
                <?php foreach ($encuestas as $surveyId => $title): ?>
                    <td>
                        <?php 
                            echo isset($empleado['encuestas'][$surveyId]) 
                                ? $empleado['encuestas'][$surveyId] 
                                : 'Pendiente'; 
                        ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<script>
function printPersonal() {
    let department_filter = $('#department_filter').val();
    let custom_search = $('#custom_search').val();

    $.ajax({
        url: "./?action=personal/print-historial",  // URL proporcionada
        type: "POST",
        data: {
            department_filter: department_filter,
            custom_search: custom_search
        },
        success: function(response) {
            // Redirigir para descargar el PDF
            window.location.href = "./?action=personal/print-historial&download=1&department_filter=" + department_filter + "&custom_search=" + custom_search;
        },
        error: function(xhr, status, error) {
            console.error("Error al generar el PDF:", error);
        }
    });
}



</script>
