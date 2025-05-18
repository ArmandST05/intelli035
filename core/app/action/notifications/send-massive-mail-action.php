<?php
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_POST['id'];

    $configuration = ConfigurationData::getAll();
    $personal = PersonalData::getById($userId);

    if (!$personal) {
        http_response_code(404);
        error_log("Usuario no encontrado: ID $userId");
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado.']);
        exit;
    }

    if (empty($personal->correo) || !filter_var($personal->correo, FILTER_VALIDATE_EMAIL)) {
        error_log("Correo inválido para usuario ID $userId: " . $personal->correo);
        echo json_encode(['success' => false, 'message' => 'El usuario no tiene un correo válido registrado.']);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'mail.v2technoconsulting.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'armando_suarez@v2technoconsulting.com';
        $mail->Password = '=oetE(u5{%-?';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($configuration['email']->value, $configuration['name']->value);
        $mail->addAddress($personal->correo);
        $mail->Subject = "Tus credenciales de acceso";
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);

        $mail->Body = "
            <p>Hola <b>{$personal->nombre}</b>,</p>
            <p>Estas son tus credenciales de acceso:</p>
            <ul>
                <li><b>Usuario:</b> {$personal->usuario}</li>
                <li><b>Clave:</b> {$personal->clave}</li>
                <br>
                <li><a href='https://intelli035.v2technoconsulting.com'>Acceder a la plataforma</a></li>
            </ul>
            <p>Por favor, guarda esta información de manera segura.</p>
            <p>Saludos cordiales,<br>Equipo de {$configuration['name']->value}</p>
        ";

        if ($mail->send()) {
            $notification = new NotificationData();
            $notification->personal_id = $personal->id;
            $notification->type_id = 1;
            $notification->status_id = 1;
            $notification->message = $mail->Body;
            $notification->receptor = $personal->correo;
            $notification->add();

            error_log("Correo enviado exitosamente a usuario ID $userId ({$personal->correo})");
            echo json_encode(['success' => true, 'message' => 'Correo enviado exitosamente.']);
        } else {
            // Esto raramente se ejecuta porque PHPMailer lanza excepciones, pero por si acaso:
            error_log("Error desconocido enviando correo a usuario ID $userId");
            echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo.']);
        }
    } catch (Exception $e) {
        error_log("Error PHPMailer usuario ID $userId: " . $mail->ErrorInfo);
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo enviar el correo.',
            'error' => $mail->ErrorInfo,
            'exception' => $e->getMessage()
        ]);
    }

} else {
    http_response_code(400);
    error_log("Solicitud inválida: método " . $_SERVER['REQUEST_METHOD']);
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida.']);
}
?>
