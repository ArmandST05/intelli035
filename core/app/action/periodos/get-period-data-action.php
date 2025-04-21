<?php

// Verificar si el ID está presente en la URL y no está vacío
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Obtener el ID como un número entero
    $period_id = intval($_GET['id']); 

    // Intentar obtener el periodo desde la base de datos usando el ID
    $periodo = PeriodoData::getById($period_id);

    if ($periodo) {
        // Si se encuentra el periodo, devolver la información en formato JSON
        echo json_encode([
            "status" => "success", // Indicar que la operación fue exitosa
            "data" => [
                "id" => $periodo->id,
                "name" => $periodo->name,
                "start_date" => $periodo->start_date,
                "end_date" => $periodo->end_date,
                "status" => $periodo->status,
                "empresa_id" => $periodo->empresa_id // Incluir más datos si es necesario
            ]
        ]);
    } else {
        // Si no se encuentra el periodo, devolver mensaje de error
        echo json_encode(["status" => "error", "message" => "No se encontró el registro."]);
    }
} else {
    // Si el ID no está presente o es inválido, devolver mensaje de error
    echo json_encode(["status" => "error", "message" => "ID no válido."]);
}
?>
