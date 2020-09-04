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

if(!isset($menuXML)) {
	$prevFolder = "../../../../../";
	include_once($prevFolder."_setup.php");

	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$consoleObj = new ConsoleOption($mysqli);
	
	
	$menuCatObj = new MenuCategory($mysqli);
	
	if(trim($_SERVER['HTTPS']) == "" || $_SERVER['HTTPS'] == "off") {
		$dispHTTP = "http://";
	}
	else {
		$dispHTTP = "https://";
	}
	
	$siteDomain = $_SERVER['SERVER_NAME'];

	try {
		$menuXML = new SimpleXMLElement(BASE_DIRECTORY."themes/".$THEME."/themeinfo.xml", NULL, true);
	}
	catch(Exception $e) {
		$menuXML = new SimpleXMLElement(BASE_DIRECTORY."themes/".$THEME."/themeinfo.xml", NULL, true);
	}
}

$intAddMenuCatCID = $consoleObj->findConsoleIDByName("Add Menu Category");
$intEditMenuCatCID = $consoleObj->findConsoleIDByName("Manage Menu Categories");

$consoleObj->select($intAddMenuCatCID);
$checkAccess1 = $member->hasAccess($consoleObj);

$consoleObj->select($intEditMenuCatCID);
$checkAccess2 = $member->hasAccess($consoleObj);

if($member->authorizeLogin($_SESSION['btPassword']) && ($checkAccess1 || $checkAccess2)) {

	if(isset($_POST['section']) && is_numeric($_POST['section'])) {
		$orderoptions = "";
		$selectCatID = "";
		if(!isset($_POST['mcID'])) {
			$_POST['mcID'] = "";	
		}
		else {
			$menuCatObj->select($_POST['mcID']);
			$selectCatID = $menuCatObj->findBeforeAfter();
			$selectCatID = $selectCatID[0];
		}
		
		$lastCategory = "";
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."menu_category WHERE section = '".$_POST['section']."' ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {
			if($_POST['mcID'] != $row['menucategory_id']) {
				
				$dispSelected = "";
				if($selectCatID == $row['menucategory_id']) {
					$dispSelected = " selected";	
				}
				
				$orderoptions .= "<option value='".$row['menucategory_id']."'".$dispSelected.">".filterText($row['name'])."</option>";
			}
			
			$lastCategory = $row['menucategory_id'];
		}
		
		if($result->num_rows == 0 || ($result->num_rows == 1 && $_POST['mcID'] != "" && $_POST['mcID'] == $lastCategory)) {
			$orderoptions = "<option value='first'>(first category)</option>";	
		}
		
		echo $orderoptions;
	}
	elseif(isset($_POST['manage'])) {
		
		$arrDispSectionNames = array();
		for($x=0; $x<$menuXML->info->section->count(); $x++) {
		
			$arrDispSectionNames[$x] = $menuXML->info->section[$x];
			
		}
	
		echo "<table class='formTable' style='margin-top: 0px; border-spacing: 0px'><tr><td colspan='5' class='dottedLine'></td></tr>";
		
		$intSection = "";
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."menu_category ORDER BY section, sortnum");
		while($row = $result->fetch_assoc()) {
			
			if($intSection != $row['section']) {
				$intSection = $row['section'];
				$counter = 0;

				echo "
					<tr>
						<td class='dottedLine main manageList' style='padding-top: 5px; text-decoration: underline; font-weight: bold'>
							".$arrDispSectionNames[$intSection]."
						</td>
						<td class='dottedLine main manageList' colspan='4' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intAddMenuCatCID."&sectionID=".$intSection."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/add.png' class='manageListActionButton' title='Add menu category to ".$arrDispSectionNames[$intSection]."'></a></td>
					</tr>
				";
				
			}
			
			$addCSS = "";
			
			if(($counter%2) == 1) {
				$addCSS = " alternateBGColor";
			}
			
			$menuCatObj->setCategoryKeyValue($intSection);
			$intHighestSortNum = $menuCatObj->getHighestSortNum();
			
			if($counter == 0) {
				$dispUpArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' class='manageListActionButton'>";
			}
			else {
				$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveCat('up', '".$row['menucategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton' title='Move Up'></a>";
			}
			
			if(($counter+1) == $intHighestSortNum) {
				$dispDownArrow = "<img src='".$MAIN_ROOT."themes/".$THEME."/images/transparent.png' class='manageListActionButton'>";
			}
			else {
				$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveCat('down', '".$row['menucategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton' title='Move Down'></a>";
			}
			
			
			echo "
				<tr>
					<td class='dottedLine main manageList".$addCSS."' style='font-weight: bold; padding-left: 10px; width: 76%'><a href='".$MAIN_ROOT."members/console.php?cID=".$intEditMenuCatCID."&mcID=".$row['menucategory_id']."&action=edit'>".filterText($row['name'])."</a></td>
					<td class='dottedLine main manageList".$addCSS."' style='width: 6%' align='center'>".$dispUpArrow."</td>
					<td class='dottedLine main manageList".$addCSS."' style='width: 6%' align='center'>".$dispDownArrow."</td>
					<td class='dottedLine main manageList".$addCSS."' style='width: 6%' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$intEditMenuCatCID."&mcID=".$row['menucategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit Category'></a></td>
					<td class='dottedLine main manageList".$addCSS."' style='width: 6%' align='center'><a href='javascript:void(0)' onclick=\"deleteCat('".$row['menucategory_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' title='Delete Category'></a></td>
				</tr>
			";
			
			
			
			$counter++;
		}
		
		if($result->num_rows == 0) {
			$orderoptions = "
			<div class='shadedBox' style='width: 40%'>
				<p class='main' align='center'>
					<i>No Menu Categories Added Yet!</i><br><br><a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewCategory."'>Click here</a> to add one!</i>
				</p>
			</div>
			";
		}
		
		echo "</table>";
	}
	
}

?>