<?php

	/*
	 * Bluethrust Clan Scripts v4
	 * Copyright 2014
	 *
	 * Author: Bluethrust Web Development
	 * E-mail: support@bluethrust.com
	 * Website: http://www.bluethrust.com
	 *
	 * License: http://www.bluethrust.com/license.php
	 *
	 */

	if(!defined("MAIN_ROOT")) { exit(); }

	$arrTimezoneOptions = $clockObj->getTimezones();	

	$clockOrderObj = new Clock($mysqli);
	$arrClocks = array();
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."clocks ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		$arrClocks[$row['clock_id']] = filterText($row['name']);
	}
	
	if(count($arrClocks) == 0) {
		$arrClocks['first'] = "(first clock)";	
	}

	$i=0;
	$arrComponents = array(
	
		"name" => array(
			"type" => "text",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox"),
			"display_name" => "Name",
			"db_name" => "name"
		),
		"color" => array(
			"type" => "colorpick",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox", "id" => "clockColor"),
			"display_name" => "Display Color",
			"db_name" => "color"
		),
		"timezone" => array(
			"type" => "select",
			"sortorder" => $i++,
			"attributes" => array("class" => "formInput textBox"),
			"display_name" => "Timezone",
			"db_name" => "timezone",
			"options" => $arrTimezoneOptions
		),
		"displayorder" => array(
			"type" => "beforeafter",
			"sortorder" => $i++,
			"attributes" => array("class" => "textBox"),
			"db_name" => "ordernum",
			"options" => $arrClocks,
			"display_name" => "Display Order",
			"validate" => array(array("name" => "VALIDATE_ORDER", "orderObject" => $clockOrderObj))
		),
		"submit" => array(
			"type" => "submit",
			"sortorder" => $i++,
			"attributes" => array("class" => "formSubmitButton submitButton"),
			"value" => "Add Clock"
		)
	
	);
	
	$setupFormArgs = array(
		"name" => "console-".$cID,
		"components" => $arrComponents,
		"saveObject" => $clockObj,
		"saveType" => "add",
		"saveMessage" => "Successfully added new clock!",
		"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
		"description" => "Use the form below to add a new clock to your website.",
		"beforeAfter" => true
	);
	
?>