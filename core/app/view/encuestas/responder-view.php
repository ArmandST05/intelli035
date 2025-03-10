<?php

if (!isset($_SESSION['typeUser']) || $_SESSION['typeUser'] !== 'e') {
    die("Acceso denegado: Solo los empleados pueden responder encuestas.");
}

// Validar si se proporcionó un ID de encuesta
if (!isset($_GET['survey_id']) || !is_numeric($_GET['survey_id'])) {
    die("ID de encuesta inválido.");
}

$survey_id = intval($_GET['survey_id']);
$personal_id = $_SESSION['user_id']; // ID del empleado autenticado

// Obtener la encuesta asignada y sus preguntas organizadas por sección
$encuesta = EncuestaData::getAssignedSurveyById($personal_id, $survey_id);
if (!$encuesta) {
    die("Encuesta no encontrada o no asignada.");
}

$secciones = EncuestaData::getQuestionsBySection($survey_id);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Responder Encuesta</title>
    <script>
        // Función para revisar si alguna pregunta de la Sección 1 tiene respuesta "Sí"
        function checkSectionI() {
            let sectionIAnsweredYes = false;

            // Recorremos todas las preguntas de la sección 1
            let radios = document.querySelectorAll('input[name^="answers["]'); // Solo las respuestas
            radios.forEach(function(radio) {
                // Verificamos si la respuesta es "Sí" (valor 1)
                if (radio.checked && radio.value == '1') {
                    sectionIAnsweredYes = true;
                }
            });

            // Si alguna respuesta es "Sí", mostramos las siguientes secciones
            if (sectionIAnsweredYes) {
                document.getElementById('sectionII').style.display = 'block';
                document.getElementById('sectionIII').style.display = 'block';
                document.getElementById('sectionIV').style.display = 'block';
            } else {
                document.getElementById('sectionII').style.display = 'none';
                document.getElementById('sectionIII').style.display = 'none';
                document.getElementById('sectionIV').style.display = 'none';
            }
        }
        
        // Función para enviar el formulario
        function handleSubmit() {
            document.getElementById('surveyForm').submit();
        }
    </script>
</head>
<body>
    <h1><?php echo htmlspecialchars($encuesta->title); ?></h1>
    <p><?php echo htmlspecialchars($encuesta->description); ?></p>
    
    <form id="surveyForm" method="POST" action="index.php?action=encuestas/save-answers">
        <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">

        <!-- Sección I: Acontecimiento traumático severo -->
        <?php foreach ($secciones as $index => $seccion): ?>
            <?php if ($seccion['name'] == "I.- Acontecimiento traumático severo"): ?>
                <h2><?php echo htmlspecialchars($seccion['name']); ?></h2>
                <?php foreach ($seccion['questions'] as $pregunta): ?>
                    <div class="question">
                        <label for="question_<?php echo $pregunta['id']; ?>">
                            <?php echo htmlspecialchars($pregunta['text']); ?>
                        </label>
                        <br>
                        <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="1" required onchange="checkSectionI()"> Sí
                        <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="0" onchange="checkSectionI()"> No
                    </div>
                    <br>
                <?php endforeach; ?>
                <?php break; // Solo mostramos la Sección I al principio ?>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Sección II: Recuerdos persistentes sobre el acontecimiento -->
        <div id="sectionII" style="display: none;">
            <?php foreach ($secciones as $index => $seccion): ?>
                <?php if ($seccion['name'] == "II.- Recuerdos persistentes sobre el acontecimiento (durante el último mes)"): ?>
                    <h2><?php echo htmlspecialchars($seccion['name']); ?></h2>
                    <?php foreach ($seccion['questions'] as $pregunta): ?>
                        <div class="question">
                            <label for="question_<?php echo $pregunta['id']; ?>">
                                <?php echo htmlspecialchars($pregunta['text']); ?>
                            </label>
                            <br>
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="1" required> Sí
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="0"> No
                        </div>
                        <br>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Sección III: Esfuerzo por evitar circunstancias parecidas o asociadas -->
        <div id="sectionIII" style="display: none;">
            <?php foreach ($secciones as $index => $seccion): ?>
                <?php if ($seccion['name'] == "III.- Esfuerzo por evitar circunstancias parecidas o asociadas al acontecimiento (durante el último mes)"): ?>
                    <h2><?php echo htmlspecialchars($seccion['name']); ?></h2>
                    <?php foreach ($seccion['questions'] as $pregunta): ?>
                        <div class="question">
                            <label for="question_<?php echo $pregunta['id']; ?>">
                                <?php echo htmlspecialchars($pregunta['text']); ?>
                            </label>
                            <br>
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="1" required> Sí
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="0"> No
                        </div>
                        <br>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Sección IV: Afectación -->
        <div id="sectionIV" style="display: none;">
            <?php foreach ($secciones as $index => $seccion): ?>
                <?php if ($seccion['name'] == "IV.- Afectación (durante el último mes)"): ?>
                    <h2><?php echo htmlspecialchars($seccion['name']); ?></h2>
                    <?php foreach ($seccion['questions'] as $pregunta): ?>
                        <div class="question">
                            <label for="question_<?php echo $pregunta['id']; ?>">
                                <?php echo htmlspecialchars($pregunta['text']); ?>
                            </label>
                            <br>
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="1" required> Sí
                            <input type="radio" name="answers[<?php echo $pregunta['id']; ?>]" value="0"> No
                        </div>
                        <br>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <button type="button" onclick="handleSubmit()">Enviar Respuestas</button>
    </form>
</body>
</html>
