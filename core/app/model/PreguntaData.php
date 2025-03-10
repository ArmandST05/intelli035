
<?php
class PreguntaData {
    public static function getQuestionsBySurvey($encuesta_id) {
        $sql = "SELECT id, question_text FROM survey_questions WHERE survey_id = $encuesta_id";
        $result = Executor::doit($sql);

        $preguntas = [];
        if ($result && $result[0] instanceof mysqli_result) {
            while ($row = $result[0]->fetch_assoc()) {
                $preguntas[] = $row;
            }
        }

        return $preguntas;
    }
}
