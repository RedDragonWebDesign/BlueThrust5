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
$intAddNewMenuCatID = $consoleObj->findConsoleIDByName("Add Menu Category");

if(isset($_GET['action']) && $_GET['action'] == "edit" && $menuCatObj->select($_GET['mcID'])) {	
	include("managemenu/edit_category.php");	
}
elseif(!isset($_GET['action']) || (isset($_GET['action']) && !$menuCatObj->select($_GET['mcID']))) {
	
	echo "
		<table class='formTable'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddNewMenuCatID."'>Add New Menu Category</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' style='width: 76%'>Menu Category:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>

		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		<div id='menuCategoryDiv'>
		";
	
	$_POST['manage'] = 1;
	include("include/admin/managemenu/include/menucategorylist.php");
	echo "
		</div>
		
		<div id='deleteMessage' style='display: none'></div>
		
		
		<script type='text/javascript'>
		
			function refreshCategoryList() {
			
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#menuCategoryDiv').fadeOut(250);
					
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/include/menucategorylist.php', { manage: 1 }, function(data) {
					
						$('#menuCategoryDiv').html(data);
						$('#loadingSpiral').hide();
						$('#menuCategoryDiv').fadeIn(250);
					
					});
					
				});
			}
			
			function moveCat(strDir, intCatID) {
			
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#menuCategoryDiv').fadeOut(250);
					
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/move_category.php', { manage: 1, cDir: strDir, mcID: intCatID }, function(data) {
					
						$('#menuCategoryDiv').html(data);
						$('#loadingSpiral').hide();
						$('#menuCategoryDiv').fadeIn(250);
					
					});
					
				});
			
			}
			
			function deleteCat(intCatID) {
				$(document).ready(function() {
					$.post('".$MAIN_ROOT."members/include/admin/managemenu/delete_category.php', { manage: 1, mcID: intCatID }, function(data) {
					
						$('#deleteMessage').html(data);

					});
					
				});
			}
			
			
			
		</script>
	";
	
	
}

?>