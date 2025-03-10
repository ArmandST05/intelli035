<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['employeeId']) && isset($_POST['surveys'])) {
        $personalId = intval($_POST['employeeId']);
        $surveyIds = array_map('intval', $_POST['surveys']);

        // Obtener las encuestas que ya tiene asignadas el empleado y que aún no ha completado
        $existingSurveys = EncuestaData::getAssignedSurveys($personalId);

        // Filtrar encuestas ya asignadas
        $assignedSurveyIds = array_column($existingSurveys, 'id');   // IDs de encuestas ya asignadas
        $assignedSurveyTitles = array_column($existingSurveys, 'title'); // Nombres de encuestas ya asignadas

        // Verificar si alguna de las encuestas ya está asignada
        $alreadyAssigned = array_intersect($surveyIds, $assignedSurveyIds);

        if (!empty($alreadyAssigned)) {
            // Obtener los nombres de las encuestas ya asignadas
            $assignedTitles = array_map(function($id) use ($existingSurveys) {
                foreach ($existingSurveys as $survey) {
                    if ($survey->id == $id) {
                        return $survey->title;
                    }
                }
            }, $alreadyAssigned);

            echo json_encode([
                "status" => "error",
                "message" => "Esta persona ya tiene asignada la(s) encuesta(s): " . implode(", ", $assignedTitles)
            ]);
        } else {
            // Asignar encuestas al empleado
            $result = EncuestaData::assignToPersonal($personalId, $surveyIds);

            if ($result) {
                echo json_encode(["status" => "success", "message" => "Encuestas asignadas correctamente."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error al asignar encuestas."]);
            }
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Datos insuficientes."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}

// Depuración
file_put_contents('debug.txt', print_r($_POST, true), FILE_APPEND);
?>
