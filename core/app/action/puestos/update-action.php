<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["id"]) || empty($_POST["id"])) {
        die("Error: ID del puesto no recibido.");
    }

    $puesto = PuestoData::getById($_POST["id"]);

    if (!$puesto) {
        die("Error: No se encontró el puesto.");
    }

    $puesto->nombre = $_POST["nombre"];
    $puesto->id_departamento = $_POST["id_departamento"];
    $puesto->id_encuesta = $_POST["id_encuesta"];

    if ($puesto->update()) {
        echo "Puesto actualizado correctamente.";
    } else {
        die("Error: Falló la actualización en la base de datos.");
    }
}
?>
