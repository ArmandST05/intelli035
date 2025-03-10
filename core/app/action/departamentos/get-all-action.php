<?php
$conn = Database::getCon();

// Consulta para obtener todos los departamentos
$sql = "SELECT idDepartamento, nombre FROM departamentos";

// Ejecutar la consulta
$query = mysqli_query($conn, $sql);

// Verificar si hay resultados
$departments = array();
while ($row = mysqli_fetch_assoc($query)) {
    $departments[] = array(
        'id' => $row['idDepartamento'],
        'nombre' => $row['nombre']
    );
}
// Retornar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($departments);
?>
