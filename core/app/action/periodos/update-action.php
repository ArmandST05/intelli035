<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $periodo = PeriodoData::getById($_POST['id']);

        if ($periodo) {
            $periodo->name = $_POST['name'] ?? $periodo->name;
            $periodo->start_date = $_POST['start_date'] ?? $periodo->start_date;
            $periodo->end_date = $_POST['end_date'] ?? $periodo->end_date;
            $periodo->status = $_POST['status'] ?? $periodo->status;

            if ($periodo->update()) {
                echo json_encode(['status' => 'success', 'message' => 'Periodo actualizado correctamente.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el periodo.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Periodo no encontrado.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID no válido.']);
    }
}
?>
