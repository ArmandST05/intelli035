<?php
// Validar y obtener el survey_id
if (isset($_GET['survey_id']) && is_numeric($_GET['survey_id'])) {
    $survey_id = intval($_GET['survey_id']); // Convertir a entero
} else {
    die("ID de encuesta inválido.");
}

// Obtener las preguntas generales
$questions = EncuestaData::getPsychosocialRiskQuestions($survey_id);

// Obtener preguntas específicas dependiendo del survey_id
if ($survey_id == 2) {
    $serviceClientQuestions = EncuestaData::getServiceClientQuestions($survey_id); // Se asume que ya se ajustó para 2
    $bossQuestions = EncuestaData::getBossQuestions($survey_id); // Se asume que ya se ajustó para 2
} elseif ($survey_id == 3) {
    $serviceClientQuestions = EncuestaData::getServiceClientQuestions($survey_id); // Se asume que ya se ajustó para 3
    $bossQuestions = EncuestaData::getBossQuestions($survey_id); // Se asume que ya se ajustó para 3
} else {
    die("Encuesta no válida.");
}

// Verificar que se obtuvieron preguntas generales
if (empty($questions)) {
    die("No se encontraron preguntas para esta encuesta.");
}
?>

<!-- Estilos para la tabla -->
<style>
.table {
    border-collapse: collapse;
    width: 80%;
    margin: 0 auto;
    text-align: center;
    background-color: #e5e8e8;
    padding: 10px;
}
thead {
    background-color: #3498db;
    color: white;
    text-align: left;
}
tr {
    border-bottom: 1px solid #ccc;
}
.hidden {
    display: none;
}
</style>

<!-- Formulario -->
<form action="index.php?action=encuestas/save-answers-factorpsicosocial" method="POST">
    <input type="hidden" name="survey_id" value="<?php echo $survey_id; ?>">

    <table class="table table-hover">
        <thead>
            <tr>
            <th scope="col">id</th>
                <th scope="col">Pregunta</th>
                <th scope="col">Siempre</th>
                <th scope="col">Casi siempre</th>
                <th scope="col">Algunas veces</th>
                <th scope="col">Casi nunca</th>
                <th scope="col">Nunca</th>
            </tr>
        </thead>
        <tbody>
            <!-- Preguntas generales -->
            <?php foreach ($questions as $question): ?>
                <tr>
                <td><?php echo htmlspecialchars($question->id); ?></td>
                    <td><?php echo htmlspecialchars($question->text); ?></td>
                    <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Siempre" required></td>
                    <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi siempre"></td>
                    <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Algunas veces"></td>
                    <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi nunca"></td>
                    <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Nunca"></td>
                </tr>
            <?php endforeach; ?>

            <!-- Pregunta de servicio al cliente y las preguntas asociadas (solo si es encuesta 2 o 3) -->
            <?php if ($survey_id == 2 || $survey_id == 3): ?>
                <!-- Pregunta principal de servicio al cliente -->
                <tr>
                    <td><strong>¿En tu trabajo debes brindar servicio a clientes o usuarios?</strong></td>
                    <td>
                        <input type="radio" name="service_client" value="Si" onclick="toggleServiceClientQuestions(true)" required> Sí
                    </td>
                    <td>
                        <input type="radio" name="service_client" value="No" onclick="toggleServiceClientQuestions(false)"> No
                    </td>
                </tr>

                <!-- Preguntas de servicio al cliente (inicialmente ocultas) -->
                <?php foreach ($serviceClientQuestions as $question): ?>
                    <tr class="hidden service-client-question">
                    <td><?php echo htmlspecialchars($question->id); ?></td>

                        <td><?php echo htmlspecialchars($question->text); ?></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Siempre" required></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi siempre"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Algunas veces"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi nunca"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Nunca"></td>
                    </tr>
                <?php endforeach; ?>

                <!-- Pregunta principal de jefe -->
                <tr>
                    <td><strong>¿Eres jefe de otros trabajadores?</strong></td>
                    <td>
                        <input type="radio" name="is_boss" value="Si" onclick="toggleBossQuestions(true)" required> Sí
                    </td>
                    <td>
                        <input type="radio" name="is_boss" value="No" onclick="toggleBossQuestions(false)"> No
                    </td>
                </tr>

                <!-- Preguntas de jefe (inicialmente ocultas) -->
                <?php foreach ($bossQuestions as $question): ?>
                    <tr class="hidden boss-question">
                    <td><?php echo htmlspecialchars($question->id); ?></td>

                        <td><?php echo htmlspecialchars($question->text); ?></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Siempre"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi siempre"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Algunas veces"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Casi nunca"></td>
                        <td><input type="radio" name="answers[<?php echo $question->id; ?>]" value="Nunca"></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

        </tbody>
    </table>

    <button type="submit">Enviar respuestas</button>
</form>

<style>
    .hidden {
        display: none !important;
    }
</style>

<!-- Script para manejar la visibilidad de preguntas -->
<script>
function toggleServiceClientQuestions(show) {
    const questions = document.querySelectorAll('.service-client-question');
    questions.forEach(question => {
        if (show) {
            question.classList.remove('hidden');
        } else {
            question.classList.add('hidden');
        }
    });
}

function toggleBossQuestions(show) {
    const questions = document.querySelectorAll('.boss-question');
    questions.forEach(question => {
        if (show) {
            question.classList.remove('hidden');
        } else {
            question.classList.add('hidden');
        }
    });
}
</script>
