<?php
// Validar si el parámetro `id` está presente en la solicitud
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Convertir el ID a un entero para mayor seguridad
    $idPuesto = intval($_GET['id']);
    
    // Obtener el puesto correspondiente utilizando la clase de datos (PuestoData)
    $puesto = PuestoData::getById($idPuesto);

    if ($puesto) {
        // Si se encuentra el puesto, devolver sus datos como JSON
        echo json_encode([
            "id" => $puesto->id,
            "nombre" => $puesto->nombre,
            "id_departamento" => $puesto->id_departamento,
            "id_encuesta" => $puesto->id_encuesta
        ]);
    } else {
        // Si no se encuentra el registro, devolver un mensaje de error
        echo json_encode(["error" => "No se encontró el registro."]);
    }
} else {
    // Manejar el caso de un ID no válido o ausente
    echo json_encode(["error" => "ID no válido."]);
}
?>
