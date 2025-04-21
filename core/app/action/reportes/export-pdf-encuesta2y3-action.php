<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';
$conn = Database::getCon();

if (isset($_POST['encuesta_id']) && isset($_POST['personal_id']) && isset($_POST['table_html'])) {
    $encuesta_id = $_POST['encuesta_id'];
    $personal_id = $_POST['personal_id'];
    $table_html = $_POST['table_html'];

    // Obtener nombre de la encuesta
    $encuesta_sql = "SELECT title FROM surveys WHERE id = $encuesta_id";
    $encuesta_result = Executor::doit($encuesta_sql);
    $encuesta_nombre = ($encuesta_result && $encuesta_result[0]) ? $encuesta_result[0]->fetch_assoc()['title'] : "Encuesta";

    // Obtener nombre del empleado
    $empleado_sql = "SELECT nombre FROM personal WHERE id = $personal_id";
    $empleado_result = Executor::doit($empleado_sql);
    $empleado_nombre = ($empleado_result && $empleado_result[0]) ? $empleado_result[0]->fetch_assoc()['nombre'] : "Empleado";

    // Construir el contenido del PDF
    $html = "<h2>Reporte de Encuesta {$encuesta_id}</h2>" . $table_html;

    // Generar PDF
    $mpdf = new \Mpdf\Mpdf();
    $mpdf->WriteHTML($html);

    // Configurar cabeceras para descarga
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Reporte_' . str_replace(" ", "_", $encuesta_nombre) . '_' . str_replace(" ", "_", $empleado_nombre) . '.pdf"');

    echo $mpdf->Output('', 'S');
    exit;
}
