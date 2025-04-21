<?php
if(count($_POST)>0){
	$workshift = WorkshiftData::getById($_POST["id"]);
	$workshift->name = strtoupper($_POST["name"]);
	$workshift->color = $_POST["color"];
	//$workshift->is_active = ((isset($_POST["isActive"])) ? 1 : 0);

	if($workshift->update()){
	}else{
		Core::alert("Ocurrió un error al actualizar.");
	}
	
	print "<script>window.location='index.php?view=workshifts/index';</script>";
}
?>