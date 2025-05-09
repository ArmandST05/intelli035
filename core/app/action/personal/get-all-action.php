<?php
$conn = Database::getCon();
$user_id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;

// Verificar el tipo de usuario
$user = UserData::getLoggedIn();
$user_type = $user->user_type;

// DataTables request
$requestData = $_REQUEST;

// Columnas
$columns = array(
    0 => 'id',
    1 => 'nombre',
    2 => 'id_departamento',
    3 => 'empresa',
    4 => 'usuario',
    5 => 'clave',
    6 => 'correo',
    7 => 'telefono'
);

// Filtros personalizados
$department_filter = isset($requestData['department_filter']) ? $requestData['department_filter'] : '';
$company_filter = isset($requestData['company_filter']) ? $requestData['company_filter'] : ''; // Nuevo filtro
$custom_search = isset($requestData['custom_search']) ? $requestData['custom_search'] : '';
$custom_length = isset($requestData['length']) ? intval($requestData['length']) : 10;

// Consulta base
$sql = "SELECT personal.id, personal.nombre, personal.usuario, personal.clave, personal.correo, personal.telefono, 
        departamentos.nombre AS departamento,
        empresas.nombre AS empresa
        FROM personal
        INNER JOIN departamentos ON personal.id_departamento = departamentos.idDepartamento
        INNER JOIN empresas ON personal.empresa_id = empresas.id";

$where = [];

// Filtro de búsqueda general
if (!empty($custom_search)) {
    $searchValue = mysqli_real_escape_string($conn, $custom_search);
    $where[] = "(personal.nombre LIKE '%$searchValue%' OR empresas.nombre LIKE '%$searchValue%')";
}

// Filtro por departamento
if (!empty($department_filter)) {
    $where[] = "personal.id_departamento = " . mysqli_real_escape_string($conn, $department_filter);
}

// 🔸 Filtro por empresa
if (!empty($company_filter)) {
    $where[] = "personal.empresa_id = " . mysqli_real_escape_string($conn, $company_filter);
}

// Agregar condiciones al SQL
if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Total filtrado
$query = mysqli_query($conn, $sql);
$totalFiltered = mysqli_num_rows($query);

// Ordenamiento y paginación
$orderColumn = isset($requestData['order'][0]['column']) ? $columns[$requestData['order'][0]['column']] : 'id';
$orderDirection = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'ASC';
$start = isset($requestData['start']) ? intval($requestData['start']) : 0;

$sql .= " ORDER BY $orderColumn $orderDirection LIMIT $start, $custom_length";

// error_log($sql); // opcional para debug

$query = mysqli_query($conn, $sql);

// Construcción de respuesta
$data = array();
while ($row = mysqli_fetch_assoc($query)) {
    $nestedData = array();
    $nestedData[] = $row["id"];
    $nestedData[] = $row["nombre"];
    $nestedData[] = $row["departamento"];
    $nestedData[] = $row["empresa"];
    $nestedData[] = $row["usuario"];
    $nestedData[] = $row["clave"];
    $nestedData[] = $row["correo"];
    $nestedData[] = $row["telefono"];
    
    $buttons = '
    <style>
        #dropdownMenuButton' . $row["id"] . ' i {
            background-color: grey;
            color: black;
            border-radius: 50%;
            padding: 6px;
            font-size: 18px;
            display: inline-block;
            text-align: center;
            line-height: 18px;
        }
        .table .dropdown { position: relative; text-align: center; }
        .dropdown-menu {
            position: absolute;
            z-index: 10000;
            display: none;
            width: 250px;
            background-color: white;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            left: auto;
            right: 0;
        }
        .dropdown-toggle { background-color: transparent; border: none; }
        td, th { height: 60px; border: 1px solid grey; }
    </style>
    <div class="dropdown">
        <button class="btn btn-link dropdown-toggle" type="button" id="dropdownMenuButton' . $row["id"] . '">
            <i class="fa-solid fa-ellipsis" style="color: black;"></i>
        </button>
        <ul class="dropdown-menu" id="dropdownMenu' . $row["id"] . '" style="display: none; position: absolute;">
            <li><a class="dropdown-item" href="#" onclick="editPersonal(' . $row["id"] . ')">Editar</a></li>
            <li><a class="dropdown-item" href="#" onclick="deletePersonal(' . $row["id"] . ',`' . $row["nombre"] . '`)">Eliminar</a></li>
            <li><a class="dropdown-item" href="#" onclick="openAssignSurveyModal(' . $row["id"] . ')">Asignar Encuesta</a></li>
            <hr>
            <li><a class="dropdown-item" href="#" onclick="sendMail(' . $row["id"] . ')">Enviar credenciales por correo</a></li>
            <li><a class="dropdown-item" href="#" onclick="sendWhatsapp(' . $row["id"] . ')">Enviar credenciales por Whatsapp</a></li>
        </ul>
    </div>
    ';

    $nestedData[] = $buttons;
    $data[] = $nestedData;
}

// Respuesta final
$response = array(
    "draw" => intval($requestData['draw']),
    "recordsTotal" => intval($totalFiltered),
    "recordsFiltered" => intval($totalFiltered),
    "data" => $data
);

header('Content-Type: application/json');
echo json_encode($response);
?>
