<?php 
class DepartamentoData{
    public static $tablename = "departamentos";

    


    public static function getAll(){
    $sql = "SELECT * FROM ".self::$tablename;
    $query = Executor::doit($sql);
    return Model::many($query[0], new DepartamentoData());
    }

    
}



?>