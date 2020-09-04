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

include_once("../../_setup.php");
include_once("../../classes/member.php");
include_once("../../classes/rank.php");
include_once("../../classes/btplugin.php");
include_once("../../classes/consolecategory.php");

// Plugin Info

$PLUGIN_TABLE_NAME = $dbprefix."facebook";
$PLUGIN_NAME = "Facebook Login";


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Plugin Manager");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$pluginObj = new btPlugin($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	
	$countErrors = 0;
	$dispError = array();
	
	// Check if installed
	
	if(!in_array($_POST['pluginDir'], $pluginObj->getPlugins("filepath"))) {
		$countErrors++;
		$dispError[] = "The selected plugin is not installed!";
	}
	
	// Start Uninstall
	
	$sql = "DROP TABLE `".$PLUGIN_TABLE_NAME."`";
	
	if($mysqli->query($sql)) {
		// Remove Plugin from plugin table
		$pluginID = array_search($_POST['pluginDir'], $pluginObj->getPlugins("filepath"));
		
		$pluginObj->select($pluginID);
		$checkDeletePlugin = $pluginObj->delete();
		
		// Remove Console Option
		$fbLoginCID = $consoleObj->findConsoleIDByName($PLUGIN_NAME);
		$checkDeleteConsole = false;
		if($consoleObj->select($fbLoginCID)) {
			$checkDeleteConsole = $consoleObj->delete();
		}

		
		if(!$checkDeletePlugin) {
			$countErrors++;
			$dispError[] = "Unable to delete plugin from database table.  You will have to manually delete it. - ".$pluginID;
		}
		
		if(!$checkDeleteConsole) {
			$countErrors++;
			$dispError[] = "Unable to delete ".$PLUGIN_NAME." console option.  You will have to manually delete it.";	
		}
		
	}
	else {
		$countErrors++;
		$dispError[] = "Unable to delete plugin database table.";
	}
	
	
	$arrReturn = array();
	if($countErrors == 0) {
		$arrReturn['result'] = "success";
		$member->logAction("Uninstalled ".$PLUGIN_NAME." Plugin.");
	}
	else {
		$arrReturn['result'] = "fail";
		$arrReturn['errors'] = $dispError;
	}
	
	
	echo json_encode($arrReturn);
	
	
}