<?php
/// en caso de que el parametro action/api este definido evitamos que se muestre
/// el layout por defecto y ejecutamos el action/api sin mostrar nada de vista
if(isset($_GET["action"])){
	Action::load($_GET["action"]);
}
else if(isset($_GET["api"])){
	Api::load($_GET["api"]);
}
else{
//	Bootload::load("default");
	Module::loadLayout("index");
}
?>