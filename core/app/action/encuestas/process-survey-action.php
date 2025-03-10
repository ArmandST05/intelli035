<?php

try {
    // Capturar datos del formulario
    $employeeId = isset($_POST['employeeId']) ? intval($_POST['employeeId']) : null;
    $surveyIds = isset($_POST['surveys']) ? $_POST['surveys'] : [];

    // Validar datos
    if (!$employeeId || empty($surveyIds)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
        exit;
    }

    // Asignar encuestas al empleado
    EncuestaData::assignSurveysToEmployee($employeeId, $surveyIds);

    // Respuesta exitosa
    echo json_encode(['status' => 'success', 'message' => 'Encuestas asignadas correctamente']);
} catch (Exception $e) {
    // Respuesta de error
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
