<?php

	/*
	 * BlueThrust Clan Scripts
	 * Copyright 2014
	 *
	 * Author: Bluethrust Web Development
	 * E-mail: support@bluethrust.com
	 * Website: http://www.bluethrust.com
	 *
	 * License: http://www.bluethrust.com/license.php
	 *
	 */

if (!defined("MAIN_ROOT")) {
	exit();
}

$arrTimezoneOptions = $clockObj->getTimezones();

$clockOrderObj = new Clock($mysqli);
$arrClocks = [];
$result = $mysqli->query("SELECT * FROM ".$dbprefix."clocks ORDER BY ordernum DESC");
while ($row = $result->fetch_assoc()) {
	$arrClocks[$row['clock_id']] = filterText($row['name']);
}

if (count($arrClocks) == 0) {
	$arrClocks['first'] = "(first clock)";
}

$i=0;
$arrComponents = [

		"name" => [
			"type" => "text",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox"],
			"display_name" => "Name",
			"db_name" => "name"
		],
		"color" => [
			"type" => "colorpick",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox", "id" => "clockColor"],
			"display_name" => "Display Color",
			"db_name" => "color"
		],
		"timezone" => [
			"type" => "select",
			"sortorder" => $i++,
			"attributes" => ["class" => "formInput textBox"],
			"display_name" => "Timezone",
			"db_name" => "timezone",
			"options" => $arrTimezoneOptions
		],
		"displayorder" => [
			"type" => "beforeafter",
			"sortorder" => $i++,
			"attributes" => ["class" => "textBox"],
			"db_name" => "ordernum",
			"options" => $arrClocks,
			"display_name" => "Display Order",
			"validate" => [["name" => "VALIDATE_ORDER", "orderObject" => $clockOrderObj]]
		],
		"submit" => [
			"type" => "submit",
			"sortorder" => $i++,
			"attributes" => ["class" => "formSubmitButton submitButton"],
			"value" => "Add Clock"
		]

	];

$setupFormArgs = [
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"saveObject" => $clockObj,
		"saveType" => "add",
		"saveMessage" => "Successfully added new clock!",
		"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
		"description" => "Use the form below to add a new clock to your website.",
		"beforeAfter" => true
	];
