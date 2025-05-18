<?php
$conn = Database::getCon();
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Obtener tipo de usuario (no usado aquí, pero lo dejas)
$user = UserData::getLoggedIn();
$user_type = $user->user_type ?? null;

// DataTables request
$requestData = $_REQUEST;

// Columnas - deben coincidir con los campos que envías al frontend
$columns = [
    0 => 'personal.id',
    1 => 'personal.nombre',
    2 => 'departamentos.nombre',
    3 => 'empresas.nombre',
    4 => 'personal.usuario',
    5 => 'personal.clave',
    6 => 'personal.correo',
    7 => 'personal.telefono'
];

// Filtros personalizados
$department_filter = $requestData['department_filter'] ?? '';
$company_filter = $requestData['company_filter'] ?? '';
$custom_search = $requestData['custom_search'] ?? '';
$custom_length = isset($requestData['length']) ? intval($requestData['length']) : 10;
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;

// --- Construcción base de la consulta con parámetros ---
$sqlBase = "
    FROM personal
    INNER JOIN departamentos ON personal.id_departamento = departamentos.idDepartamento
    INNER JOIN empresas ON personal.empresa_id = empresas.id
";

// Array para parámetros y tipos para sentencia preparada
$whereClauses = [];
$params = [];
$paramTypes = "";

// Búsqueda general
if (!empty($custom_search)) {
    $whereClauses[] = "(personal.nombre LIKE ? OR empresas.nombre LIKE ?)";
    $likeSearch = "%{$custom_search}%";
    $params[] = $likeSearch;
    $params[] = $likeSearch;
    $paramTypes .= "ss";
}

// Filtro departamento numérico
if (!empty($department_filter) && is_numeric($department_filter)) {
    $whereClauses[] = "personal.id_departamento = ?";
    $params[] = intval($department_filter);
    $paramTypes .= "i";
}

// Filtro empresa numérico
if (!empty($company_filter) && is_numeric($company_filter)) {
    $whereClauses[] = "personal.empresa_id = ?";
    $params[] = intval($company_filter);
    $paramTypes .= "i";
}

// Construir WHERE si hay filtros
$whereSql = "";
if (count($whereClauses) > 0) {
    $whereSql = " WHERE " . implode(" AND ", $whereClauses);
}

// --- Conteo total de registros sin filtro ---
$sqlCountTotal = "SELECT COUNT(*) AS total FROM personal";
$resultTotal = $conn->query($sqlCountTotal);
$totalRecords = 0;
if ($resultTotal) {
    $rowTotal = $resultTotal->fetch_assoc();
    $totalRecords = intval($rowTotal['total']);
}

// --- Conteo total con filtros ---
$sqlCountFiltered = "SELECT COUNT(*) AS total_filtered " . $sqlBase . $whereSql;
$stmtCount = $conn->prepare($sqlCountFiltered);
if ($stmtCount && strlen($paramTypes) > 0) {
    $stmtCount->bind_param($paramTypes, ...$params);
}
$stmtCount->execute();
$resultFiltered = $stmtCount->get_result();
$totalFiltered = 0;
if ($resultFiltered) {
    $rowFiltered = $resultFiltered->fetch_assoc();
    $totalFiltered = intval($rowFiltered['total_filtered']);
}
$stmtCount->close();

// --- Orden y paginación ---
$orderColumnIndex = $requestData['order'][0]['column'] ?? 0;
$orderDir = strtoupper($requestData['order'][0]['dir'] ?? 'ASC');

// Validar orden columna y dirección
$orderColumn = $columns[$orderColumnIndex] ?? 'personal.id';
$orderDir = ($orderDir === 'ASC' || $orderDir === 'DESC') ? $orderDir : 'ASC';

// Consulta final con datos
$sqlData = "
    SELECT personal.id, personal.nombre, personal.usuario, personal.clave, personal.correo, personal.telefono,
           departamentos.nombre AS departamento,
           empresas.nombre AS empresa
    " . $sqlBase . $whereSql . " 
    ORDER BY $orderColumn $orderDir
    LIMIT ?, ?
";

$stmtData = $conn->prepare($sqlData);

if (!$stmtData) {
    // Error al preparar
    http_response_code(500);
    echo json_encode(['error' => 'Error en consulta SQL']);
    exit;
}

// Añadir parámetros para límite y offset
// bind_param requiere que todos los parámetros estén antes, así que creamos un array
$paramsData = $params;
$paramTypesData = $paramTypes . "ii";
$paramsData[] = $start;
$paramsData[] = $custom_length;

// Bind params dinámicamente
$stmtData->bind_param($paramTypesData, ...$paramsData);
$stmtData->execute();
$resultData = $stmtData->get_result();

// Construcción de datos para DataTables
$data = [];

while ($row = $resultData->fetch_assoc()) {
    $checkbox = '<input type="checkbox" class="row-select" value="' . htmlspecialchars($row['id']) . '">';
    $nestedData = [];
    $nestedData[] = $checkbox;
    $nestedData[] = $row["id"];
    $nestedData[] = htmlspecialchars($row["nombre"]);
    $nestedData[] = htmlspecialchars($row["departamento"]);
    $nestedData[] = htmlspecialchars($row["empresa"]);
    $nestedData[] = htmlspecialchars($row["usuario"]);
    $nestedData[] = htmlspecialchars($row["clave"]);
    $nestedData[] = htmlspecialchars($row["correo"]);
    $nestedData[] = htmlspecialchars($row["telefono"]);



    $data[] = $nestedData;
}

$stmtData->close();

// Respuesta JSON para DataTables
$response = [
    "draw" => intval($requestData['draw'] ?? 0),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
exit;
?>
