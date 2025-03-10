<?php

// Verificar si se recibió la solicitud correcta
if (isset($_GET['encuesta_id']) && isset($_GET['personal_id'])) {
    $encuesta_id = $_GET['encuesta_id'];
    $personal_id = $_GET['personal_id'];

    // Obtener las preguntas de la encuesta
    $preguntas_sql = "SELECT id, question_text FROM survey_questions WHERE survey_id = $encuesta_id";
    $preguntas_result = Executor::doit($preguntas_sql);
    $preguntas = [];
    if ($preguntas_result && $preguntas_result[0]) {
        while ($row = $preguntas_result[0]->fetch_assoc()) {
            $preguntas[$row['id']] = $row['question_text'];
        }
    }

    // Obtener las respuestas del empleado para la encuesta
    $respuestas_sql = "SELECT question_id, response FROM survey_answers WHERE personal_id = $personal_id AND survey_id = $encuesta_id";
    $respuestas_result = Executor::doit($respuestas_sql);
    $respuestas = [];
    if ($respuestas_result && $respuestas_result[0]) {
        while ($row = $respuestas_result[0]->fetch_assoc()) {
            $respuestas[$row['question_id']] = $row['response']; // Guardamos la respuesta (1 = Sí, 0 = No)
        }
    }

    // Generar HTML con las preguntas y respuestas
    $html = "<thead>
            <tr>
                <th>Preguntas</th>
                <th>Sí</th>
                <th>No</th>
            </tr>
        </thead>
        <tbody>"; // Inicio del cuerpo de la tabla
    foreach ($preguntas as $id => $texto) {
        $respuesta = isset($respuestas[$id]) ? $respuestas[$id] : null;
        $checked_si = ($respuesta === "1") ? "checked" : "";
        $checked_no = ($respuesta === "0") ? "checked" : "";

        $html .= "
       
        <tr>
                    <td>{$texto}</td>
                    <td><input type='radio' name='respuesta_{$id}' value='1' disabled $checked_si></td>
                    <td><input type='radio' name='respuesta_{$id}' value='0' disabled $checked_no></td>
                  </tr>";
    }

    echo $html; // Devolver el HTML en lugar de JSON
    exit;
}
?>
