<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['selectedEmployees']) && isset($_POST['surveys'])) {
        // Convertir la cadena de IDs a un array de enteros
        $employeeIds = array_filter(array_map('intval', explode(',', $_POST['selectedEmployees'])));
        $surveyIds = array_map('intval', $_POST['surveys']);

        $alreadyAssignedList = [];

        foreach ($employeeIds as $personalId) {
            // Obtener encuestas ya asignadas a esta persona
            $existingSurveys = EncuestaData::getAssignedSurveys($personalId);
            $assignedSurveyIds = array_column($existingSurveys, 'id');

            // Verificar si alguna ya está asignada
            $alreadyAssigned = array_intersect($surveyIds, $assignedSurveyIds);

            if (!empty($alreadyAssigned)) {
                $assignedTitles = array_map(function ($id) use ($existingSurveys) {
                    foreach ($existingSurveys as $survey) {
                        if ($survey->id == $id) {
                            return $survey->title;
                        }
                    }
                }, $alreadyAssigned);

                $alreadyAssignedList[] = "Empleado ID {$personalId}: " . implode(", ", $assignedTitles);
            }
        }

        if (!empty($alreadyAssignedList)) {
            echo json_encode([
                "status" => "error",
                "message" => "Algunas encuestas ya están asignadas:\n" . implode("\n", $alreadyAssignedList)
            ]);
        } else {
            // Asignar encuestas a todos los empleados
            foreach ($employeeIds as $personalId) {
EncuestaData::assignToPersonal($personalId, $surveyIds);
            }

            echo json_encode(["status" => "success", "message" => "Encuestas asignadas correctamente."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Datos insuficientes."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Método no permitido."]);
}

// (Opcional) Para depurar qué datos llegan
file_put_contents('debug_multiple.txt', print_r($_POST, true), FILE_APPEND);
