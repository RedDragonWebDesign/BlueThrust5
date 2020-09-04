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

if(!isset($member)) {
	include_once("../../../../../_setup.php");
	include_once("../../../../../classes/member.php");
	include_once("../../../../../classes/menucategory.php");
	include_once("../../../../../classes/menuitem.php");
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	$consoleObj = new ConsoleOption($mysqli);
	
	$menuCatObj = new MenuCategory($mysqli);
	$menuItemObj = new MenuItem($mysqli);
	
}

if(!isset($intAddNewMenuItemID)) {  $intAddNewMenuItemID = $consoleObj->findConsoleIDByName("Add Menu Item"); }

if($member->authorizeLogin($_SESSION['btPassword'])) {
	
	if(isset($_POST['menuCatID']) && $menuCatObj->select($_POST['menuCatID'])) {
		$orderoptions = "";
		$menuCatInfo = $menuCatObj->get_info_filtered();
		
		$selectItemID = "";
		if(!isset($_POST['itemID'])) {
			$_POST['itemID'] = "";
		}
		else {
			
			$menuItemObj->select($_POST['itemID']);			
			$selectItemID = $menuItemObj->findBeforeAfter();
			$selectItemID = $selectItemID[0];
		}
		
		
		$lastItem = "";
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."menu_item WHERE menucategory_id = '".$menuCatInfo['menucategory_id']."' ORDER BY sortnum");
		while($row = $result->fetch_assoc()) {		
			if($_POST['itemID'] != $row['menuitem_id']) {
				$dispSelected = "";
				if($selectItemID == $row['menuitem_id']) {
					$dispSelected = " selected";
				}
				echo $selectItemID;
				$orderoptions .= "<option value='".$row['menuitem_id']."'".$dispSelected.">".filterText($row['name'])."</option>";
			}
			
			$lastItem = $row['menuitem_id'];
		}
		
		if($result->num_rows == 0 || ($result->num_rows == 1 && $_POST['itemID'] != "" && $_POST['itemID'] == $lastItem)) {
			$orderoptions = "<option value='first'>(first item)</option>";	
		}
		
		echo $orderoptions;
	}
	elseif(!isset($_POST['menuCatID'])) {
		
		$intManageMenuCatCID = $consoleObj->findConsoleIDByName("Manage Menu Categories");
		$query = "SELECT ".$dbprefix."menu_item.* FROM ".$dbprefix."menu_item, ".$dbprefix."menu_category WHERE ".$dbprefix."menu_item.menucategory_id = ".$dbprefix."menu_category.menucategory_id ORDER BY ".$dbprefix."menu_category.section, ".$dbprefix."menu_category.sortnum, ".$dbprefix."menu_item.menucategory_id, ".$dbprefix."menu_item.sortnum";
		
		echo "<table class='formTable' style='border-spacing: 0px; margin-top: 0px'><tr><td class='dottedLine' colspan='5'></td></tr>";
		$result = $mysqli->query($query);
		
		$intMenuCatID = "";
		while($row = $result->fetch_assoc()) {

			if($intMenuCatID != $row['menucategory_id']) {
				$counter = 0;
				$intMenuCatID = $row['menucategory_id'];
				$menuCatObj->select($intMenuCatID);
				echo "
				
					<tr>
						<td class='main manageList dottedLine' style='padding-top: 8px; font-weight: bold; text-decoration: underline'>".$menuCatObj->get_info_filtered("name")."</td>
						<td class='main manageList dottedLine' align='center' colspan='2'><a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewMenuItemID."&mcID=".$row['menucategory_id']."'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/add.png' class='manageListActionButton' title='Add menu item to ".$menuCatObj->get_info_filtered("name")."'></a></td>
						<td class='main manageList dottedLine' align='center' colspan='2'><a href='".$MAIN_ROOT."members/console.php?cID=".$intManageMenuCatCID."&mcID=".$row['menucategory_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit ".$menuCatObj->get_info_filtered("name")."'></a></td>
					</tr>
				
				";
				
				
			}
			
			$addCSS = "";
			if($counter%2 == 1) {
				$addCSS = " alternateBGColor";				
			}
			
			$menuItemObj->setCategoryKeyValue($intMenuCatID);
			$intHighestSortNum = $menuItemObj->getHighestSortNum();
			if(($counter+1) == $intHighestSortNum) {
				$dispDownArrow = "<img src='".$MAIN_ROOT."images/transparent.png' class='manageListActionButton'>";		
			}
			else {
				$dispDownArrow = "<a href='javascript:void(0)' onclick=\"moveItem('down', '".$row['menuitem_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton' title='Move Down'></a>";				
			}
			
			if($counter == 0) {
				$dispUpArrow = "<img src='".$MAIN_ROOT."images/transparent.png' class='manageListActionButton'>";
			}
			else {
				$dispUpArrow = "<a href='javascript:void(0)' onclick=\"moveItem('up', '".$row['menuitem_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton' title='Move Up'></a>";			
			}
			
			echo "
				<tr>
					<td class='main dottedLine manageList".$addCSS."' style='padding-left: 10px; font-weight: bold; width: 76%'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&menuID=".$row['menuitem_id']."&action=edit'>".filterText($row['name'])."</a></td>
					<td class='main dottedLine manageList".$addCSS."' style='width: 6%' align='center'>".$dispUpArrow."</td>
					<td class='main dottedLine manageList".$addCSS."' style='width: 6%' align='center'>".$dispDownArrow."</td>
					<td class='main dottedLine manageList".$addCSS."' style='width: 6%' align='center'><a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&menuID=".$row['menuitem_id']."&action=edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton' title='Edit Menu Item'></a></td>
					<td class='main dottedLine manageList".$addCSS."' style='width: 6%' align='center'><a href='javascript:void(0)' onclick=\"deleteItem('".$row['menuitem_id']."')\"><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton' title='Delete Menu Item'></a></td>
				</tr>
				";
			
			$counter++;
		}
		echo "</table>";
		
		
	}
	
}


?>