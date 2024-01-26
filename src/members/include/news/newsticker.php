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

if (!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	exit();
} else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if (!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];
$i=1;
$arrComponents = [
	"newsticker" => [
		"type" => "textbox",
		"value" => $websiteInfo['newsticker'],
		"attributes" => ["class" => "formInput textBox", "style" => "width: 35%"],
		"sortorder" => $i++,
		"display_name" => "News Ticker",
		"tooltip" => "Leave blank to turn off this feature."
	],
	"displaysettings" => [
		"type" => "section",
		"options" => ["section_title" => "Display Settings"],
		"sortorder" => $i++
	],
	"color" => [
		"type" => "colorpick",
		"value" => $websiteInfo['newstickercolor'],
		"sortorder" => $i++,
		"display_name" => "Color",
		"attributes" => ["class" => "formInput textBox", "id" => "ntColor"]
	],
	"fontsize" => [
		"type" => "select",
		"display_name" => "Font Size",
		"attributes" => ["class" => "formInput textBox"],
		"sortorder" => $i++,
		"options" => [
						"" => "Default",
						10 => "10px",
						12 => "12px",
						14 => "14px",
						16 => "16px",
						18 => "18px",
						20 => "20px",
						22 => "22px",
						24 => "24px"],
		"value" => $websiteInfo['newstickersize'],
		"validate" => ["RESTRICT_TO_OPTIONS"]
	],
	"boldtext" => [
		"type" => "checkbox",
		"display_name" => "Bold Text",
		"options" => [1 => ""],
		"attributes" => ["class" => "formInput textBox"],
		"sortorder" => $i++,
		"value" => $websiteInfo['newstickerbold'],
		"validate" => ["POSITIVE_NUMBER"]
	],
	"italictext" => [
		"type" => "checkbox",
		"display_name" => "Italic Text",
		"options" => [1 => ""],
		"attributes" => ["class" => "formInput textBox"],
		"sortorder" => $i++,
		"value" => $websiteInfo['newstickeritalic'],
		"validate" => ["POSITIVE_NUMBER"]
	],
	"submit" => [
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => ["class" => "submitButton formSubmitButton"],
		"type" => "submit"
	]
];

$setupFormArgs = [
	"name" => "console-".$cID,
	"components" => $arrComponents,
	"description" => "Use the form below to set what displays in the news ticker on the home page.",
	"saveMessage" => "Successfully saved news ticker!",
	"attributes" => ["action" => $MAIN_ROOT."members/console.php?cID=".$cID, "method" => "post"],
	"afterSave" => ["saveNewsTicker"]
];


function saveNewsTicker() {
	global $webInfoObj;

	$arrColumns = ["newsticker", "newstickercolor", "newstickersize", "newstickerbold", "newstickeritalic"];
	$arrValues = [$_POST['newsticker'], $_POST['color'], $_POST['fontsize'], $_POST['boldtext'], $_POST['italictext']];

	$webInfoObj->multiUpdate($arrColumns, $arrValues);
}
