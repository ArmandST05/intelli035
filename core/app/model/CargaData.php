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
        $stmt = $this->db->prepare("INSERT INTO departamentos (nombre) VALUES (?)");
    
        if (!$stmt) {
            echo "❌ Error en prepare (departamento): " . $this->db->error . "<br>";
            return null;
        }
    
        $stmt->bind_param("s", $nombreDepartamento);
    
        if ($stmt->execute()) {
            $idDepartamento = $stmt->insert_id;
        } else {
            echo "❌ Error al insertar departamento ($nombreDepartamento): " . $stmt->error . "<br>";
            $idDepartamento = null;
        }
    
        $stmt->close();
        return $idDepartamento;
    }
    public function insertPosition($nombrePuesto, $idDepartamento) {
        $stmt = $this->db->prepare("INSERT INTO puestos (nombre, id_departamento) VALUES (?, ?)");
    
        if (!$stmt) {
            echo "❌ Error en prepare (puesto): " . $this->db->error . "<br>";
            return;
        }
    
        $stmt->bind_param("si", $nombrePuesto, $idDepartamento);
    
        if (!$stmt->execute()) {
            echo "❌ Error al insertar puesto ($nombrePuesto): " . $stmt->error . "<br>";
        }
    
        $stmt->close();
    }
    public function insertOrGetDepartamento($nombre) {
        $stmt = $this->db->prepare("SELECT idDepartamento FROM departamentos WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['idDepartamento'];
        } else {
            $stmt = $this->db->prepare("INSERT INTO departamentos (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombre);
            $stmt->execute();
            return $this->db->insert_id;
        }
    }
    
    public function insertOrGetPuesto($nombre, $departamento_id) {
        $stmt = $this->db->prepare("SELECT id FROM puestos WHERE nombre = ? AND id_departamento = ?");
        $stmt->bind_param("si", $nombre, $departamento_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            return $row['id'];
        } else {
            $stmt = $this->db->prepare("INSERT INTO puestos (nombre, id_departamento) VALUES (?, ?)");
            $stmt->bind_param("si", $nombre, $departamento_id);
            $stmt->execute();
            return $this->db->insert_id;
        }
    }
    
    
    

// Insertar personal
public function insertIntoPersonal($nombre, $id_puesto, $id_departamento, $correo, $telefono, $usuario, $clave, $fecha_alta) {
    $stmt = $this->db->prepare("INSERT INTO personal (nombre, id_puesto, id_departamento, correo, telefono, usuario, clave, fecha_alta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $stmt->execute([$nombre, $id_puesto, $id_departamento, $correo, $telefono, $usuario, $clave, $fecha_alta]);
}

    
}
?>
