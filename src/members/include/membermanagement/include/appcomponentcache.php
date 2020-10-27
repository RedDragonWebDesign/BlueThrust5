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



include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/basicorder.php");



$consoleObj = new ConsoleOption($mysqli);
$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$cID = $consoleObj->findConsoleIDByName("Member Application");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	if($_POST['action'] == "add") {

		if(trim($_POST['newOptionValue']) != "") {
		
			$tempOptionArr = $_SESSION['btAppComponent']['cOptions'];
			$tempOptionArr[] = $_POST['newOptionValue'];
			asort($tempOptionArr);
			$_SESSION['btAppComponent']['cOptions'] = $tempOptionArr;
		
		}
		
	}
	elseif($_POST['action'] == "delete") {
		
		if(is_numeric($_POST['deleteOptionKey'])) {
			$tempOptionArr = $_SESSION['btAppComponent']['cOptions'];
			
			unset($tempOptionArr[$_POST['deleteOptionKey']]);
			
			asort($tempOptionArr);
			
			$_SESSION['btAppComponent']['cOptions'] = $tempOptionArr;
			
		}
	}
	
	
	$counter = 1;
	foreach($_SESSION['btAppComponent']['cOptions'] as $key => $optionValue) {
		
		echo "<div style='float: left'>".$counter.". ".filterText($optionValue)."</div><div style='float: right; padding-right: 30px'>- <a href='javascript:void(0)' onclick=\"deleteOptionValue('".$key."')\">Delete</a></div><div style='clear: both'></div>";
		
		$counter++;
	}
	
	if($counter == 1) {
		echo "<i>None</i>";
	}
	
	
}


?>