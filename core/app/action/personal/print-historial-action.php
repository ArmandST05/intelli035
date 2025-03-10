<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';


// Obtener los datos de encuestas asignadas
$datos = EncuestaData::getAllSurveyStatuses();

// Agrupar los datos en una estructura organizada
$empleados = [];
$encuestas = [];

foreach ($datos as $dato) {
    $empleados[$dato->personal_id]['nombre'] = $dato->nombre;
    $empleados[$dato->personal_id]['encuestas'][$dato->survey_id] = $dato->completed == 1 ? 'Terminado' : 'Pendiente';
    
    if (!isset($encuestas[$dato->survey_id])) {
        $encuestas[$dato->survey_id] = $dato->title;
    }
}

// Inicializar mPDF
$mpdf = new \Mpdf\Mpdf();
$html = "<h2 style='text-align:center;'>Lista de Encuestas Asignadas</h2>";
$html .= "<table border='1' width='100%' cellspacing='0' cellpadding='5' style='border-collapse: collapse; text-align: center;'>";
$html .= "<thead><tr style='background-color: #f2f2f2;'><th>Empleado</th>";
$html .= "<style> 

body{
font-family: arial;
}
    

</style>";
// Agregar encabezados de encuestas
foreach ($encuestas as $title) {
    $html .= "<th>$title</th>";
}

$html .= "</tr></thead><tbody>";

// Agregar filas de empleados
foreach ($empleados as $empleado) {
    $html .= "<tr><td>{$empleado['nombre']}</td>";
    foreach ($encuestas as $surveyId => $title) {
        $estado = isset($empleado['encuestas'][$surveyId]) ? $empleado['encuestas'][$surveyId] : 'Pendiente';
        $html .= "<td>$estado</td>";
    }
    $html .= "</tr>";
}

$html .= "</tbody></table>";

// Agregar contenido a mPDF y generar salida
$mpdf->WriteHTML($html);
$mpdf->Output("Lista_Encuestas.pdf", "I"); // Muestra el PDF en el navegador
