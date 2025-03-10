<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $puestoId = isset($_POST['puestoId']) ? intval($_POST['puestoId']) : null;
    $surveyIds = isset($_POST['surveyIds']) ? $_POST['surveyIds'] : [];

    if (!$puestoId || empty($surveyIds)) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos: falta el ID del puesto o las encuestas."]);
        exit;
    }

    try {
        // Obtener la lista de empleados para el puesto
        $personalList = PersonalData::getPersonalByPuesto($puestoId);

        if (empty($personalList)) {
            http_response_code(404);
            echo json_encode(["error" => "No se encontró personal para el puesto especificado."]);
            exit;
        }

        // Asignar encuestas a cada empleado del puesto
        foreach ($personalList as $personal) {
            foreach ($surveyIds as $surveyId) {
                $sql = "INSERT INTO personal_surveys (personal_id, survey_id, completed, assigned_at)
                        VALUES ({$personal->id}, " . intval($surveyId) . ", 0, NOW())";
                Executor::doit($sql);
            }
        }

        // Respuesta de éxito
        http_response_code(200);
        echo json_encode(["success" => true, "message" => "Encuestas asignadas correctamente."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => "Error al asignar las encuestas: " . $e->getMessage()]);
    }
}
?>
