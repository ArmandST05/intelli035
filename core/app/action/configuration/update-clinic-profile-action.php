<?php
if (count($_POST) > 0) {
	foreach ($_POST["configuration"] as $index => $value) {
		$configuration = ConfigurationData::getByName($index);
		$configuration->value = $value;
		$configuration->update();
	}

	if (!isset($_POST["configuration"]["active_card_commission"])) {
		$configuration = ConfigurationData::getByName("active_card_commission");
		$configuration->value = 0;
		$configuration->update();
	}


	if (isset($_FILES["logo"]) && ($_FILES["logo"]["size"] > 0)) {
		$targetFile = "assets/clinic-logo.png";
		unlink($targetFile);
		$configuration = ConfigurationData::getByName('logo');

		if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
			$configuration->value = "clinic-logo.png";
		} else {
			$configuration->value = '0';
		}
		$configuration->update();
	}

}
Core::redir("./index.php?view=configuration/edit-clinic-profile");
