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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php") {
	
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/basicorder.php");
	
	
	
	$consoleObj = new ConsoleOption($mysqli);
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$cID = $consoleObj->findConsoleIDByName("Member Application");
	$consoleObj->select($cID);
	
	
	if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {
		
		exit();
		
	}
	
	
}


echo "

		<table class='formTable' style='width: 90%; margin-top: 0px'>
			<tr>
				<td class='main' style='width: 50%'>Username</td>
				<td class='main' align='center' style='width: 25%'>Input</td>
				<td class='main' align='center' style='width: 25%; height: 24px'>N/A</td>
			</tr>
			<tr>
				<td class='main' style='width: 50%'>Password</td>
				<td class='main' align='center' style='width: 25%'>Input</td>
				<td class='main' align='center' style='width: 25%; height: 24px'>N/A</td>
			</tr>
			<tr>
				<td class='main' style='width: 50%'>E-mail Address</td>
				<td class='main' align='center' style='width: 25%'>Input</td>
				<td class='main' align='center' style='width: 25%; height: 24px'>N/A</td>
			</tr>
			";
	
	$objAppComponent = new BasicOrder($mysqli, "app_components", "appcomponent_id");
	$result = $mysqli->query("SELECT appcomponent_id FROM ".$dbprefix."app_components ORDER BY ordernum DESC");
	while($row = $result->fetch_assoc()) {
		
		$objAppComponent->select($row['appcomponent_id']);
		$appComponentInfo = $objAppComponent->get_info_filtered();
		
		$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveAppComponent('up', '".$row['appcomponent_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' width='24' height='24' title='Move Up'></a>";
		$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveAppComponent('down', '".$row['appcomponent_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' width='24' height='24' title='Move Down'></a>";
		if($appComponentInfo['ordernum'] == 1) {
			$dispDownArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
		}
		
		if($appComponentInfo['ordernum'] == $objAppComponent->getHighestOrderNum()) {
			$dispUpArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' width='24' height='24'>";
		}

		
		
		switch($appComponentInfo['componenttype']) {
			case "multiselect":
				$appComponentInfo['componenttype'] = "Multi-Select";
				break;
			case "largeinput":
				$appComponentInfo['componenttype'] = "Large-Input";
				break;
			case "captchaextra":
				$appComponentInfo['componenttype'] = "Captcha - Extra Distortion";	
				break;
			case "profile":
				$appComponentInfo['componenttype'] = "Profile Option";
				break;
		}
		
		
		$dispType = ucfirst($appComponentInfo['componenttype']);
		
		echo "
		
			<tr>
				<td class='main' style='width: 50%'>".$appComponentInfo['name']."</td>
				<td class='main' align='center' style='width: 25%'>".$dispType."</td>
				<td class='main' align='center' style='width: 25%'>".$dispUpArrow."&nbsp;".$dispDownArrow."&nbsp;<a href='javascript:void(0)' onclick=\"editAppComponent('".$row['appcomponent_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' width='24' height='24' title='Edit Component'></a>&nbsp;<a href='javascript:void(0)' onclick=\"deleteAppComponent('".$row['appcomponent_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' width='24' height='24' title='Delete Component'></a></td>
			</tr>
		
		
		";
		

	}
	
echo "
		</table>


";

?>