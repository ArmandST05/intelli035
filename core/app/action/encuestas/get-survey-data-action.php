<?php
if (!isset($_GET['survey_id']) || !is_numeric($_GET['survey_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID de encuesta no válido']);
    exit;
}

$survey_id = intval($_GET['survey_id']);

try {
    if ($survey_id == 1) {
        // Consulta para encuesta 1 (Sí/No)
        $sql = "SELECT response, COUNT(*) as count 
                FROM survey_answers 
                WHERE survey_id = 1 
                GROUP BY response";

        $labels = ['No', 'Sí']; // Etiquetas para las respuestas binarias
        $counts = [0, 0]; // Inicializar contadores para "No" y "Sí"

        $query = Executor::doit($sql);
        $results = $query[0]->fetch_all(MYSQLI_ASSOC);

        foreach ($results as $row) {
            if ($row['response'] == 0) {
                $counts[0] = intval($row['count']); // Contador para "No"
            } elseif ($row['response'] == 1) {
                $counts[1] = intval($row['count']); // Contador para "Sí"
            }
        }
    } elseif ($survey_id == 2) {
        // Consulta para encuesta 2 (escalas de frecuencia)
        $sql = "SELECT response, COUNT(*) as count 
                FROM survey_answers 
                WHERE survey_id = 2 
                GROUP BY response";

        $labels = ['Siempre', 'Casi siempre', 'Algunas veces', 'Casi nunca', 'Nunca'];
        $counts = array_fill(0, count($labels), 0); // Inicializar contadores en 0

        $query = Executor::doit($sql);
        $results = $query[0]->fetch_all(MYSQLI_ASSOC);

        foreach ($results as $row) {
            $responseIndex = array_search($row['response'], $labels);
            if ($responseIndex !== false) {
                $counts[$responseIndex] = intval($row['count']);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Encuesta no reconocida']);
        exit;
    }

    // Responder con JSON
    echo json_encode(['status' => 'success', 'data' => ['labels' => $labels, 'counts' => $counts]]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al obtener datos: ' . $e->getMessage()]);
    exit;
}
?>
