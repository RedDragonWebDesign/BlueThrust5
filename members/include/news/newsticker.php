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

if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];
$i=1;
$arrComponents = array(
	"newsticker" => array(
		"type" => "textbox",
		"value" => $websiteInfo['newsticker'],
		"attributes" => array("class" => "formInput textBox", "style" => "width: 35%"),
		"sortorder" => $i++,
		"display_name" => "News Ticker",
		"tooltip" => "Leave blank to turn off this feature."
	),
	"displaysettings" => array(
		"type" => "section",
		"options" => array("section_title" => "Display Settings"),
		"sortorder" => $i++		
	),
	"color" => array(
		"type" => "colorpick",
		"value" => $websiteInfo['newstickercolor'],
		"sortorder" => $i++,
		"display_name" => "Color",
		"attributes" => array("class" => "formInput textBox", "id" => "ntColor")
	),
	"fontsize" => array(
		"type" => "select",
		"display_name" => "Font Size",
		"attributes" => array("class" => "formInput textBox"),
		"sortorder" => $i++,
		"options" => array(
						"" => "Default",
						10 => "10px",
						12 => "12px",
						14 => "14px",
						16 => "16px",
						18 => "18px",
						20 => "20px",
						22 => "22px",
						24 => "24px"),
		"value" => $websiteInfo['newstickersize'],
		"validate" => array("RESTRICT_TO_OPTIONS")
	),
	"boldtext" => array(
		"type" => "checkbox",
		"display_name" => "Bold Text",
		"options" => array(1 => ""),
		"attributes" => array("class" => "formInput textBox"),
		"sortorder" => $i++,
		"value" => $websiteInfo['newstickerbold'],
		"validate" => array("POSITIVE_NUMBER")
	),
	"italictext" => array(
		"type" => "checkbox",
		"display_name" => "Italic Text",
		"options" => array(1 => ""),
		"attributes" => array("class" => "formInput textBox"),
		"sortorder" => $i++,
		"value" => $websiteInfo['newstickeritalic'],
		"validate" => array("POSITIVE_NUMBER")
	),
	"submit" => array(
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => array("class" => "submitButton formSubmitButton"),
		"type" => "submit"		
	)
);

$setupFormArgs = array(
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to set what displays in the news ticker on the home page.",
	"saveMessage" => "Successfully saved news ticker!",
	"attributes" => array("action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"),
	"afterSave" => array("saveNewsTicker")
);


function saveNewsTicker() {
	global $webInfoObj;
	
	$arrColumns = array("newsticker", "newstickercolor", "newstickersize", "newstickerbold", "newstickeritalic");
	$arrValues = array($_POST['newsticker'], $_POST['color'], $_POST['fontsize'], $_POST['boldtext'], $_POST['italictext']);
	
	$webInfoObj->multiUpdate($arrColumns, $arrValues);
}

?>