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


if(!isset($pluginObj)) { exit(); }
include_once(BASE_DIRECTORY."plugins/donations/classes/campaign.php");


$arrPaypalCurrencyCodes = DonationCampaign::getCurrencyCodes();
$arrPaypalCurrencyInfo =  DonationCampaign::getCurrencyCodeInfo();

$configInfo = $pluginObj->getConfigInfo();


$i=0;
$arrComponents = array(
	"email" => array(
		"type" => "text",
		"attributes" => array("class" => "textBox formInput"),
		"display_name" => "Paypal E-mail",
		"sortorder" => $i++,
		"value" => $configInfo['email'],
		"validate" => array("NOT_BLANK")
	),
	"mode" => array(
		"type" => "select",
		"attributes" => array("class" => "textBox formInput"),
		"display_name" => "Mode",
		"options" => array("" => "Sandbox", "live" => "Live"),	
		"sortorder" => $i++,
		"value" => $configInfo['mode'],
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"tooltip" => "You can use sandbox mode to test donations without real money.  You will have to set up test accounts with Paypal in order to use Sandbox mode"
	),
	"defaultcurrency" => array(
		"type" => "select",
		"attributes" => array("class" => "textBox formInput"),
		"display_name" => "Default Currency",
		"options" => $arrPaypalCurrencyCodes,
		"sortorder" => $i++,
		"validate" => array("RESTRICT_TO_OPTIONS"),
		"value" => $configInfo['currency']
	),
	"goalprogresscolor" => array(
		"type" => "colorpick",
		"value" => $configInfo['goalprogresscolor'],
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox", "id" => "goalColor"),
		"display_name" => "Progressbar Front Color"
	),
	"goalprogressbackcolor" => array(
		"type" => "colorpick",
		"value" => $configInfo['goalprogressbackcolor'],
		"sortorder" => $i++,
		"attributes" => array("class" => "formInput textBox", "id" => "goalBackColor"),
		"display_name" => "Progressbar Back Color"
	),
	"thankyou" => array(
		"type" => "richtextbox",
		"attributes" => array("class" => "textBox formInput", "id" => "thankYouMessage", "style" => "width: 100%", "rows" => 15),
		"display_name" => "Thank You Page Message",
		"sortorder" => $i++,
		"value" => $configInfo['thankyou'],
		"allowHTML" => true
	),
	"submit" => array(
		"type" => "submit",
		"value" => "Save",
		"sortorder" => $i++,
		"attributes" => array("class" => "submitButton formSubmitButton")			
	)
		
);


$setupFormArgs = array(
	"name" => "pluginsettings-".$_GET['plugin'],
	"components" => $arrComponents,
	"description" => "Use the form below to configure the donation plugin.",
	"attributes" => array("action" => $MAIN_ROOT."plugins/settings.php?plugin=".$_GET['plugin'], "method" => "post"),
	"afterSave" => array("saveDonationSettings"),
	"saveMessage" => "Donation Settings Saved!",
	"saveLink" => $MAIN_ROOT."members/console.php?cID=".$cID
);

function saveDonationSettings() {
	global $pluginObj;
	
	$arrFilter = array("<?", "?>", "<script>", "</script>");
	foreach($arrFilter as $filterOut) {
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

?>