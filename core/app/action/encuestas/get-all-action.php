<?php
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $surveys = EncuestaData::getAll(); // Asume que EncuestaData es una clase que obtiene las encuestas de la base de datos

    if (!empty($surveys)) {
        $data = [];
        foreach ($surveys as $survey) {
            $data[] = [
                "id" => $survey->id,
                "title" => $survey->title,
                "description" => $survey->description // Agrega más campos si lo necesitas
            ];
        }
        // Devuelve los datos en formato JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se encontraron encuestas disponibles.']);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
