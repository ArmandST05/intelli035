<?php 
if (count($_POST) > 0) {
    // Log de los datos recibidos
    error_log(print_r($_POST, true));  // Esto te ayudará a verificar si los datos están llegando correctamente
    
    $newPuesto = new PuestoData();
    $newPuesto->nombre = trim($_POST["roleName"]);
    $newPuesto->id_departamento = $_POST["id_departamento"];
    $newPuesto->id_encuesta = $_POST["id_encuesta"];
    

    try {
        $result = $newPuesto->add(); // Guarda en la base de datos

        if ($result) {
            echo json_encode(["success" => true, "message" => "Puesto agregado con éxito."]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Error al agregar el puesto en la base de datos."]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["error" => "No se recibieron datos."]);
}


?>