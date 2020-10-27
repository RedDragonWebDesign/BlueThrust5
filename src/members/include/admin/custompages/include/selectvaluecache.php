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


if($_POST['whichValue'] == "" || !is_numeric($_POST['whichValue'])) {
	$componentIndex = $_SESSION['btFormComponentCount'];
	
	$tempArr = $_SESSION['btFormComponent'][$componentIndex]['cOptions'];
	asort($tempArr);
	
}
else {
	$componentIndex = $_POST['whichValue'];	
	
	$tempArr = $_SESSION['btFormComponentTempSelectValues'];
	asort($tempArr);
	
}

if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {
	
	$countErrors = 0;
	if($_POST['action'] == "add") {

		if(trim($_POST['optionValue']) == "") {
			
			$countErrors++;
		}
		
		if($countErrors == 0 && !isset($_POST['whichValue'])) {
			$_SESSION['btFormComponentTempSelectValues'] = array();
			$tempArr = $_SESSION['btFormComponent'][$componentIndex]['cOptions'];
			$tempArr[] = $_POST['optionValue'];
			
			asort($tempArr);
			
			$_SESSION['btFormComponent'][$componentIndex]['cOptions'] = $tempArr;
			
		}
		elseif($countErrors == 0 && isset($_POST['whichValue'])) {
			$_SESSION['btFormComponentTempSelectValues'][] = $_POST['optionValue'];
			
			$tempArr = $_SESSION['btFormComponentTempSelectValues'];
			asort($tempArr);
			
		}
		
		
	}
	elseif($_POST['action'] == "delete" && is_numeric($_POST['deleteKey'])) {
		
		if($_POST['whichValue'] == "" || !is_numeric($_POST['whichValue'])) {
			$componentIndex = $_SESSION['btFormComponentCount'];
			unset($_SESSION['btFormComponent'][$componentIndex]['cOptions'][$_POST['deleteKey']]);
			
			$tempArr = $_SESSION['btFormComponent'][$componentIndex]['cOptions'];
			asort($tempArr);
			
		}
		else {
			$componentIndex = $_POST['whichValue'];
			unset($_SESSION['btFormComponentTempSelectValues'][$_POST['deleteKey']]);
			
			$tempArr = $_SESSION['btFormComponentTempSelectValues'];
			asort($tempArr);
			
		}
		
		
	}
	
	
	$counter = 1;
	foreach($tempArr as $key => $value) {
		echo "<div style='float: left'>".$counter.". ".filterText($value)."</div><div style='float: right'> - <a href='javascript:void(0)' onclick=\"deleteSelectValue('".$key."')\">Delete</a></div><div style='clear: both'></div>";
		$counter++;		
	}
	
	if($counter == 1) {
		echo "<i>None</i>";	
	}
	
	
}

?>