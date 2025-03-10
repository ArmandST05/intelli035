<?php

if (count($_POST) > 0) {
    // Obtener el personal desde la base de datos
    $personal = PersonalData::getById($_POST["id"]);
    if ($personal) {
        $personal->nombre = $_POST["nombre"] ?? 'Sin Nombre';
        $personal->correo = $_POST["correo"] ?? '';
        $personal->id_departamento = !empty($_POST["id_departamento"]) ? $_POST["id_departamento"] : 0;
        $personal->id_puesto = !empty($_POST["id_puesto"]) ? $_POST["id_puesto"] : 0;
        $personal->fecha_alta = !empty($_POST["fecha_alta"]) ? $_POST["fecha_alta"] : date('Y-m-d');
        $personal->telefono = !empty($_POST["telefono"]) ? $_POST["telefono"] : 'Sin Teléfono';
    
        if ($personal->update()) {
            echo json_encode(["success" => true, "message" => "Personal actualizado correctamente."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al ejecutar la actualización en la base de datos."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Personal no encontrado."]);
    }
    
    
}

?>
