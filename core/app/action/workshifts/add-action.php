<?php
if(count($_POST)>0){
	$workshift = new WorkshiftData();
	$workshift->name = strtoupper($_POST["name"]);
	$workshift->color = $_POST["color"];
	$newWorkshift = $workshift->add();

	if($newWorkshift && $newWorkshift[1]){
	}else{
		Core::alert("Ocurrió un error al agregar.");
	}
	print "<script>window.location='index.php?view=workshifts/index';</script>";
}
?>