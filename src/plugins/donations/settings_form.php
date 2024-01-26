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


if (!isset($pluginObj)) {
	exit();
}
require_once(BASE_DIRECTORY."plugins/donations/classes/campaign.php");


$arrPaypalCurrencyCodes = DonationCampaign::getCurrencyCodes();
$arrPaypalCurrencyInfo =  DonationCampaign::getCurrencyCodeInfo();

$configInfo = $pluginObj->getConfigInfo();


$i=0;
$arrComponents = [
	"email" => [
		"type" => "text",
		"attributes" => ["class" => "textBox formInput"],
		"display_name" => "Paypal E-mail",
		"sortorder" => $i++,
		"value" => $configInfo['email'],
		"validate" => ["NOT_BLANK"]
	],
	"mode" => [
		"type" => "select",
		"attributes" => ["class" => "textBox formInput"],
		"display_name" => "Mode",
		"options" => ["" => "Sandbox", "live" => "Live"],
		"sortorder" => $i++,
		"value" => $configInfo['mode'],
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"tooltip" => "You can use sandbox mode to test donations without real money.  You will have to set up test accounts with Paypal in order to use Sandbox mode"
	],
	"defaultcurrency" => [
		"type" => "select",
		"attributes" => ["class" => "textBox formInput"],
		"display_name" => "Default Currency",
		"options" => $arrPaypalCurrencyCodes,
		"sortorder" => $i++,
		"validate" => ["RESTRICT_TO_OPTIONS"],
		"value" => $configInfo['currency']
	],
	"goalprogresscolor" => [
		"type" => "colorpick",
		"value" => $configInfo['goalprogresscolor'],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox", "id" => "goalColor"],
		"display_name" => "Progressbar Front Color"
	],
	"goalprogressbackcolor" => [
		"type" => "colorpick",
		"value" => $configInfo['goalprogressbackcolor'],
		"sortorder" => $i++,
		"attributes" => ["class" => "formInput textBox", "id" => "goalBackColor"],
		"display_name" => "Progressbar Back Color"
	],
	"thankyou" => [
		"type" => "richtextbox",
		"attributes" => ["class" => "textBox formInput", "id" => "thankYouMessage", "style" => "width: 100%", "rows" => 15],
		"display_name" => "Thank You Page Message",
		"sortorder" => $i++,
		"value" => $configInfo['thankyou'],
		"allowHTML" => true
	],
	"submit" => [
		"type" => "submit",
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => ["class" => "submitButton formSubmitButton"]
	]

];


$setupFormArgs = [
	"name" => "pluginsettings-".$_GET['plugin'],
	"components" => $arrComponents,
	"description" => "Use the form below to configure the donation plugin.",
	"attributes" => ["action" => $MAIN_ROOT."plugins/settings.php?plugin=".$_GET['plugin'], "method" => "post"],
	"afterSave" => ["saveDonationSettings"],
	"saveMessage" => "Donation Settings Saved!",
	"saveLink" => $MAIN_ROOT."members/console.php?cID=".$cID
];

function saveDonationSettings() {
	global $pluginObj;

	$arrFilter = ["<?", "?>", "<script>", "</script>"];
	foreach ($arrFilter as $filterOut) {
		$_POST['thankyou'] = str_replace($filterOut, "", $_POST['thankyou']);
	}

	$pluginObj->addConfigValue("email", $_POST['email']);
	$pluginObj->addConfigValue("mode", $_POST['mode']);
	$pluginObj->addConfigValue("currency", $_POST['defaultcurrency']);
	$pluginObj->addConfigValue("thankyou", $_POST['thankyou']);
	$pluginObj->addConfigValue("goalprogresscolor", $_POST['goalprogresscolor']);
	$pluginObj->addConfigValue("goalprogressbackcolor", $_POST['goalprogressbackcolor']);
}

$EXTERNAL_JAVASCRIPT .= $formObj->getRichtextboxJSFile();
$EXTERNAL_JAVASCRIPT .= $formObj->getColorpickerJSFile();
