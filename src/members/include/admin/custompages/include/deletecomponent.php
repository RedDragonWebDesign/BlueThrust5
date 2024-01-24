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



require_once("../../../../../_setup.php");
require_once("../../../../../classes/member.php");
require_once("../../../../../classes/customform.php");



$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Add Custom Form Page");
$consoleObj->select($cID);
$checkAccess1 = $member->hasAccess($consoleObj);
$cID = $consoleObj->findConsoleIDByName("Manage Custom Form Pages");
$consoleObj->select($cID);
$checkAccess2 = $member->hasAccess($consoleObj);


$customFormObj = new CustomForm($mysqli);
$appComponentObj = $customFormObj->objComponent;

$componentIndex = $_POST['whichComponent'];



if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2) && is_numeric($componentIndex)) {

	if(isset($_SESSION['btFormComponent'][$componentIndex]['component_id'])) {
		$_SESSION['btDeleteFormComponent'][] = $_SESSION['btFormComponent'][$componentIndex]['component_id'];
	}


	unset($_SESSION['btFormComponent'][$componentIndex]);

	require_once("componentcache.php");

}