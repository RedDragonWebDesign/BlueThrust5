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
	exit();
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$cID = $_GET['cID'];

include_once($prevFolder."classes/btupload.php");

$menuCatObj = new MenuCategory($mysqli);
$menuItemObj = new MenuItem($mysqli);
$intAddNewMenuItemID = $consoleObj->findConsoleIDByName("Add Menu Item");


if(isset($_GET['menuID']) && $menuItemObj->select($_GET['menuID']) && $_GET['action'] == "edit") {
	$menuItemInfo = $menuItemObj->get_info();
	include("include/admin/managemenu/edit_item.php");
}
else {
	
	echo "
		<table class='formTable'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewMenuItemID."'>Add New Menu Item</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' style='width: 76%'>Menu Item:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>
	
		
		<div id='menuItemDiv'>
	";
	
	include("include/admin/managemenu/include/menuitemlist.php");
	
	echo "
		</div>
		<div id='deleteMessage'></div>
		<script type='text/javascript'>
		
			function moveItem(strDir, intItemID) {
			
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#menuItemDiv').fadeOut(250);
					
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/move_item.php', { itemID: intItemID, iDir: strDir}, function(data) {
					
						$('#menuItemDiv').html(data);
						$('#loadingSpiral').hide();
						$('#menuItemDiv').fadeIn(250);
					
					});
					
				});
			
			}
			
			function deleteItem(intItemID) {
				$(document).ready(function() {
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/delete_item.php', { itemID: intItemID }, function(data) {
					
						$('#deleteMessage').html(data);

					});
					
				});
			}
			
		
		</script>
		
	";
	
	
	
}


?>