<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Leer el JSON crudo del cuerpo de la solicitud
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || !isset($data['users'])) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos o no enviados.']);
    http_response_code(400);
    exit;
}

$results = [];

foreach ($data['users'] as $user) {
    $userId = isset($user['id']) ? intval($user['id']) : 0;
    if ($userId === 0) {
        $results[] = [
            'id' => 0,
            'success' => false,
            'message' => 'ID inválido o no enviado.'
        ];
        continue;
    }

    // Obtener datos del usuario
    $credentials = PersonalData::getById($userId);

    if (!$credentials) {
        $results[] = [
            'id' => $userId,
            'success' => false,
            'message' => 'Usuario no encontrado'
        ];
        continue;
    }

    // Validar datos necesarios
    if (empty($credentials->telefono) || empty($credentials->usuario) || empty($credentials->clave)) {
        $results[] = [
            'id' => $userId,
            'success' => false,
            'message' => 'Faltan datos para enviar'
        ];
        continue;
    }

    // Limpiar teléfono
    $telefono = preg_replace('/\D/', '', $credentials->telefono);

    // Usar nombre si está disponible, si no, usar "Usuario"
    $nombre = !empty($credentials->nombre) ? $credentials->nombre : "Usuario";

    // Construir mensaje
    $mensaje = "Hola *{$nombre}*!\nTus credenciales son:\nUsuario: {$credentials->usuario}\nClave: {$credentials->clave}\n\nVisita: https://intelli035.v2technoconsulting.com";

    // Codificar mensaje para URL
    $mensaje_url = urlencode($mensaje);

    // Generar link de WhatsApp
    $linkWhatsApp = "https://wa.me/{$telefono}?text={$mensaje_url}";

    $results[] = [
        'id' => $userId,
        'success' => true,
        'message' => 'Link generado',
        'telefono' => $telefono,
        'link' => $linkWhatsApp
    ];
}

// Devolver respuesta final
echo json_encode([
    'success' => true,
    'results' => $results
]);
