<?php
if (count($_POST) > 0) {
    // Cargar la clase y crear una nueva instancia
    $newPersonal = new PersonalData();

    // Asignar los valores recibidos desde el formulario
    $newPersonal->name = trim($_POST["employeeName"]);  // "employeeName" en el formulario → "nombre" en la base de datos
    $newPersonal->email = trim($_POST["employeeEmail"]);  // "employeeEmail" → "correo"
    $newPersonal->id_departamento = $_POST["employeeDepartment"];  // "employeeDepartment" → "id_departamento"
    $newPersonal->id_puesto = $_POST["employeeRole"];  // "employeeRole" → "id_puesto"
    $newPersonal->fecha_alta = $_POST["employeeDate"];  // "employeeDate" → "fecha_alta"
    $newPersonal->phone = $_POST["employeePhone"];  // "employeePhone" → "telefono"

    // Generar automáticamente el usuario
    $nombre = trim($newPersonal->name);
    $iniciales = strtoupper(substr($nombre, 0, 1));
    $palabras = explode(' ', $nombre);
    if (count($palabras) > 1) {
        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
    }
    $numeroAzar = rand(100000, 999999);
    $newPersonal->usuario = 'u' . $iniciales . $numeroAzar;

    // Generar automáticamente la clave
    $longitudClave = rand(6, 8);
    $newPersonal->clave = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $longitudClave);

    try {
        // Llamar al método para guardar el nuevo personal
        $result = $newPersonal->add();

        if ($result) {
            // Respuesta de éxito
            echo json_encode(["success" => true, "message" => "Personal agregado con éxito."]);
        } else {
            // Error al guardar en la base de datos
            http_response_code(500);
            echo json_encode(["error" => "Error al agregar el personal en la base de datos."]);
        }
    } catch (Exception $e) {
        // Capturar y devolver cualquier excepción
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
} else {
    // Error si no se reciben datos
    http_response_code(400);
    echo json_encode(["error" => "No se recibieron datos."]);
}
