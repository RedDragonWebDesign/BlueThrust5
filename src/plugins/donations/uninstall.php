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

$prevFolder = "../../";
include_once("../../_setup.php");


// Plugin Info

$PLUGIN_TABLE_NAME = $dbprefix."donations";
$PLUGIN_NAME = "Donations";

$arrPluginTables = array(
	$dbprefix."donations",
	$dbprefix."donations_campaign",
	$dbprefix."donations_errorlog"
);

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
	
	$countDrops = 0;
	foreach($arrPluginTables as $tableName) {

		$dropSQL = "DROP TABLE `".$tableName."`";
		if($mysqli->query($dropSQL)) {
			$countDrops++;	
		}
		
	}
	
	if($countDrops == count($arrPluginTables)) {
		// Remove Plugin from plugin table
		$pluginID = array_search($_POST['pluginDir'], $pluginObj->getPlugins("filepath"));
		
		$pluginObj->select($pluginID);
		$checkDeletePlugin = $pluginObj->delete();
		
		
		
		// Remove Console Option
		
		$arrDeleteConsoleOptions = array(
			"Create a Donation Campaign",
			"Manage Donation Campaigns"
		);
		
		$countDrops = 0;
		foreach($arrDeleteConsoleOptions as $consoleOptionName) {
			$consoleOptionID = $consoleObj->findConsoleIDByName($consoleOptionName);
			if($consoleOptionID !== false) {
				$consoleObj->select($consoleOptionID);
				$countDrops = ($consoleObj->delete()) ? $countDrops+1 : $countDrops;
			}
		}
		
		$checkDeleteConsole = (count($arrDeleteConsoleOptions) == $countDrops);
		
		if(!$checkDeletePlugin) {
			$countErrors++;
			$dispError[] = "Unable to delete plugin from database table.  You will have to manually delete it. - ".$pluginID;
		}
		
		if(!$checkDeleteConsole) {
			$countErrors++;
			$dispError[] = "Unable to delete ".$PLUGIN_NAME." console options.  You will have to manually delete them.";	
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
	
	$mysqli->optimizeTables();
	
	echo json_encode($arrReturn);
	
}


?>