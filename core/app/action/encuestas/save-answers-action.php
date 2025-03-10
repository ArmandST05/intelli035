<?php

if (!isset($_SESSION['typeUser']) || $_SESSION['typeUser'] !== 'e') {
    die("Acceso denegado.");
}

// Verificar si se recibió el formulario correctamente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['survey_id'], $_POST['answers'])) {
    $survey_id = intval($_POST['survey_id']);
    $personal_id = $_SESSION['user_id']; // ID del empleado autenticado
    $answers = $_POST['answers'];

    // Guardar las respuestas
    foreach ($answers as $question_id => $answer) {
        $question_id = intval($question_id);
        $answer = intval($answer);
        $sql = "INSERT INTO survey_answers (personal_id, survey_id, question_id, response, answered_at)
                VALUES ($personal_id, $survey_id, $question_id, $answer, NOW())";
        Executor::doit($sql);
    }

    // Marcar la encuesta como completada
    $sql = "UPDATE personal_surveys SET completed = 1 WHERE personal_id = $personal_id AND survey_id = $survey_id";
    Executor::doit($sql);

    echo "Respuestas guardadas exitosamente.";
    header("Location: index.php?view=home-personal"); 
    exit;
} else {
    die("Datos insuficientes o método no permitido.");
}
?>
