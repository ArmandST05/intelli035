<?php
// Obtén el usuario actual basado en la sesión
if (isset($_SESSION['typeUser']) && $_SESSION['typeUser'] === 'e') {
    // Si es un empleado
    $user_name = $_SESSION['user_name'] ?? "Invitado";
} else {
    // Si es un usuario del sistema
    $user = UserData::getLoggedIn();
    $user_name = $user ? $user->name : "Invitado";
}

if ($_SESSION['typeUser'] === 'e') {
    $personal_id = $_SESSION['user_id'];
    $encuestas = EncuestaData::getAssignedSurveys($personal_id);
} else {
    die("Acceso denegado: Solo los empleados tienen encuestas asignadas.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Encuestas Asignadas</title>
</head>
<body>
    <h1>Bienvenido, <?php echo htmlspecialchars($user_name); ?></h1>
    <h2>Encuestas Asignadas</h2>
    <ul>
        <?php if (!empty($encuestas)): ?>
            <?php foreach ($encuestas as $encuesta): ?>
                <li>
                    <strong><?php echo htmlspecialchars($encuesta->title); ?></strong><br>
                    <p><?php echo htmlspecialchars($encuesta->description); ?></p>

                    <!-- Verifica el tipo de encuesta y muestra un botón específico -->
                    <?php if ($encuesta->type === 'psychosocial_risk'): ?>
                        <a href="./index.php?view=encuestas/factor-psicosocial&survey_id=<?php echo $encuesta->id ?>">
                            Responder Encuesta de Riesgo Psicosocial
                        </a>
                    <?php else: ?>
                        <a href="./index.php?view=encuestas/responder&survey_id=<?php echo $encuesta->id ?>">
                            Responder Encuesta General
                        </a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No tienes encuestas asignadas.</p>
        <?php endif; ?>
    </ul>
</body>
</html>
