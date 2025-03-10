<?php
// 15 de Diciembre del 2021
// Api.php
// @brief Un api corresponde a una rutina de un módulo.

class Api {
	/**
	* @function load
	* @brief la funcion load carga una vista correspondiente a un módulo
	**/	
	public static function load($api){

		if(!isset($_GET['api'])){
			include "core/app/api/".$api."-api.php";
		}else{
			if(Api::isValid()){
				include "core/app/api/".$_GET['api']."-api.php";				
			}else{
				$response["error"] = true;
				$response["message"] = "Este recurso no existe";
				return http_response_code(404);			}
		}
	}

	/**
	* @function isValid
	* @brief valida la existencia de una vista
	**/	
	public static function isValid(){
		$valid=false;
		if(file_exists($file = "core/app/api/".$_GET['api']."-api.php")){
			$valid = true;
		}
		return $valid;
	}

	public static function Error($message){
		print $message;
	}

	public function execute($api,$params){
		$fullpath =  "core/app/api/".$api."-api.php";
		if(file_exists($fullpath)){
			include $fullpath;
		}else{
			assert("wtf");
		}
	}

}

?>