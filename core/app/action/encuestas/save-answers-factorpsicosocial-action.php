<?php

if (!isset($_SESSION['typeUser']) || $_SESSION['typeUser'] !== 'e') {
    die("Acceso denegado.");
}

// Verificar si se recibió el formulario correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['survey_id'], $_POST['answers'])) {
    $survey_id = intval($_POST['survey_id']); // ID de la encuesta
    $personal_id = $_SESSION['user_id']; // ID del empleado autenticado
    $answers = $_POST['answers']; // Respuestas del formulario

    // Grupos para encuesta 2
    $group1_survey2 = [18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33];

    $group2_survey2 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46];
              

    // Grupos para encuesta 3 (ajustados con IDs del 48 al 115)
    $group1_survey3 = [
        48, 51, 70, 71, 72, 73, 74, 75, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89,
        90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111
    ];
    $group2_survey3 = [
        49, 50, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, 62, 63, 64, 65, 66, 67, 68, 69, 76, 101,
        112, 113, 114, 115, 116, 117, 118,119
    ];

    $valuesGroup1 = [
        'Siempre' => 0,
        'Casi siempre' => 1,
        'Algunas veces' => 2,
        'Casi nunca' => 3,
        'Nunca' => 4,
    ];

    $valuesGroup2 = [
        'Siempre' => 4,
        'Casi siempre' => 3,
        'Algunas veces' => 2,
        'Casi nunca' => 1,
        'Nunca' => 0,
    ];

    // Función para obtener el valor según el grupo y la encuesta
    function getValue($questionId, $response, $survey_id, $group1_survey2, $group2_survey2, $group1_survey3, $group2_survey3, $valuesGroup1, $valuesGroup2) {
        if ($survey_id === 2) {
            if (in_array($questionId, $group1_survey2)) {
                return $valuesGroup1[$response] ?? null;
            } elseif (in_array($questionId, $group2_survey2)) {
                return $valuesGroup2[$response] ?? null;
            }
        } elseif ($survey_id === 3) {
            if (in_array($questionId, $group1_survey3)) {
                return $valuesGroup1[$response] ?? null;
            } elseif (in_array($questionId, $group2_survey3)) {
                return $valuesGroup2[$response] ?? null;
            }
        }
        return null; // Manejo de preguntas fuera de los grupos
    }

    // Guardar las respuestas de la encuesta
    foreach ($answers as $question_id => $answer) {
        $question_id = intval($question_id);
        $answer = htmlspecialchars($answer); // Sanitizar el texto de la respuesta
        $value = getValue($question_id, $answer, $survey_id, $group1_survey2, $group2_survey2, $group1_survey3, $group2_survey3, $valuesGroup1, $valuesGroup2);

        // Validar que el valor sea válido antes de guardar
        if ($value === null) {
            die("Error: Respuesta inválida para la pregunta ID $question_id.");
        }

        $sql = "INSERT INTO survey_answers (personal_id, survey_id, question_id, response, valor, answered_at)
                VALUES ($personal_id, $survey_id, $question_id, '$answer', $value, NOW())";
        Executor::doit($sql);
    }

    error_log(print_r($answers, true));

    // Marcar la encuesta como completada
    $sql = "UPDATE personal_surveys SET completed = 1 WHERE personal_id = $personal_id AND survey_id = $survey_id";
    Executor::doit($sql);

    echo "Respuestas de la Encuesta $survey_id guardadas exitosamente.";
    header("Location: index.php?view=home-personal");
    exit;
} else {
    die("Datos insuficientes o método no permitido.");
}
?>
