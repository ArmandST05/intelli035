<?php

function calcularCategoriasEncuesta2() {
    // Consulta para obtener valores agrupados por categoría
    $sql = "SELECT 
    category.id AS categoria_id, 
    category.name AS categoria_nombre, 
    dominio.id AS dominio_id, 
    dimensiones.id AS dimension_id, 
    survey_answers.personal_id AS persona_id,
    survey_answers.valor AS total_valor 
FROM survey_answers 
INNER JOIN psychosocial_risk_questions 
    ON survey_answers.question_id = psychosocial_risk_questions.id 
INNER JOIN dimensiones 
    ON psychosocial_risk_questions.id_dimension = dimensiones.id 
INNER JOIN dominio 
    ON dimensiones.dominio_id = dominio.id 
INNER JOIN category 
    ON dominio.category_id = category.id 
WHERE survey_answers.survey_id = 2

";

    $result = Executor::doit($sql);

    $categorias = [];
    if ($result && $result[0]) {
        while ($row = $result[0]->fetch_assoc()) {
            $categoria_id = $row['categoria_id'];
            $categorias[$categoria_id] = [
                'categoria_id' => $categoria_id,
                'categoria_nombre' => $row['categoria_nombre'],
                'total_valor' => $row['total_valor'],
            ];
        }
    } else {
        error_log("No se obtuvieron resultados para consulta de categorías: $sql");
        return ['error' => 'No se encontraron resultados para los parámetros proporcionados.'];
    }

    // Asignar niveles según las categorías
    foreach ($categorias as &$categoria) {
        switch ($categoria['categoria_id']) {
            case 1: // Ambiente de trabajo
                if ($categoria['total_valor'] < 3) $categoria['nivel'] = "Nulo";
                elseif ($categoria['total_valor'] < 5) $categoria['nivel'] = "Bajo";
                elseif ($categoria['total_valor'] < 7) $categoria['nivel'] = "Medio";
                elseif ($categoria['total_valor'] < 9) $categoria['nivel'] = "Alto";
                else $categoria['nivel'] = "Muy Alto";
                break;
            case 3: // Factores propios de la actividad
                if ($categoria['total_valor'] < 10) $categoria['nivel'] = "Nulo";
                elseif ($categoria['total_valor'] < 20) $categoria['nivel'] = "Bajo";
                elseif ($categoria['total_valor'] < 30) $categoria['nivel'] = "Medio";
                elseif ($categoria['total_valor'] < 40) $categoria['nivel'] = "Alto";
                else $categoria['nivel'] = "Muy Alto";
                break;
            case 4: // Organización del tiempo de trabajo
                if ($categoria['total_valor'] < 4) $categoria['nivel'] = "Nulo";
                elseif ($categoria['total_valor'] < 6) $categoria['nivel'] = "Bajo";
                elseif ($categoria['total_valor'] < 9) $categoria['nivel'] = "Medio";
                elseif ($categoria['total_valor'] < 12) $categoria['nivel'] = "Alto";
                else $categoria['nivel'] = "Muy Alto";
                break;
            case 5: // Liderazgo y relaciones en el trabajo
                if ($categoria['total_valor'] < 10) $categoria['nivel'] = "Nulo";
                elseif ($categoria['total_valor'] < 18) $categoria['nivel'] = "Bajo";
                elseif ($categoria['total_valor'] < 28) $categoria['nivel'] = "Medio";
                elseif ($categoria['total_valor'] < 38) $categoria['nivel'] = "Alto";
                else $categoria['nivel'] = "Muy Alto";
                break;
            default:
                $categoria['nivel'] = "Nivel no definido";
                break;
        }
    }

    return ['categorias' => $categorias];
}

// Validación de parámetros GET
if (isset($_GET['survey_id']) &&  $_GET['survey_id'] == 2) { // Solo encuesta 2

    $response = calcularCategoriasEncuesta2();

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'Parámetros inválidos o faltantes.']);
}
?>
