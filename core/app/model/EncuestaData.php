<?php 

class EncuestaData{
    
    public static $tablename = "surveys";
    public function __construct() {
        $this->id = "";
        $this->title = "";
        $this->description = "";
        $this->created_at = "";
    }
    
    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    // Obtener encuestas asignadas a un empleado
    public static function getAssignedSurveys($personal_id) {
        $sql = "SELECT s.* 
                FROM personal_surveys ps
                INNER JOIN " . self::$tablename . " s ON ps.survey_id = s.id
                WHERE ps.personal_id = $personal_id AND ps.completed = 0";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    
    // Obtener preguntas de una encuesta específica
    public static function getQuestions($survey_id) {
        $sql = "SELECT * FROM survey_questions WHERE survey_id = $survey_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    public static function assignToPersonal($personalId, $surveyIds) {
        foreach ($surveyIds as $surveyId) {
            $sql = "INSERT INTO personal_surveys (personal_id, survey_id, completed, assigned_at) 
                    VALUES ($personalId, $surveyId, 0, NOW())";
            Executor::doit($sql);
        }
        return true;
    }
    public static function getEmployeeByEmpresa($empresa_id){
       $sql ="SELECT id, empresa_id FROM personal WHERE empresa_id = $empresa_id";
       $query = Executor::doit($sql);
       return Model::many($query[0], new EncuestaData());
    }
    // Método para asignar encuestas a un empleado
    public static function assignSurveysToEmployee($personalId, $surveyIds) {

        foreach ($surveyIds as $surveyId) {
            $surveyId = intval($surveyId); // Asegúrate de que sea un número
            $sql = "INSERT INTO personal_surveys (personal_id, survey_id, completed, assigned_at)
                    VALUES ($personalId, $surveyId, 0, NOW())";
            Executor::doit($sql);
        }
        return true;
        
        
    }
    public static function getAssignedSurveyById($personal_id, $survey_id) {
        $sql = "SELECT s.* 
                FROM personal_surveys ps
                INNER JOIN " . self::$tablename . " s ON ps.survey_id = s.id
                WHERE ps.personal_id = $personal_id AND ps.survey_id = $survey_id";
        $query = Executor::doit($sql);
        
        // Convertimos el resultado en un array
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC); // Usamos MYSQLI_ASSOC para obtener un array asociativo
        
        // Si hay resultados, retornamos el primero, mapeado al objeto EncuestaData
        if (count($result) > 0) {
            $encuesta = new EncuestaData();
            $encuesta->id = $result[0]['id'] ?? ""; // Asignamos el ID, o cadena vacía si no está presente
            $encuesta->title = $result[0]['title'] ?? ""; // Asignamos el título
            $encuesta->description = $result[0]['description'] ?? ""; // Asignamos la descripción
            $encuesta->created_at = $result[0]['created_at'] ?? ""; // Asignamos la fecha de creación (si está presente)
            return $encuesta;
        }
        
        // Si no se encuentra la encuesta asignada, retornamos null
        return null;
    }
    
    public static function getQuestionsBySection($survey_id) {
        $sql = "SELECT s.id AS section_id, s.name AS section_name, q.id AS question_id, q.question_text
                FROM sections s
                INNER JOIN survey_questions q ON s.id = q.section_id
                WHERE s.survey_id = $survey_id
                ORDER BY s.id, q.id";
        $query = Executor::doit($sql);
        
        // Convertimos el resultado en un array asociativo
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC);
        
        // Estructuramos los datos en un formato por secciones
        $sections = [];
        foreach ($result as $row) {
            $section_id = $row['section_id'];
            if (!isset($sections[$section_id])) {
                $sections[$section_id] = [
                    'name' => $row['section_name'],
                    'questions' => []
                ];
            }
            $sections[$section_id]['questions'][] = [
                'id' => $row['question_id'],
                'text' => $row['question_text']
            ];
        }
        
        return $sections; // Retornamos las secciones con sus preguntas agrupadas
    }
    public static function getSurveyResponses($survey_id, $personal_id) {
        $sql = "SELECT sa.* 
                FROM survey_answers sa
                WHERE sa.survey_id = $survey_id AND sa.personal_id = $personal_id";
        $query = Executor::doit($sql);
        
        // Convertimos el resultado en un array asociativo
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC);
        
        // Si hay resultados, retornamos las respuestas mapeadas a un formato adecuado
        if (count($result) > 0) {
            return $result; // Puedes optar por devolver los resultados como objetos si prefieres
        }
        
        // Si no se encuentran respuestas, retornamos un array vacío
        return [];
    }
    
    public static function getQuestionById($question_id) {
        $sql = "SELECT q.*, s.id AS section_id
                FROM survey_questions q
                LEFT JOIN sections s ON q.section_id = s.id
                WHERE q.id = $question_id";
        $query = Executor::doit($sql);
        
        // Convertimos el resultado en un array asociativo
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC);
        
        // Si se encuentra la pregunta, la retornamos como objeto
        if (count($result) > 0) {
            $question = new EncuestaData(); // Puedes cambiar EncuestaData si es necesario
            $question->id = $result[0]['id'] ?? "";
            $question->title = $result[0]['question_text'] ?? ""; // Asumí que 'question_text' es el texto de la pregunta
            $question->section_id = $result[0]['section_id'] ?? ""; // Aseguramos que 'section_id' esté presente
            return $question;
        }
        
        // Si no se encuentra la pregunta, retornamos null
        return null;
    }
    
    public static function getPsychosocialRiskQuestions($survey_id) {
        $sql = "SELECT * FROM psychosocial_risk_questions 
            WHERE survey_id = $survey_id
            and categoria = 1";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    public static function getPsychosocialRiskAnswers($survey_id, $user_id) {
        $sql = "SELECT * FROM psychosocial_risk_answers WHERE survey_id = $survey_id AND user_id = $user_id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    public static function savePsychosocialRiskAnswer($survey_id, $user_id, $question_id, $answer) {
        $sql = "INSERT INTO psychosocial_risk_answers (survey_id, user_id, question_id, answer)
                VALUES ($survey_id, $user_id, $question_id, '$answer')";
        Executor::doit($sql);
    }
    
    public static function getSurveyResults($survey_id) {
        $sql = "SELECT q.id AS question_id, q.question_text, a.response, COUNT(a.response) AS total_respuestas
                FROM survey_questions q
                LEFT JOIN survey_answers a ON q.id = a.question_id
                WHERE q.survey_id = $survey_id
                GROUP BY q.id, a.response
                ORDER BY q.id";
        
        $query = Executor::doit($sql);
        if ($query[0] === false) {
            // Loguear el error de la consulta
            error_log("Error en la consulta SQL: " . mysqli_error($query[0]));
            throw new Exception("Error en la consulta SQL");
        }
        
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC);
        $result = mysqli_fetch_all($query[0], MYSQLI_ASSOC);
        
        // Estructuramos los resultados agrupados por preguntas
        $questions = [];
        foreach ($result as $row) {
            $question_id = $row['question_id'];
            if (!isset($questions[$question_id])) {
                $questions[$question_id] = [
                    'question_text' => $row['question_text'],
                    'answers' => []
                ];
            }
            
            $questions[$question_id]['answers'][] = [
                'answer' => $row['answer'] ?? 'Sin respuesta', // Maneja respuestas nulas
                'count' => $row['total_respuestas']
            ];
        }
        
        return $questions; // Retorna el resultado estructurado
    }
    
    
    
    public static function getServiceClientQuestions($survey_id) {
        // Si el survey_id es 2
        if ($survey_id == 2) {
            $sql = "SELECT * FROM psychosocial_risk_questions WHERE id IN (41, 42, 43)";
        } 
        // Si el survey_id es 3
        else if ($survey_id == 3) {
            $sql = "SELECT * FROM psychosocial_risk_questions WHERE id IN (112, 113, 114, 115)";
            
        } 
        // Si no es ni 2 ni 3, retornar un array vacío o lanzar un error.
        else {
            return [];
        }
        
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    
    public static function getBossQuestions($survey_id) {
        // Si el survey_id es 2
        if ($survey_id == 2) {
            $sql = "SELECT * FROM psychosocial_risk_questions WHERE id IN (44, 45, 46)";
            //$sql = "SELECT * FROM psychosocial_risk_questions WHERE id IN (112, 113, 114, 115)";
        } 
        // Si el survey_id es 3
        else if ($survey_id == 3) {
            $sql = "SELECT * FROM psychosocial_risk_questions WHERE id IN (116, 117, 118, 119)";
        } 
        // Si no es ni 2 ni 3, retornar un array vacío o lanzar un error.
        else {
            return [];
        }
        
        $query = Executor::doit($sql);
        return Model::many($query[0], new EncuestaData());
    }
    
    public static function getPersonalByPuesto($puestoId) {
        $sql = "SELECT id FROM personal WHERE puesto_id = " . intval($puestoId);
        $query = Executor::doit($sql);
        return Model::many($query[0], new PersonalData()); // Suponiendo que PersonalData es el modelo para los empleados
    }
    
    // Método para asignar encuestas a todo el personal de un puesto
    public static function assignSurveysToRole($puestoId, $surveyIds) {
        // Verificamos que las encuestas no estén vacías
        if (empty($surveyIds)) {
            throw new Exception("No se proporcionaron encuestas para asignar.");
        }
        
        // Obtener todo el personal del puesto
        $personalList = self::getPersonalByPuesto($puestoId);
        
        // Asignar la encuesta a cada empleado del puesto
        foreach ($personalList as $personal) {
            foreach ($surveyIds as $surveyId) {
                $surveyId = intval($surveyId); // Asegúrate de que sea un número
                $sql = "INSERT INTO personal_surveys (personal_id, survey_id, completed, assigned_at)
                    VALUES ({$personal->id}, {$surveyId}, 0, NOW())";
                Executor::doit($sql);
            }
        }
        
        return true;
    }    
    
    public static function getAnswersByEmployeeAndSurvey($personal_id, $survey_id) {
        $sql = "SELECT survey_answers.*
            FROM survey_answers
            JOIN personal_surveys ON survey_answers.personal_id = personal_surveys.personal_id AND survey_answers.survey_id = personal_surveys.survey_id
            WHERE personal_surveys.completed = 1 AND survey_answers.survey_id = $survey_id AND survey_answers.personal_id = $personal_id";
        
        return Executor::doit($sql);
    }
    public static function getAllSurveyStatuses() {
        $sql = "SELECT personal_surveys.personal_id, personal.nombre, 
                       personal_surveys.survey_id, surveys.title, 
                       personal_surveys.completed 
                FROM personal_surveys
                INNER JOIN personal ON personal_surveys.personal_id = personal.id
                INNER JOIN surveys ON personal_surveys.survey_id = surveys.id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PersonalData()); // Retorna múltiples resultados
    }
    

  
}

?>