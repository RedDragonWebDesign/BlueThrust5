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

	if(!defined("LOGGED_IN") || !LOGGED_IN) { 
		
		$setupManageListArgs = json_decode($_POST['listArgs'], true);

		include("../_setup.php");

		
		$member = new Member($mysqli);
		$member->select($_SESSION['btUsername']);
		
		$consoleObj = new ConsoleOption($mysqli);
		if(!$consoleObj->select($setupManageListArgs['console_id'])) {
			exit();
		}
		
		if(!$member->authorizeLogin($_SESSION['btPassword']) || !$member->hasAccess($consoleObj)) {
			exit();	
		}
		
		$actionsWidth = count($setupManageListArgs['actions'])*6;
		$titleWidth = 100-($actionsWidth);
	}

	
	echo "
	
		<table class='formTable' style='border-spacing: 0px; margin-top: 0px'>
	";

		$counter = 0;
		foreach($setupManageListArgs['items'] as $itemInfo) {

			if($itemInfo['type'] == "listitem") {
			
				if($counter == 1) {
					$addCSS = " alternateBGColor";
					$counter = 0;
				}
				else {
					$addCSS = "";
					$counter = 1;
				}
				
				echo "
					<tr>
						<td class='main manageList dottedLine".$addCSS."' style='width: ".$titleWidth."%; padding-left: 10px'><b><a href='".$itemInfo['edit_link']."'>".$itemInfo['display_name']."</a></b></td>	
					
					";
				
				foreach($setupManageListArgs['actions'] as $actionTypes) {
					$dispAction = "";
					switch($actionTypes) {
						case "moveup":
							$dispAction = !in_array("moveup", $itemInfo['actions']) ? "" : "<a href='javascript:void(0)' onclick=\"moveItem('up', '".$itemInfo['item_id']."')\" title='Move Up'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/uparrow.png' class='manageListActionButton'></a>";
							break;
						case "movedown":
							$dispAction = !in_array("movedown", $itemInfo['actions']) ? "" : "<a href='javascript:void(0)' onclick=\"moveItem('down', '".$itemInfo['item_id']."')\" title='Move Down'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/downarrow.png' class='manageListActionButton'></a>";
							break;
						case "edit":
							$dispAction = "<a href='".$itemInfo['edit_link']."' title='Edit'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/edit.png' class='manageListActionButton'></a>";
							break;
						case "delete":
							$dispAction = "<a href='javascript:void(0)' onclick=\"deleteItem('".$itemInfo['item_id']."')\" title='Delete'><img src='".$MAIN_ROOT."themes/".$THEME."/images/buttons/delete.png' class='manageListActionButton'></a>";
							break;
						default:
							$dispAction = call_user_func_array($actionTypes, array($itemInfo['item_id']));	
					}
					
					echo "
						<td align='center' class='main manageList dottedLine".$addCSS."' style='width: 6%;'>".$dispAction."</td>	
					";
				}
				
						
				echo "</tr>";
				
			}
			else { // Category Title
				
				$dispAddItemToCategory = ($itemInfo['add_to_cat_link'] == "") ? "" : "<a href='".$itemInfo['add_to_cat_link']."'><img src='".$MAIN_ROOT."themes/".$THEME."images/buttons/add.png' class='manageListActionButton'></a>";
				
				echo "
					<tr>
						<td class='main manageList dottedLine' style='width: ".$titleWidth."%'></td>
						<td class='manageList dottedLine' colspan='".count($setupManageListArgs['actions'])."' align='center'>".$dispAddItemToCategory."</td>
					</tr>
				";
				
			}
		}
	
	echo "
		</table>
	
	";
	
	if(count($setupManageListArgs['items']) == 0) {

		if(substr($setupManageListArgs['item_title'],-1) == ":") {
			$noItemName = substr($setupManageListArgs['item_title'], 0, strlen($setupManageListArgs['item_title'])-1);
		}
		elseif($setupManageListArgs['item_title'] == "") {
			$noItemName = "item";	
		}
		
		echo "
			<div class='shadedBox' style='margin-left: auto; margin-right: auto; width: 45%; margin-top: 20px'>
				<p class='main' align='center'>
					<i>No ".strtolower($noItemName)."s added yet!</i>
				</p>
			</div>
		";
		
	}
	
?>