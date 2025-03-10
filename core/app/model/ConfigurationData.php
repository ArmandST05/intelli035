<?php
class ConfigurationData {
	public static $tablename = "configuration";
	public $id;
	public $name;
	public $description;
	public $kind;
	public $value;

	public function __construct(){
		$this->name = "";
		$this->description = "";
		$this->kind = "";
		$this->value = "";
	}

	//GENERAL FUNCTIONS
	public static function deleteDirectory($dirPath) {
		if (! is_dir($dirPath)) {
			throw new InvalidArgumentException("$dirPath must be a directory");
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDirectory($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}

	public static function formatFileName($string)
	{
		//Sustituir caracteres no permitidos en un nombre de archivo, al guardar las imágenes
		$string = str_replace(".", "", $string);
		$string = str_replace(" ", "", $string);

		//Reemplazamos la A y a
		$string = str_replace(
			array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
			array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
			$string
		);

		//Reemplazamos la E y e
		$string = str_replace(
			array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
			array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
			$string
		);

		//Reemplazamos la I y i
		$string = str_replace(
			array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
			array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
			$string
		);

		//Reemplazamos la O y o
		$string = str_replace(
			array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
			array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
			$string
		);

		//Reemplazamos la U y u
		$string = str_replace(
			array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
			array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
			$string
		);

		//Reemplazamos la N, n, C y c
		$string = str_replace(
			array('Ñ', 'ñ', 'Ç', 'ç'),
			array('N', 'n', 'C', 'c'),
			$string
		);

		return $string;
	}


	public function add(){
		$sql = "INSERT INTO ".self::$tablename." (name,description,kind,value) ";
		$sql .= "value (\"$this->name\",\"$this->description\",\"$this->kind\",\"$this->value\")";
		Executor::doit($sql);
	}

	public function update(){
		$sql = "UPDATE ".self::$tablename." set value=\"$this->value\" WHERE id = $this->id";
		Executor::doit($sql);
	}

	public static function delete($id){
		$sql = "DELETE FROM ".self::$tablename." WHERE id = $id";
		Executor::doit($sql);
	}

	public static function getAll(){
		//Crear array con el nombre de la configuración para acceder a los datos con mayor facilidad.
		$sql = "SELECT * FROM ".self::$tablename;
		$query = Executor::doit($sql);
		$array = array();
		while($r = $query[0]->fetch_array()){
			$array[$r['name']] = new ConfigurationData();
			$array[$r['name']]->id = $r['id'];
			$array[$r['name']]->name = $r['name'];
			$array[$r['name']]->description = $r['description'];
			$array[$r['name']]->kind = $r['kind'];
			$array[$r['name']]->value = $r['value'];
		}
		return $array;
	}

	public static function getById($id){
		$sql = "SELECT * FROM ".self::$tablename." WHERE id = '$id'";
		$query = Executor::doit($sql);
		return Model::many($query[0],new ConfigurationData());
	}

	public static function getByName($name){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name = '$name'";
		$query = Executor::doit($sql);
		return Model::one($query[0],new ConfigurationData());
	}

	public static function getLike($q){
		$sql = "SELECT * FROM ".self::$tablename." WHERE name like '%$q%'";
		$query = Executor::doit($sql);
		$array = array();
		$cnt = 0;
		while($r = $query[0]->fetch_array()){
			$array[$cnt] = new ConfigurationData();
			$array[$cnt]->id = $r['id'];
			$array[$cnt]->name = $r['name'];
			$array[$cnt]->mail = $r['mail'];
			$array[$cnt]->password = $r['password'];
			$array[$cnt]->created_at = $r['created_at'];
			$cnt++;
		}
		return $array;
	}
}
