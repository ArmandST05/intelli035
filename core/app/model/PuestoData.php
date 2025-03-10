<?php 

class PuestoData{
    public static $tablename = "puestos";

    public function __construct(){
        $this -> nombre = "";
        $this -> id_departamento = "";
        $this -> id_encuesta = "";
    }

    public function add(){
        $sql = "INSERT INTO " . self::$tablename . " (nombre, id_departamento, id_encuesta) 
        VALUES (\"$this->nombre\", \"$this->id_departamento\", $this->id_encuesta)";


        return Executor::doit($sql);
    }


    public static function getAll() {
        $sql = "
            SELECT 
                puestos.*, 
                departamentos.nombre AS nombre_departamento 
            FROM 
                puestos
            INNER JOIN 
                departamentos 
            ON 
                puestos.id_departamento = departamentos.idDepartamento";
        $query = Executor::doit($sql);
        return Model::many($query[0], new DepartamentoData());
    }
    public static function getById($idPuesto){
        $id = intval($idPuesto); // Asegurar que sea un número entero
        $sql = "SELECT * FROM " . self::$tablename . " WHERE id = $idPuesto";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PuestoData());
    }
    
    public static function delete($id) {
        // Elimina el puesto por el ID
        $sql = "DELETE FROM puestos WHERE id = $id";
        Executor::doit($sql);
    }
    public static function getByDepartment($id_departamento) {
        $sql = "SELECT * FROM puestos WHERE id_departamento = $id_departamento";
        $query = Executor::doit($sql);
        return Model::many($query[0], new PuestoData());
    }
    public function update() {
        try {
            $sql = "UPDATE " . self::$tablename . " 
                    SET 
                        nombre = \"$this->nombre\",
                        id_departamento = \"$this->id_departamento\",
                        id_encuesta = \"$this->id_encuesta\"
                    WHERE id = $this->id";
    
            Executor::doit($sql);
            return true; // Si la actualización es exitosa.
        } catch (Exception $e) {
            return false; // En caso de error.
        }
    }
    
}

?>