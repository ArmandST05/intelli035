<?php

function calcularDominioGeneral()
{
    // SQL para obtener los dominios y respuestas relacionadas sin filtrar por personal_id
    $sql = "SELECT 
    dominio.id AS dominio_id, 
    dominio.name AS dominio_nombre, 
    dimensiones.id AS dimension_id, 
    SUM(survey_answers.valor) AS total_valor
FROM survey_answers 
INNER JOIN psychosocial_risk_questions 
    ON survey_answers.question_id = psychosocial_risk_questions.id 
INNER JOIN dimensiones 
    ON psychosocial_risk_questions.id_dimension = dimensiones.id 
INNER JOIN dominio 
    ON dimensiones.dominio_id = dominio.id 
WHERE survey_answers.survey_id = 2 
GROUP BY dominio.id, dominio.name, dimensiones.id;
";

    $result = Executor::doit($sql);
    $dominios = [];

    // Comprobamos si hay resultados y procesamos los dominios
    if ($result && $result[0]) {
        while ($row = $result[0]->fetch_assoc()) {
            $dominio_id = $row['dominio_id'];
            $dominios[$dominio_id] = [
                'dominio_id' => $dominio_id,
                'dominio_nombre' => $row['dominio_nombre'],
                'total_valor' => $row['total_valor'],
            ];
        }
    } else {
        error_log("No se obtuvieron resultados para la consulta: $sql");
        return ['error' => 'No se encontraron resultados.'];
    }

    // Calcular el nivel de cada dominio
    foreach ($dominios as &$dominio) {
        $valor_total = $dominio['total_valor'];
        switch ($dominio['dominio_id']) {
            case 1: // Condiciones en el ambiente de trabajo
                if ($valor_total < 3) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 5) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 7) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 9) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 2: // Carga de trabajo
                if ($valor_total < 12) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 15) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 20) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 24) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 3: // Falta del control sobre el trabajo
                if ($valor_total < 5) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 8) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 11) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 14) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 4: // Jornada de trabajo
                if ($valor_total < 1) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 2) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 4) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 6) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 5: // Interferencia en la relación trabajo-familia
                if ($valor_total < 1) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 2) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 4) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 6) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 6: // Liderazgo
                if ($valor_total < 3) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 5) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 8) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 11) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 7: // Relaciones en el trabajo
                if ($valor_total < 5) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 8) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 11) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 14) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 8: // Violencia
                if ($valor_total < 7) $dominio['nivel'] = 'Nulo';
                elseif ($valor_total <= 10) $dominio['nivel'] = 'Bajo';
                elseif ($valor_total <= 13) $dominio['nivel'] = 'Medio';
                elseif ($valor_total <= 16) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            default:
                $dominio['nivel'] = "Nivel no definido";
                break;
        }
    }

    return ['dominios' => $dominios];
}

// Validación de parámetros GET
if (
    isset($_GET['survey_id']) &&
    $_GET['survey_id'] == 2
) {
    $survey_id = intval($_GET['survey_id']);

    // Ejecutar la función para cálculos generales
    $response = calcularDominioGeneral();

    // Asegurarse de que la respuesta sea válida
    if (isset($response['error'])) {
        http_response_code(400); // Si hay un error, enviamos un código de error 400
    }

    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Error en los parámetros
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Parámetros inválidos o faltantes.']);
}
?>
