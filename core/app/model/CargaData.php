<?php
class CargaData {
    private $db;
    private $empresa_id;

    public function __construct() {
        $this->db = Database::getCon(); 
        $this->empresa_id = "";  // Guardamos el empresa_id pasado al constructor
    }
    // Función para limpiar los datos
public function cleanData($data) {
    // Elimina los espacios al principio y al final de cada valor
    return array_map(function($value) {
        $value = trim($value);
        $value = preg_replace('/\s+/', ' ', $value);  // Reemplazar espacios múltiples por uno solo
        return $value;
    }, $data);
}

    public function setEmpresaId($empresa_id) {
        $this->empresa_id = $empresa_id;  // Permite cambiar el empresa_id en cualquier momento
    }
    public function insertFileToDatabase($fileName, $fileType, $fileData) {
        // Preparar la consulta para insertar el archivo
        $stmt = $this->db->prepare("INSERT INTO archivos (nombre_archivo, tipo_archivo, contenido) VALUES (?, ?, ?)");
        
        // Asegurarte de que el tipo de dato es adecuado para la columna de contenido (BLOB o LONGBLOB)
        $stmt->bind_param("sss", $fileName, $fileType, $fileData);
        
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            echo "Error al guardar el archivo: " . $stmt->error;
        }
    
        $stmt->close();
    }
    
    
    public function getPositions() {
        // Ejecutamos la consulta directamente
        $sql = "SELECT id, nombre FROM puestos";
        $con = Database::getCon();

        $positions = [];
        if ($query = $con->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $positions[strtoupper($row['nombre'])] = $row['id'];
            }
        }

        return $positions;
    }

    public function getDepartments() {
        // Ejecutamos la consulta directamente
        $sql = "SELECT idDepartamento, nombre FROM departamentos";
        $con = Database::getCon();

        $departments = [];
        if ($query = $con->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $departments[strtoupper($row['nombre'])] = $row['idDepartamento'];
            }
        }

        return $departments;
    }

    public function insertIntoDatabase($nombre, $id_puesto, $id_departamento, $correo, $telefono, $usuario, $clave, $fecha_alta) {
       
        $sql = "INSERT INTO personal (nombre, id_puesto, id_departamento, correo, telefono, usuario, clave, fecha_alta, empresa_id) 
                VALUES (\"$nombre\", \"$id_puesto\", \"$id_departamento\", \"$correo\", \"$telefono\", \"$usuario\", \"$clave\", \"$fecha_alta\", \"$this->empresa_id\")";
        
        return Executor::doit($sql);
    }
    public function insertDepartment($nombreDepartamento) {
        $stmt = $this->db->prepare("INSERT INTO departamentos (nombre) VALUES (?) ON DUPLICATE KEY UPDATE idDepartamento=LAST_INSERT_ID(idDepartamento)");
        $stmt->bind_param("s", $nombreDepartamento);
        $stmt->execute();
        $idDepartamento = $stmt->insert_id;
        $stmt->close();
        return $idDepartamento;
    }
    
    public function insertPosition($nombrePuesto, $idDepartamento) {
        $stmt = $this->db->prepare("INSERT INTO puestos (nombre, idDepartamento) VALUES (?, ?)");
        $stmt->bind_param("si", $nombrePuesto, $idDepartamento);
        $stmt->execute();
        $stmt->close();
    }
    
}
?>
