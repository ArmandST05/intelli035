<?php
// Conexión a la base de datos
$conn = Database::getCon();

// Obtener datos enviados por POST
$departmentId = isset($_POST['department_id']) ? intval($_POST['department_id']) : null;
$roleId = isset($_POST['role_id']) ? intval($_POST['role_id']) : null;

// Consulta base
$sql = "SELECT 
            personal.id, 
            personal.nombre, 
            departamentos.nombre AS departamento, 
            puestos.nombre AS puesto
        FROM 
            personal
        INNER JOIN 
            departamentos ON personal.id_departamento = departamentos.idDepartamento
        INNER JOIN 
            puestos ON personal.id_puesto = puestos.id";

// Construir condiciones dinámicas según filtros
$where = [];

if ($departmentId) {
    $where[] = "personal.id_departamento = " . mysqli_real_escape_string($conn, $departmentId);
}

if ($roleId) {
    $where[] = "personal.id_puesto = " . mysqli_real_escape_string($conn, $roleId);
}

// Agregar condiciones a la consulta
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Ejecutar consulta
$result = mysqli_query($conn, $sql);

// Verificar errores en la consulta
if (!$result) {
    error_log("Error en la consulta: " . mysqli_error($conn));
    header('Content-Type: application/json');
    echo json_encode(["error" => "Error al obtener datos"]);
    exit;
}

// Construir respuesta en formato JSON
$data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = [
        "id" => $row["id"],
        "name" => $row["nombre"],
        "department" => $row["departamento"],
        "role" => $row["puesto"]
    ];
}

// Enviar respuesta JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
