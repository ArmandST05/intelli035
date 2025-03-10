<?php
// Obtener los datos del formulario
$survey_id = $_POST['survey_id']; // ID de la encuesta
$user_id = 5; // Ejemplo de ID del usuario, deberías obtener esto dinámicamente (por ejemplo, desde la sesión)

if (isset($_POST['answers'])) {
    $answers = $_POST['answers']; // Las respuestas enviadas

    // Guardar las respuestas en la base de datos
    foreach ($answers as $question_id => $answer) {
        EncuestaData::savePsychosocialRiskAnswer($survey_id, $user_id, $question_id, $answer);
    }

    echo "Respuestas guardadas con éxito.";
} else {
    echo "No se enviaron respuestas.";
}
?>
