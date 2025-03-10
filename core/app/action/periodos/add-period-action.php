<?php
// Depuración: Ver los datos recibidos
error_log('Datos recibidos: ' . print_r($_POST, true));

// Verificar si los datos necesarios están presentes en la solicitud
if (
    !empty($_POST['name']) &&
    !empty($_POST['start_date']) &&
    !empty($_POST['end_date']) &&
    !empty($_POST['status']) &&
    !empty($_POST['empresa_id'])
) {
    // Crear una nueva instancia de PeriodoData
    $periodo = new PeriodoData();

    // Asignar los valores del formulario a las propiedades del objeto
    $periodo->name = $_POST['name'];
    $periodo->start_date = $_POST['start_date'];
    $periodo->end_date = $_POST['end_date'];
    $periodo->status = $_POST['status'];
    $periodo->empresa_id = $_POST['empresa_id'];

    // Intentar guardar el nuevo periodo
    $periodo->add();

    // Enviar respuesta de éxito
    echo json_encode(['status' => 'success', 'message' => 'Periodo agregado correctamente.']);
} else {
    // Si faltan datos, enviar un mensaje de error
    echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
}
