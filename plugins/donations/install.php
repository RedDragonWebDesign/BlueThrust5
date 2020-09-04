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
	
	// Check if already installed
	
	if(in_array($_POST['pluginDir'], $pluginObj->getPlugins("filepath"))) {
		$countErrors++;
		$dispError[] = "The selected plugin is already installed!";
	}
	
	// Check if plugin table name interferes with other tables
	
	$result = $mysqli->query("SHOW TABLES");

	while($row = $result->fetch_array()) {
		if(in_array($row[0], $arrPluginTables)) {
			$countErrors++;
			$dispError[] = "There is database table that conflicts with this plugin. - ".$row[0];	
		}
	}
	
	
	
	if($countErrors == 0) {
		// $sql variable
		include(BASE_DIRECTORY."plugins/donations/sql.php");
		
		if($mysqli->multi_query($sql)) {
			
			do {
				if($result = $mysqli->store_result()) {
					$result->free();
				}
			}
			while($mysqli->next_result());

			$pluginObj->addNew(array("name", "filepath", "dateinstalled"), array($PLUGIN_NAME, $_POST['pluginDir'], time()));
			
			$pluginID = $pluginObj->get_info("plugin_id");
			$pluginObj->pluginPage->setCategoryKeyValue($pluginID);

			$pluginObj->pluginPage->addNew(array("plugin_id", "page", "pagepath"), array($pluginID, "mods", "plugins/donations/include/menu_module.php"));
			
			// Check if need to add new console category
			
			$result = $mysqli->query("SELECT consolecategory_id FROM ".$dbprefix."consolecategory WHERE name = 'Donations'");
			if($result->num_rows == 0) {
				$consoleCatObj = new ConsoleCategory($mysqli);
				$newOrderNum = $consoleCatObj->getHighestOrderNum()+1;
				$consoleCatObj->addNew(array("name", "ordernum"), array("Donations", $newOrderNum));
				$consoleCatID = $consoleCatObj->get_info("consolecategory_id");
			}
			else {
				$row = $result->fetch_assoc();
				$consoleCatID = $row['consolecategory_id'];	
			}
			
			$consoleObj->setCategoryKeyValue($consoleCatID);
			$newSortNum = $consoleObj->getHighestSortNum()+1;
			
			$consoleObj->addNew(array("consolecategory_id", "pagetitle", "filename", "sortnum"), array($consoleCatID, "Create a Donation Campaign", "../plugins/donations/console/createcampaign.php", $newSortNum++));
			$consoleObj->addNew(array("consolecategory_id", "pagetitle", "filename", "sortnum"), array($consoleCatID, "Manage Donation Campaigns", "../plugins/donations/console/managecampaign.php", $newSortNum++));
			
			
		}
		else {
			$countErrors++;
			$dispError[] = "Unable to create plugin database table.";
		}
	}
	
	
	$arrReturn = array();
	if($countErrors == 0) {
		$arrReturn['result'] = "success";
		$member->logAction("Installed ".$PLUGIN_NAME." Plugin.");
	}
	else {
		$arrReturn['result'] = "fail";	
		$arrReturn['errors'] = $dispError;
	}
	
	
	echo json_encode($arrReturn);
	
}
?>