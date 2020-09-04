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


include_once("../../../../../_setup.php");
include_once("../../../../../classes/member.php");
include_once("../../../../../classes/customform.php");



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
	
	
	if($_POST['moveDir'] == "up") {
		$addTo = -1;
	}
	else {
		$addTo = 1;		
	}
	
	$newIndex = $componentIndex+$addTo;
	$currentValue = $_SESSION['btFormComponent'][$componentIndex];
	$otherValue = $_SESSION['btFormComponent'][$newIndex];
	
	$_SESSION['btFormComponent'][$componentIndex] = $otherValue;
	$_SESSION['btFormComponent'][$newIndex] = $currentValue;
	
	$_SESSION['btFormComponent'] = array_filter($_SESSION['btFormComponent']);
	
	
	include("componentcache.php");
	
	
}




?>