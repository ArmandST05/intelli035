<?php
//Desarrollado por Jesus Liñán
//ribosomatic.com
//Puedes hacer lo que quieras con el código
//pero visita la web cuando te acuerdes

//Configuracion de la conexion a base de datos
/*$bd_host = "localhost"; 
$bd_usuario = "root"; 
$bd_password = "admin"; 
$bd_base = "sistemal_emr"; */
$bd_host = "localhost"; 
$bd_usuario = "sistemal_emr"; 
$bd_password = "emr12#techno0"; 
$bd_base = "sistemal_emr"; 


$con = mysql_connect($bd_host, $bd_usuario, $bd_password); 

mysql_select_db($bd_base, $con); 

//consulta todos los empleados
$valor=$_GET["valor"];
//$sql=mysql_query("SELECT * FROM pacient WHERE id>='1' AND id<='3'",$con);
$sql=mysql_query("SELECT * FROM pacient WHERE DATE_FORMAT(fecha_na,'%m-%d')='$valor'",$con);
//muestra los datos consultados
while($row = mysql_fetch_array($sql)){
	echo "<li>".$row['email'].","."</li> \n";
}
?>