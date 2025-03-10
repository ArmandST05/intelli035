<?php
// Asegúrate de que la conexión a la base de datos esté incluida

// Verifica que se haya recibido el 'period_id' y 'empresa_id' desde el formulario
if (isset($_POST['period_id']) && isset($_POST['empresa_id']) && isset($_POST['surveys'])) {
    $period_id = intval($_POST['period_id']);
    $empresa_id = intval($_POST['empresa_id']);
    $survey_ids = $_POST['surveys'];  // Aquí se obtienen las encuestas seleccionadas

    // Obtener los empleados de la empresa seleccionada en el periodo
    $sql = "SELECT id FROM personal WHERE empresa_id = $empresa_id";
    $result = Executor::doit($sql);

    // Verifica si la consulta fue exitosa y si hay empleados
    if ($result && $result[0]->num_rows > 0) {
        $employees = [];
        while ($row = $result[0]->fetch_assoc()) {
            $employees[] = $row['id'];  // Almacena los IDs de los empleados
        }

        // Asignar las encuestas seleccionadas a cada empleado
        foreach ($employees as $employee_id) {
            foreach ($survey_ids as $survey_id) {
                // Asegúrate de que el ID de la encuesta es un número entero
                $survey_id = intval($survey_id);

                // Insertar la encuesta asignada al empleado
                $sql_assign = "INSERT INTO personal_surveys (personal_id, survey_id, completed, assigned_at)
                               VALUES ($employee_id, $survey_id, 0, NOW())";
                Executor::doit($sql_assign);
            }
        }

        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();
    } else {
        // Si no se encontraron empleados en esa empresa
        echo "No se encontraron empleados para la empresa seleccionada.";
    }
} else {
    // Si faltan datos necesarios en el formulario
    echo "Faltan datos en el formulario (period_id, empresa_id o surveys).";
}
?>
