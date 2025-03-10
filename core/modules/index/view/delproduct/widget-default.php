<?php

$operations = OperationDetailData::getAllByProductId($_GET["id"]);

foreach ($operations as $op) {
	$op->del();
}

$product = ProductData::getById($_GET["id"]);
$product->del();

Core::redir("./index.php?view=products");
?>