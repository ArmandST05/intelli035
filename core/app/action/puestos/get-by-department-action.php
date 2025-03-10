<?php

if (isset($_GET['department_id']) && is_numeric($_GET['department_id'])) {
    $department_id = intval($_GET['department_id']);

    // Obtener los puestos asociados a este departamento
    $puestos = PuestoData::getByDepartment($department_id);

    if (!empty($puestos)) {
        // Preparar los datos para devolverlos en formato JSON
        $response = [
            'status' => 'success',
            'data' => []
        ];

        foreach ($puestos as $puesto) {
            $response['data'][] = [
                'id' => $puesto->id,
                'nombre' => $puesto->nombre
            ];
        }

        // Devolver la respuesta en formato JSON
        echo json_encode($response);
    } else {
        // Si no se encuentran puestos, devolver un error
        echo json_encode(['status' => 'error', 'message' => 'No se encontraron puestos para este departamento.']);
    }
} else {
    // Si no se recibe el ID del departamento
    echo json_encode(['status' => 'error', 'message' => 'ID de departamento no válido.']);
}
?>
