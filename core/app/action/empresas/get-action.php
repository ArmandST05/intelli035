<?php
// Validar si el parámetro `id` está presente en la solicitud
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Convertir el ID a un entero para mayor seguridad
    $idEmpresa = intval($_GET['id']);
    
    // Obtener el puesto correspondiente utilizando la clase de datos (PuestoData)
    $empresa = EmpresaData::getById($idEmpresa);

    if ($empresa) {
        // Si se encuentra el puesto, devolver sus datos como JSON
        echo json_encode([
            "id" => $empresa->id,
            "nombre" => $empresa->nombre,
            "comentarios" => $empresa->comentarios,
            "id_cantidad" => $empresa->id_cantidad
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
