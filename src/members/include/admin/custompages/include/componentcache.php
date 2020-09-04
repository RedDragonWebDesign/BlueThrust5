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


//$componentIndex = $_SESSION['btFormComponentCount'];

$_SESSION['btFormComponent'] = array_values($_SESSION['btFormComponent']);

if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {
	
	
	echo "
	
		<table class='formTable' style='width: 90%; margin-top: 3px'>
		

	";
	
	$totalComponents = count($_SESSION['btFormComponent'])-1;
	$counter = 0;
	foreach($_SESSION['btFormComponent'] as $key => $value) {
		
		$dispUpArrow = "";
		if($counter != 0) {
			$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveComponent('".$key."', 'up')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' style='width: 24px; height: 24px; border: 0px'></a>";
		}
		
		$dispDownArrow = "";
		if($counter != $totalComponents) {
			$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveComponent('".$key."', 'down')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' style='width: 24px; height: 24px; border: 0px'></a>";
		}
		
		$counter++;
		
		$dispType = ucfirst($value['type']);
		if($value['type'] == "multiselect") {
			$dispType = "Multi-Select";
		}
		elseif($value['type'] == "largeinput") {
			$dispType = "Large-Input";	
		}
		
		
		echo "
			<tr>
				<td class='main' style='width: 50%'><a href='javascript:void(0)' onclick=\"editComponent('".$key."')\">".$value['name']."</a></td>
				<td class='main' style='width: 26%' align='center'>".$dispType."</td>
				<td class='main' style='width: 6%' align='center'>".$dispUpArrow."</td>
				<td class='main' style='width: 6%' align='center'>".$dispDownArrow."</td>
				<td class='main' style='width: 6%' align='center'><a href='javascript:void(0)' onclick=\"editComponent('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' style='width: 24px; height: 24px; border: 0px'></a></td>
				<td class='main' style='width: 6%' align='center'><a href='javascript:void(0)' onclick=\"deleteComponent('".$key."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' style='width: 24px; height: 24px; border: 0px'></a></td>
			</tr>
		";
		
	}

	
	echo "
		</table>
	";
	
	if(count($_SESSION['btFormComponent']) == 0) {
		echo "
			<p class='main' align='center'>
				<i>You have not added any components!</i>
			</p>
		";
	}
	
}



?>