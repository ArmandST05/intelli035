<?php

if(count($_POST)>0){
	$user = UserData::getById($_POST["user_id"]);
	$user->name = $_POST["name"];
	$user->username = $_POST["username"];
	$user->user_type = $_POST["user_type"];
	$user->update();

	if($_POST["password"]!=""){
		$user->password = sha1(md5($_POST["password"]));
		$user->updatePassword();
		print "<script>alert('Se ha actualizado la contraseña');</script>";

	}
	print "<script>window.location='index.php?view=users/index';</script>";
}


?>