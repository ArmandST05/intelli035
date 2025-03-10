<?php
require_once __DIR__ . '/../../../../vendor/autoload.php';

$conn = Database::getCon(); // Conexión a la BD

// Obtener filtros desde AJAX
$department_filter = isset($_POST['department_filter']) ? $_POST['department_filter'] : '';
$custom_search = isset($_POST['custom_search']) ? $_POST['custom_search'] : '';

// Construir consulta SQL
$sql = "SELECT personal.id, personal.nombre, personal.usuario, personal.clave, personal.correo, personal.telefono, 
        departamentos.nombre AS departamento
        FROM personal
        INNER JOIN departamentos ON personal.id_departamento = departamentos.idDepartamento";

$where = [];
if (!empty($custom_search)) {
    $searchValue = mysqli_real_escape_string($conn, $custom_search);
    $where[] = "(personal.nombre LIKE '%$searchValue%')";
}
if (!empty($department_filter)) {
    $where[] = "personal.id_departamento = " . mysqli_real_escape_string($conn, $department_filter);
}
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Ejecutar consulta
$query = mysqli_query($conn, $sql);

// Crear HTML para el PDF
$html = '<h2>Lista de Personal</h2>';
$html .= '<table border="1" cellpadding="5" cellspacing="0">';
$html .= '<tr><th>ID</th><th>Nombre</th><th>Departamento</th><th>Usuario</th><th>Correo</th><th>Teléfono</th></tr>';

while ($row = mysqli_fetch_assoc($query)) {
    $html .= '<tr>';
    $html .= '<td>' . $row['id'] . '</td>';
    $html .= '<td>' . $row['nombre'] . '</td>';
    $html .= '<td>' . $row['departamento'] . '</td>';
    $html .= '<td>' . $row['usuario'] . '</td>';
    $html .= '<td>' . $row['correo'] . '</td>';
    $html .= '<td>' . $row['telefono'] . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

// Generar PDF con mPDF
$mpdf = new \Mpdf\Mpdf();
$mpdf->WriteHTML($html);

// Configurar cabeceras para la descarga del PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Personal.pdf"');
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $mpdf->Output('', 'S'); // Enviar el PDF al navegador
exit();
?>
