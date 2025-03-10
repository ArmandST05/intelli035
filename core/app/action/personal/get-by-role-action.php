<?php

if (isset($_GET['role_id']) && is_numeric($_GET['role_id'])) {
    $role_id = intval($_GET['role_id']);

    // Obtener el personal asociado a este puesto
    $personal = PersonalData::getByRole($role_id); // Función para obtener los empleados según el puesto

    if (!empty($personal)) {
        // Preparar la respuesta
        $response = [
            'status' => 'success',
            'data' => []
        ];

        foreach ($personal as $empleado) {
            $response['data'][] = [
                'id' => $empleado->id, // ID del empleado
                'nombre' => $empleado->nombre // Nombre del empleado
            ];
        }

        // Devolver la respuesta en formato JSON
        echo json_encode($response);
    } else {
        // Si no se encuentran empleados, devolver un error
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron empleados para este puesto.']);
    }
} else {
    // Si no se recibe el ID del puesto
    echo json_encode(['status' => 'error', 'message' => 'ID de puesto no válido.']);
}
?>
