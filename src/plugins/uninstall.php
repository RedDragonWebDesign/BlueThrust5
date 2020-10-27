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

$prevFolder = "../";
include_once("../_setup.php");


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
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && isset($_GET['plugin'])) {
	
	$pluginInstaller = new PluginInstaller($mysqli);

	require(BASE_DIRECTORY."plugins/".$_GET['plugin']."/install_setup.php");	
	
	$pluginInstaller->uninstall();
	
	if(!$pluginInstaller->isInstalled()) {
		$member->logAction("Uninstalled ".$pluginInstaller->pluginName." Plugin.");		
	}
	
	
}

?>