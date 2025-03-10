<?php 
class EmpresaData {
    public static $tablename = "empresas";
    public static $tablecantidad = "cantidad_empleados";

    public function __construct() {
        $this->nombre = "";
        $this->comentarios = "";
        $this->id_cantidad = "";
        $this->logo = ""; // Nuevo campo para la ruta del logo
    } 

    // Agregar una empresa con opción de logo
    public function add() {
        $sql = "INSERT INTO " . self::$tablename . " (nombre, comentarios, id_cantidad, logo) VALUES 
        (\"$this->nombre\", \"$this->comentarios\", \"$this->id_cantidad\", \"$this->logo\")";
        return Executor::doit($sql);
    }
    public static function getAllDepartment(){
        $sql = "SELECT * FROM ".self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0], new DepartamentoData());
        }
    public static function getCantidades() {
        $sql = "SELECT * FROM " . self::$tablecantidad;
        $query = Executor::doit($sql);
        return Model::many($query[0], new PersonalData());
    }

    public static function delete($id) {
        $sql = "DELETE FROM " . self::$tablename . " WHERE id = $id";
        Executor::doit($sql);
    }

    // Actualizar empresa incluyendo el logo si se proporciona
    public function update() {
        try {
            $sql = "UPDATE " . self::$tablename . " 
                    SET 
                        nombre = \"$this->nombre\",
                        comentarios = \"$this->comentarios\",
                        id_cantidad = \"$this->id_cantidad\",
                        logo = \"$this->logo\"
                    WHERE id = $this->id";
    
            Executor::doit($sql);
            return true; 
        } catch (Exception $e) {
            return false;
        }
    }

    // Método específico para actualizar solo el logo
    public function updateLogo($logoPath) {
        $sql = "UPDATE " . self::$tablename . " SET logo = \"$logoPath\" WHERE id = $this->id";
        Executor::doit($sql);
    }

    public static function getById($id) {
        $sql = "SELECT * FROM " . self::$tablename . " WHERE id = '$id'";
        $query = Executor::doit($sql);
        return Model::one($query[0], new EmpresaData());
    }

    public static function getByCantidad($id) {
        $sql = "SELECT empresas.*, cantidad_empleados.descripcion AS cantidad_descripcion 
                FROM empresas 
                INNER JOIN cantidad_empleados ON empresas.id_cantidad = cantidad_empleados.id
                WHERE empresas.id = '$id'";

        $query = Executor::doit($sql);
        return Model::one($query[0], new EmpresaData());
    }

    public static function getAll() {
        $sql = "SELECT empresas.*, cantidad_empleados.descripcion AS cantidad_descripcion, empresas.logo 
                FROM empresas 
                INNER JOIN cantidad_empleados ON empresas.id_cantidad = cantidad_empleados.id";
        $query = Executor::doit($sql);
        return Model::many($query[0], new EmpresaData());
    }
}
?>
