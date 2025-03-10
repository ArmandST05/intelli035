<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    // Obtener la configuración
    $configuration = ConfigurationData::getAll();
    $personal = PersonalData::getById($userId);

    if (!$personal) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    // Validar correo del remitente
    $fromEmail = $configuration['email']->value;
    if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Dirección de correo inválida en la configuración.']);
        exit;
    }

    // Configurar PHPMailer para usar SMTP
    $mail = new PHPMailer(true);  // Usamos true para habilitar excepciones

    try {
        // Configuración SMTP
        $mail->isSMTP();  // Habilitar el envío a través de SMTP
        $mail->Host = 'mail.v2technoconsulting.com';  // Cambia al servidor SMTP de tu dominio
        $mail->SMTPAuth = true;  // Habilitar autenticación SMTP
        $mail->Username = 'armando_suarez@v2technoconsulting.com';  // Tu correo electrónico
        $mail->Password = '=oetE(u5{%-?';  // La contraseña de tu correo electrónico
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Seguridad TLS
        $mail->Port = 587;  // Puerto para TLS (587 es el estándar)

        // De, a, asunto, etc.
        $mail->setFrom($fromEmail, $configuration['name']->value);  // Remitente
        $mail->addAddress($personal->correo);  // Destinatario
        $mail->Subject = "Tus credenciales de acceso";
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);  // Para enviar el correo en formato HTML

        // Contenido del correo
        $mail->Body = "
            <p>Hola <b>{$personal->nombre}</b>,</p>
            <p>Estas son tus credenciales de acceso:</p>
            <ul>
                <li><b>Usuario:</b> {$personal->usuario}</li>
                <li><b>Clave:</b> {$personal->clave}</li>
                <br>
                <li><a>https://intelli035.v2technoconsulting.com</a></li>
            </ul>
            <p>Por favor, guarda esta información de manera segura.</p>
            <p>Saludos cordiales,<br>Equipo de {$configuration['name']->value}</p>
        ";

        // Enviar correo
        if ($mail->send()) {
            // Registrar notificación en el sistema
            $notification = new NotificationData();
            $notification->personal_id = $personal->id;
            $notification->type_id = 1; // Correo
            $notification->status_id = 1; // Enviado
            $notification->message = $mail->Body;
            $notification->receptor = $personal->correo;
            $notification->add();

            echo json_encode(['success' => true, 'message' => 'Correo enviado exitosamente.']);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo enviar el correo.',
            'error' => $mail->ErrorInfo
        ]);
    }
    
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}
