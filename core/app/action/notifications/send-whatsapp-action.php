<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Obtener el ID del usuario
    $userId = intval($_POST['id']);

    // Obtener los datos del usuario desde la tabla 'personal'
    $credentials = PersonalData::getById($userId);

    // Depuración para verificar los datos obtenidos
    if (!$credentials) {
        echo json_encode(['success' => false, 'message' => 'No se encontraron datos para este usuario.']);
        http_response_code(400);
        exit;
    }

    // Verifica si los campos necesarios existen en los datos obtenidos
    if (empty($credentials->telefono) || empty($credentials->usuario) || empty($credentials->clave)) {
        echo json_encode(['success' => false, 'message' => 'El teléfono, usuario o clave no están disponibles para este usuario.']);
        http_response_code(400);
        exit;
    }

    // Responder con los datos del usuario y agregar la URL
    echo json_encode([
        'success' => true,
        'message' => 'Credenciales obtenidas correctamente. Visita el siguiente enlace para más información: <a href="https://intelli035.v2technoconsulting.com" target="_blank">https://intelli035.v2technoconsulting.com</a>',
        'telefono' => $credentials->telefono,
        'usuario' => $credentials->usuario,  // Suponiendo que el campo es 'usuario'
        'clave' => $credentials->clave       // Suponiendo que el campo es 'clave'
    ]);
    http_response_code(200);
} else {
    echo json_encode(['success' => false, 'message' => 'Parámetros incompletos.']);
    http_response_code(400);
}
?>
