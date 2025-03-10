<?php

function calcularDominioGeneral()
{
    // SQL para obtener los dominios y respuestas relacionadas
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
WHERE survey_answers.survey_id = 3
GROUP BY dominio.id, dominio.name, dimensiones.id;";

    $result = Executor::doit($sql);
    $dominios = [];

    // Comprobamos si hay resultados y procesamos los dominios
    if ($result && $result[0]) {
        while ($row = $result[0]->fetch_assoc()) {
            $dominio_id = $row['dominio_id'];
            if (!isset($dominios[$dominio_id])) {
                $dominios[$dominio_id] = [
                    'dominio_id' => $dominio_id,
                    'dominio_nombre' => $row['dominio_nombre'],
                    'total_valor' => 0,
                ];
            }
            $dominios[$dominio_id]['total_valor'] += $row['total_valor'];
        }
    } else {
        error_log("No se obtuvieron resultados para la consulta: $sql");
        return ['error' => 'No se encontraron resultados para los parámetros proporcionados.'];
    }

    // Calcular el nivel de cada dominio
    foreach ($dominios as &$dominio) {
        $valor_dominio = $dominio['total_valor'];
        switch ($dominio['dominio_id']) {
            case 1: // Condiciones en el ambiente de trabajo
                if ($valor_dominio < 5) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 9) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 11) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 14) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 2: // Carga de trabajo
                if ($valor_dominio < 15) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 21) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 27) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 37) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 3: // Falta del control sobre el trabajo
                if ($valor_dominio < 11) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 16) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 21) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 25) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 4: // Jornada de trabajo
                if ($valor_dominio < 1) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 2) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 4) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 6) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 5: // Interferencia en la relación trabajo-familia
                if ($valor_dominio < 4) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 6) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 8) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 10) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 6: // Liderazgo
                if ($valor_dominio < 9) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 12) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 16) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 20) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 7: // Relaciones en el trabajo
                if ($valor_dominio < 10) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 13) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 17) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 21) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 8: // Violencia
                if ($valor_dominio < 7) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 10) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 13) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 16) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 9: // Reconocimiento del desempeño
                if ($valor_dominio < 6) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 10) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 14) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 18) $dominio['nivel'] = 'Alto';
                else $dominio['nivel'] = 'Muy Alto';
                break;

            case 10: // Insuficiente sentido de pertenencia e inestabilidad
                if ($valor_dominio < 4) $dominio['nivel'] = 'Nulo';
                elseif ($valor_dominio <= 6) $dominio['nivel'] = 'Bajo';
                elseif ($valor_dominio <= 8) $dominio['nivel'] = 'Medio';
                elseif ($valor_dominio <= 10) $dominio['nivel'] = 'Alto';
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
    $_GET['survey_id'] == 3
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
