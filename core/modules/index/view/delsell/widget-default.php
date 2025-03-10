<?php

$sell = OperationData::getById($_GET["id"]);
$operations = OperationDetailData::getAllProductsByOperationId($_GET["id"]);

foreach ($operations as $op) {
	$op->del();
}

$sell->del();
Core::redir("./index.php?view=sells");

?>