<?php

// Verificar si se recibió la solicitud correcta
if (isset($_GET['encuesta_id']) && isset($_GET['personal_id'])) {
    $encuesta_id = $_GET['encuesta_id'];
    $personal_id = $_GET['personal_id'];

    // Verificar que la encuesta sea válida para este caso
    if ($encuesta_id == 2 || $encuesta_id == 3) {
        // Obtener las preguntas de la encuesta
        $preguntas_sql = "SELECT id, text FROM psychosocial_risk_questions WHERE survey_id = $encuesta_id";
        $preguntas_result = Executor::doit($preguntas_sql);
        $preguntas = [];
        if ($preguntas_result && $preguntas_result[0]) {
            while ($row = $preguntas_result[0]->fetch_assoc()) {
                $preguntas[] = $row;
            }
        }

        // Obtener las respuestas del empleado para la encuesta
        $respuestas_sql = "SELECT question_id, response FROM survey_answers WHERE personal_id = $personal_id AND survey_id = $encuesta_id";
        $respuestas_result = Executor::doit($respuestas_sql);
        $respuestas = [];
        if ($respuestas_result && $respuestas_result[0]) {
            while ($row = $respuestas_result[0]->fetch_assoc()) {
                $respuestas[] = $row;
            }
        }

        // Opciones de respuesta (para encuestas de tipo 2 y 3)
        $opciones = [
            'Siempre' => 1,
            'Casi siempre' => 2,
            'Algunas veces' => 3,
            'Casi nunca' => 4,
            'Nunca' => 5
        ];

        // Comenzar a construir el HTML de la tabla
        $tablaHTML = '
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

        // Generar las filas de las preguntas
        foreach ($preguntas as $pregunta) {
            // Buscar la respuesta del empleado para la pregunta actual
            $respuestaSeleccionada = null;
            foreach ($respuestas as $respuesta) {
                if ($respuesta['question_id'] == $pregunta['id']) {
                    $respuestaSeleccionada = $respuesta['response'];
                    break;
                }
            }

            // Crear las celdas de los radio buttons
            $filaHTML = '<tr><td>' . htmlspecialchars($pregunta['text']) . '</td>';

            foreach ($opciones as $opcion => $valor) {
                $checked = ($respuestaSeleccionada == $opcion) ? 'checked' : '';
                $filaHTML .= '<td><input disabled type="radio" name="pregunta_' . $pregunta['id'] . '" value="' . $opcion . '" ' . $checked . '></td>';
            }

            $filaHTML .= '</tr>';
            $tablaHTML .= $filaHTML;
        }

        $tablaHTML .= '</tbody>';

        // Imprimir la tabla completa
        echo $tablaHTML;
        exit;

    } else {
        echo 'Encuesta no válida para este tipo de acción.';
        exit;
    }
} else {
    echo 'Faltan parámetros necesarios.';
    exit;
}
?>
