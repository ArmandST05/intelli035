<?php
// RespuestaData.php
class RespuestaData {
    public static $tablename = "survey_answers"; // Tabla donde se guardan las respuestas

    // Función para guardar las respuestas
    public static function save($personal_id, $question_id, $answer_value) {
        $sql = "INSERT INTO " . self::$tablename . " (personal_id, question_id, answer_value) 
                VALUES ($personal_id, $question_id, $answer_value)";
        return Executor::doit($sql);
    }

    // Función para marcar la encuesta como completada
    public static function completeSurvey($personal_id, $survey_id) {
        $sql = "UPDATE personal_surveys 
                SET completed = 1, completed_at = NOW() 
                WHERE personal_id = $personal_id AND survey_id = $survey_id";
        return Executor::doit($sql);
    }
}
