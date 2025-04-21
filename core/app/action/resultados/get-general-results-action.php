<?php

// Verificar si se recibió la solicitud correcta
if (isset($_GET['personal_id']) && isset($_GET['survey_id'])) {
    $personal_id = $_GET['personal_id'];
    $survey_id = $_GET['survey_id'];

    // Consulta para obtener el conteo de respuestas completadas
    $sql = "
        SELECT COUNT(*) as total
        FROM survey_answers
        INNER JOIN personal_surveys 
        ON survey_answers.personal_id = personal_surveys.personal_id
        WHERE personal_surveys.completed = 1
          AND survey_answers.personal_id = $personal_id
          AND survey_answers.survey_id = $survey_id
    ";

    // Ejecutar la consulta
    $result = Executor::doit($sql);

    $count = 0;
    if ($result && $result[0]) {
        $row = $result[0]->fetch_assoc();
        $count = $row['total']; // Extraer el conteo desde el resultado
    }

   // Verificar el valor y generar el mensaje
$mensaje = "";
if ($count >= 90) {
    $mensaje = "El resultado es Muy Alto.";
} elseif ($count >= 70 && $count < 90) {
    $mensaje = "El resultado es Alto.";
} elseif ($count >= 45 && $count < 70) {
    $mensaje = "El resultado es Medio.";
} elseif ($count >= 20 && $count < 45) {
    $mensaje = "El resultado es Bajo.";
} elseif ($count < 20) {
    $mensaje = "El resultado es Nulo.";
}

    // Devolver el mensaje como JSON
    echo json_encode(['mensaje' => $mensaje]);
    exit;
}
?>
