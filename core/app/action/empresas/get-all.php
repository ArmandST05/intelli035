<?php
$conn = Database::getCon();

// Consulta para obtener todas las empresas
$sql = "SELECT id, nombre FROM empresas";

// Ejecutar la consulta
$query = mysqli_query($conn, $sql);

// Verificar si hay resultados
$empresas = array();
while ($row = mysqli_fetch_assoc($query)) {
    $empresas[] = array(
        'id' => $row['id'],
        'nombre' => $row['nombre']
    );
}

// Retornar la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($empresas);
?>
