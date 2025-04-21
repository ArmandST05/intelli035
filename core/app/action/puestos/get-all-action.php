<?php
$conn = Database::getCon();
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Obtener tipo de usuario
$user = UserData::getLoggedIn();
$user_type = $user->user_type;

// DataTables request
$requestData = $_REQUEST;

$columns = array(
    0 => "id",
    1 => "nombre",
    2 => "id_departamento"
);

// Consulta base
$sql = "SELECT puestos.id, puestos.nombre, puestos.id_departamento,
        departamentos.nombre AS departamento
        FROM puestos
        INNER JOIN departamentos ON puestos.id_departamento = departamentos.idDepartamento";

// Filtro de búsqueda
if (!empty($requestData['search']['value'])) {
    $searchValue = mysqli_real_escape_string($conn, $requestData['search']['value']);
    $sql .= " WHERE (puestos.nombre LIKE '%$searchValue%')";
}

// Contar registros filtrados
$queryFiltered = mysqli_query($conn, $sql);
$totalFiltered = mysqli_num_rows($queryFiltered);

// Ordenamiento y paginación
$orderColumn = isset($columns[$requestData['order'][0]['column']]) ? $columns[$requestData['order'][0]['column']] : 'id';
$orderDirection = in_array(strtoupper($requestData['order'][0]['dir']), ['ASC', 'DESC']) ? $requestData['order'][0]['dir'] : 'ASC';
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;
$length = isset($requestData['length']) ? intval($requestData['length']) : 10;

$sql .= " ORDER BY $orderColumn $orderDirection LIMIT $start, $length";
$query = mysqli_query($conn, $sql);

// Preparar datos para DataTables
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $nestedData = array();
    $nestedData[] = $row['id'];
    $nestedData[] = $row['nombre'];
    $nestedData[] = $row['departamento'];

    $buttons = '
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton' . $row["id"] . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bi bi-three-dots"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownMenu' . $row["id"] . '" style="display: none; position: absolute;">
            <li><a class="dropdown-item" href="#" onclick="editPuesto(' . $row["id"] . ')">Editar</a></li>
            <li><a class="dropdown-item" href="#" onclick="deletePuesto(' . $row["id"] . ', \'' . $row["nombre"] . '\')">Eliminar</a></li>
        </ul>
    </div>';

    $nestedData[] = $buttons;

    $data[] = $nestedData;
}

// Respuesta final
$response = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalFiltered), // Total registros filtrados
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
