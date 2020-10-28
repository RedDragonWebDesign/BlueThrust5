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

require_once("../_setup.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$consoleObj = new ConsoleOption($mysqli);
$consoleObj->select($_GET['cID']);
$consoleInfo = $consoleObj->get_info_filtered();

$_SERVER['PHP_SELF'] = "console.php";
$_GET['action'] = "move";
require_once(BASE_DIRECTORY."members/include/".$consoleInfo['filename']);
if(!isset($objManageList)) {
	exit();	
}

if($member->authorizeLogin($_SESSION['btPassword'])) {

	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && $objManageList->select($_POST['itemID'])) {
		
		define("LOGGED_IN", true);
		
		$objManageList->move($_POST['moveDir']);
		
		require_once($objManageList->strMainListLink);
		require_once("console.managelist.list.php");
		
	}
	
	
}