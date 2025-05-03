<?php

class ReporteData {
    public static $tablename = "survey_answers"; // Tabla donde se guardan las respuestas

    public static function getCompletedEmployees() {
        $sql = "SELECT DISTINCT personal_surveys.personal_id AS personal_id, personal.nombre AS personal_name
                FROM personal_surveys
                JOIN personal ON personal_surveys.personal_id = personal.id
                WHERE personal_surveys.completed = 1";
        
        $result = Executor::doit($sql); // Ejecutar la consulta y obtener el resultado
    
        if ($result && $result[0] instanceof mysqli_result) {
            $empleados = [];
            while ($row = $result[0]->fetch_assoc()) {
                $empleados[] = $row;
            }
            return $empleados;
        }
    
        return [];
    }
    


    // Función para obtener las respuestas de un empleado para una encuesta específica
public static function getAnswersByEmployeeAndSurvey($personal_id, $survey_id) {
    $sql = "SELECT *
            FROM survey_answers
            JOIN personal_surveys ON survey_answers.personal_id = personal_surveys.personal_id 
                                     AND survey_answers.survey_id = personal_surveys.survey_id
            WHERE personal_surveys.completed = 1 
              AND survey_answers.survey_id = $survey_id 
              AND survey_answers.personal_id = $personal_id";
    return Executor::doit($sql);
}

    
        public static function getAnswers($personal_id, $encuesta_id) {
            $sql = "SELECT question_id, response FROM survey_answers WHERE personal_id = $personal_id AND survey_id = $encuesta_id";
            $result = Executor::doit($sql);
    
            $respuestas = [];
            if ($result && $result[0] instanceof mysqli_result) {
                while ($row = $result[0]->fetch_assoc()) {
                    $respuestas[$row['question_id']] = $row['response'];
                }
            }
    
            return $respuestas;
        }
    
        public static function getEmployeesCompletedSurvey2And3() {
            $sql = "
                SELECT p.id AS personal_id, CONCAT(p.name, ' ', p.lastname) AS personal_name
                FROM personal p
                INNER JOIN personal_surveys ps ON ps.personal_id = p.id
                WHERE ps.completed = 1 AND ps.survey_id IN (2, 3)
                GROUP BY p.id
                HAVING COUNT(DISTINCT ps.survey_id) = 2
                ORDER BY personal_name ASC
            ";
        
            $result = Executor::doit($sql);
        
            $empleados = [];
            if ($result && $result[0] instanceof mysqli_result) {
                while ($row = $result[0]->fetch_assoc()) {
                    $empleados[] = [
                        'personal_id' => $row['personal_id'],
                        'personal_name' => $row['personal_name']
                    ];
                }
            }
        
            return $empleados;
        }
        
        
}
