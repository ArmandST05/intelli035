<?php
class UserTypeData {
	public static $tablename = "user_types";

	public function __construct(){
		$this->name = "";
		$this->created_at = "NOW()";
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserTypeData());
	}

	public static function getByDescription($name){
		$sql = "select * from ".self::$tablename." where description = '$name'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserTypeData());
	}

	public static function getAll(){
		$sql = "select * from ".self::$tablename ." order by description";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserTypeData());
	}
}
?>