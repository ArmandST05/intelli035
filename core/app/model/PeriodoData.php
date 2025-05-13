<?php
class PeriodoData {

    public static $tablename = "periods";
    // Constructor para el modelo
    public function __construct()
    {
        $this->name = "";
        $this->start_date = "";
        $this->end_date = "";
        $this->status = ""; 
        $this->empresa_id = "";
    }
    public function add() {    
        $sql = "INSERT INTO " . self::$tablename . " 
                (name, start_date, end_date, status, empresa_id) 
                VALUES (\"$this->name\", \"$this->start_date\", \"$this->end_date\", \"$this->status\", \"$this->empresa_id\")";
        Executor::doit($sql);
    }
    
    
    // Obtener un periodo por ID
    public static function getById($period_id) {
        $sql = "SELECT * FROM ".self::$tablename." WHERE id = '$period_id'";
        $query = Executor::doit($sql);
        return Model::one($query[0], new PeriodoData());
    }

    // Actualizar los datos de un periodo
    public function update() {
        try {
            $sql = "UPDATE " . self::$tablename . " 
                    SET 
                        name = \"$this->name\",
                        start_date = \"$this->start_date\",
                        end_date = \"$this->end_date\",
                        status = \"$this->status\"
                    WHERE id = $this->id";
            Executor::doit($sql);
            return true; // Si se actualiza correctamente
        } catch (Exception $e) {
            return false; // En caso de error
        }
    }

    // Obtener todos los periodos
    public static function getAll() {
        $sql = "SELECT * FROM ".self::$tablename;
        $query = Executor::doit($sql);
        return Model::many($query[0], new PeriodoData());
    }
public static function getAllWithEmpresa(){
    $sql = "SELECT periods.*, empresas.nombre AS empresa_nombre 
            FROM periods 
            INNER JOIN empresas ON periods.empresa_id = empresas.id";
    $query = Executor::doit($sql);
    return Model::many($query[0], new PeriodoData());
}
    // Eliminar un periodo por ID
    public static function delete($idPeriodo) {
        $sql = "DELETE FROM periods WHERE id = $idPeriodo";
        Executor::doit($sql);
    }
    


}  
