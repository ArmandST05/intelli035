<?php
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $idPersonal = intval($_GET['id']);
    $personal = PersonalData::getByRole($idPersonal);

    if ($personal) {
        echo json_encode([
            "id" => $personal->id,
            "nombre" => $personal->nombre,  // Nombre del personal
            "id_puesto" => $personal->id_puesto,
            "puesto_nombre" => $personal->puesto_nombre,  // Nombre del puesto
            "id_departamento" => $personal->id_departamento,
            "fecha_alta" => $personal->fecha_alta,
            "telefono" => $personal->telefono,
        ]);
    }
     else {
        echo json_encode(["error" => "No se encontró el registro."]);
    }
} else {
    echo json_encode(["error" => "ID no válido."]);
}
