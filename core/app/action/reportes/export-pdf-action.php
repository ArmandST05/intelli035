<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';

$conn = Database::getCon(); // Conexión a la BD

// Verificar si se recibió la solicitud correcta
if (isset($_GET['encuesta_id']) && isset($_GET['personal_id'])) {
    $encuesta_id = $_GET['encuesta_id'];
    $personal_id = $_GET['personal_id'];
    
    
    // Obtener el nombre de la encuesta
    $encuesta_sql = "SELECT title FROM surveys WHERE id = $encuesta_id";
    $encuesta_result = Executor::doit($encuesta_sql);
    $encuesta_nombre = "Encuesta"; // Valor por defecto en caso de error
    if ($encuesta_result && $encuesta_result[0]) {
        $row = $encuesta_result[0]->fetch_assoc();
        $encuesta_nombre = $row['title'];
    }
    
    // Obtener el nombre del empleado y su empresa
    $empleado_sql = "SELECT personal.nombre, empresas.logo 
                 FROM personal 
                 INNER JOIN empresas ON personal.empresa_id = empresas.id 
                 WHERE personal.id = $personal_id";
    
    $empleado_result = Executor::doit($empleado_sql);
    
    $empleado_nombre = "Empleado"; // Valor por defecto
    $logo_empleado = "";
    
    if ($empleado_result && $empleado_result[0]) {
        $row = $empleado_result[0]->fetch_assoc();
        $empleado_nombre = $row['nombre'];
        $logo_empleado = $row['logo']; // Guardamos el logo de la empresa
    }
    
    
    $nombre_archivo = "Reporte_" . str_replace(" ", "_", $encuesta_nombre) . "_" . str_replace(" ", "_", $empleado_nombre) . ".pdf";
    
    // Definir el HTML con estilos
    $html = "<style>
body {
    font-family: Arial, sans-serif;
    font-size: medium;
}
.header {
    text-align: center;
    margin-bottom: 20px;
}
.header img {
    width: 150px;
    height: auto;
    display: block;
    margin: 0 auto;
}
h2 {
    text-align: center;
}
</style>";
    
    // Obtener la ruta absoluta del logo
    $ruta_logo = __DIR__ . "/../../../../" . $logo_empleado;
    
    // Agregar el encabezado con el logo y el nombre del empleado
    $html .= "<div class='header'>";
    
    // Intentar mostrar el logo si existe
    if (file_exists($ruta_logo)) {
        $html .= "<img src='$ruta_logo' alt='Logo de la empresa'>";
    } else {
        $html .= "<p style='color: red;'>Logo no encontrado</p>";
    }
    
    
    // Obtener preguntas y respuestas según el tipo de encuesta
    if ($encuesta_id == 1) {
        // Obtener las preguntas de la encuesta 1
        $preguntas_sql = "SELECT id, question_text FROM survey_questions WHERE survey_id = $encuesta_id";
        $preguntas_result = Executor::doit($preguntas_sql);
        $preguntas = [];
        if ($preguntas_result && $preguntas_result[0]) {
            while ($row = $preguntas_result[0]->fetch_assoc()) {
                $preguntas[$row['id']] = $row['question_text'];
            }
        }
        
        // Obtener las respuestas del empleado para la encuesta 1
        $respuestas_sql = "SELECT question_id, response FROM survey_answers WHERE personal_id = $personal_id AND survey_id = $encuesta_id";
        $respuestas_result = Executor::doit($respuestas_sql);
        $respuestas = [];
        if ($respuestas_result && $respuestas_result[0]) {
            while ($row = $respuestas_result[0]->fetch_assoc()) {
                $respuestas[$row['question_id']] = $row['response'];
            }
        }
        
        // Generar la tabla de preguntas y respuestas
        $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
            <thead>
                <tr>
                    <th>Preguntas</th>
                    <th>Sí</th>
                    <th>No</th>
                </tr>
            </thead>
            <tbody>";
        
        foreach ($preguntas as $id => $texto) {
            $respuesta = isset($respuestas[$id]) ? $respuestas[$id] : null;
            $checked_si = ($respuesta == "1") ? "✔" : "";
            $checked_no = ($respuesta == "0") ? "✔" : "";
            
            $html .= "<tr>
                <td>{$texto}</td>
                <td style='text-align: center; font-size: 16px;'>$checked_si</td>
                <td style='text-align: center; font-size: 16px;'>$checked_no</td>
              </tr>";
        }
        
        $html .= "</tbody></table>"; // Cerrar la tabla
        
        
        
    } elseif ($encuesta_id == 2 || $encuesta_id == 3) {
        // Obtener las preguntas de la encuesta
        $preguntas_sql = "SELECT id, text FROM psychosocial_risk_questions WHERE survey_id = $encuesta_id";
        $preguntas_result = Executor::doit($preguntas_sql);
        $preguntas = [];
        if ($preguntas_result && $preguntas_result[0]) {
            while ($row = $preguntas_result[0]->fetch_assoc()) {
                $preguntas[] = $row;
            }
        }
        
        // Obtener las respuestas del empleado
        $respuestas_sql = "SELECT question_id, response FROM survey_answers WHERE personal_id = $personal_id AND survey_id = $encuesta_id";
        $respuestas_result = Executor::doit($respuestas_sql);
        $respuestas = [];
        if ($respuestas_result && $respuestas_result[0]) {
            while ($row = $respuestas_result[0]->fetch_assoc()) {
                $respuestas[] = $row;
            }
        }
        
        // Opciones de respuesta
        $opciones = [
            'Siempre' => 1,
            'Casi siempre' => 2,
            'Algunas veces' => 3,
            'Casi nunca' => 4,
            'Nunca' => 5
        ];
        
        // Construcción del HTML de la tabla
        // Construcción del HTML de la tabla
        $tablaHTML = '
<table border="1" cellpadding="5" cellspacing="0" width="100%">
<thead>
    <tr>
        <th>Pregunta</th>
        <th>Siempre</th>
        <th>Casi siempre</th>
        <th>Algunas veces</th>
        <th>Casi nunca</th>
        <th>Nunca</th>
    </tr>
</thead>
<tbody>';
        
        foreach ($preguntas as $pregunta) {
            $respuestaSeleccionada = null;
            foreach ($respuestas as $respuesta) {
                if ($respuesta['question_id'] == $pregunta['id']) {
                    $respuestaSeleccionada = $respuesta['response']; // Guardar el texto de la respuesta
                    break;
                }
            }
            
            // Agregar fila con respuestas marcadas correctamente
            $filaHTML = '<tr><td>' . htmlspecialchars($pregunta['text']) . '</td>';
            foreach ($opciones as $opcion => $valor) {
                $marcado = ($respuestaSeleccionada === $opcion) ? '✔' : ''; // Comparar con el texto, no con el número
                $filaHTML .= '<td style="text-align: center;">' . $marcado . '</td>';
            }
            $filaHTML .= '</tr>';
            $tablaHTML .= $filaHTML;
        }
        
        
        $tablaHTML .= '</tbody></table>';
        // ✅ Agregar la tabla al HTML antes de generar el PDF
        $html .= $tablaHTML;
    }
    
    $mpdf = new \Mpdf\Mpdf();
    
    // Agregar título al PDF
    $mpdf->WriteHTML('<h4>Resultados de la Encuesta ' . $empleado_nombre . '</h4>');
    
    // Agregar contenido HTML
    $mpdf->WriteHTML($html);
    
    // Generar y descargar el PDF
    $mpdf->Output($nombre_archivo, 'D');
    exit;
}  