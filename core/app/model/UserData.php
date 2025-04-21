<?php
class UserData {
	public static $tablename = "users";

	public function __construct(){
		$this->name = "";
		$this->is_active = "";
		$this->lastname = "";
		$this->email = "";
		$this->image = "";
		$this->password = "";
		$this->created_at = "NOW()";
	}

	public function getUserType(){ 
		return UserTypeData::getById($this->user_type); 
	}

	public function add(){
		$sql = "insert into ".self::$tablename." (name,username,password,created_at,user_type) ";
		$sql .= "value (\"$this->name\",\"$this->username\",\"$this->password\",$this->created_at,\"$this->user_type\")";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "update ".self::$tablename." set name=\"$this->name\",username=\"$this->username\",user_type=\"$this->user_type\" WHERE id=$this->id";
		Executor::doit($sql);
	}

	public function updatePassword(){
		$sql = "update ".self::$tablename." set password=\"$this->password\" where id = $this->id";
		Executor::doit($sql);
	}

	public static function getById($id){
		$sql = "select * from ".self::$tablename." where id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}

	

	public static function getByMail($mail){
		$sql = "select * from ".self::$tablename." WHERE user_type != 'api' AND email = \"$mail\"";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}

	public static function validateApi($username,$authenticationToken){
		$sql = "SELECT * FROM ".self::$tablename." 
		WHERE username = '$username' AND authentication_token = '$authenticationToken'
		LIMIT 1";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}

	public static function getAll(){
		$sql = "SELECT * FROM ".self::$tablename ." WHERE user_type != 'api' order by name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}

	public static function getByUserTypeStatus($userType,$status){
		$sql = "SELECT * FROM ".self::$tablename ." WHERE user_type != 'api' ";
		if($userType != 0){
			$sql .= " AND user_type = '$userType' ";
		}
		if($status != 'all'){
			$sql .= " AND is_active = '$status' ";
		}
		$sql .= " order by user_type,name ASC ";

		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}


	public static function getUnassigned(){
		//Obtiene los usuarios que no se han asignado a ningún médico.
		$sql = "select ".self::$tablename .".* from ".self::$tablename ." 
		left join medics on ".self::$tablename .".id = medics.user_id 
		WHERE user_type != 'api' AND medics.id is null 
		order by ".self::$tablename .".name ASC";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}

	public static function getLike($q){
		$sql = "select * from ".self::$tablename." WHERE user_type != 'api' AND name like '%$q%'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new UserData());
	}

	public static function deleteById($id){
		$sql = "delete from ".self::$tablename." WHERE id=$id";
		Executor::doit($sql);
	}
	public function changeStatusById(){
		$sql = "update ".self::$tablename." set is_active=\"$this->status_id\" where id=$this->id";
		Executor::doit($sql);
	}
	public static function getLoggedIn(){
		//Obtener datos del usuario logueado en el sistema
		$id = isset($_SESSION["user_id"])  ? $_SESSION["user_id"] : null;
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new UserData());
	}



	public static function getLoggedInNurse() {
		$id = isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : null;
		$sql = "SELECT *, user_type as role FROM users WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::one($query[0], new UserData());
	}
	
	
}
?>