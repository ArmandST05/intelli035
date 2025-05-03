<?php
if (isset($_GET['survey_id'])) {
    $survey_id = $_GET['survey_id'];

    // Obtener todos los empleados que completaron esa encuesta
    $sql = "
        SELECT 
            p.id as personal_id, 
            p.nombre, 
            COUNT(sa.question_id) as total
        FROM survey_answers sa
        INNER JOIN personal p ON sa.personal_id = p.id
        INNER JOIN personal_surveys ps ON sa.personal_id = ps.personal_id
        WHERE ps.completed = 1 AND sa.survey_id = $survey_id
        GROUP BY sa.personal_id
    ";

    $result = Executor::doit($sql);

    $data = [];

    if ($result && $result[0] instanceof mysqli_result) {
        while ($row = $result[0]->fetch_assoc()) {
            $total = $row['total'];
            $nivel = "";
            $color = "";

            if ($total >= 90) {
                $nivel = "Muy Alto";
                $color = "rgba(255, 0, 0, 0.8)";
            } elseif ($total >= 70) {
                $nivel = "Alto";
                $color = "rgba(255, 159, 64, 0.8)";
            } elseif ($total >= 45) {
                $nivel = "Medio";
                $color = "rgb(251, 255, 0)";
            } elseif ($total >= 20) {
                $nivel = "Bajo";
                $color = "rgba(75, 192, 192, 0.8)";
            } else {
                $nivel = "Nulo";
                $color = "rgba(0, 225, 255, 0.8)";
            }

            $data[] = [
                'nombre' => $row['nombre'],
                'total' => $total,
                'nivel' => $nivel,
                'color' => $color
            ];
        }
    }

    echo json_encode(['empleados' => $data]);
    exit;
}
?>
