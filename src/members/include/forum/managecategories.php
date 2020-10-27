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
$intAddCategoryCID = $consoleObj->findConsoleIDByName("Add Forum Category");

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");

if($categoryObj->select($_GET['catID']) && $_GET['action'] == "edit") {

	include("include/edit_category.php");

}
else {
	
	
	
	echo "
	
		<table class='formTable'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCategoryCID."'>Add Forum Category</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' style='width: 76%'>Category Name:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		
		<div id='categoryList'>
	";
	
	include("include/main_managecategory.php");
	
	echo "
		</div>
		<div id='deleteCatDiv' style='display: none'></div>
		<script type='text/javascript'>
		
			function moveCat(strDir, intCatID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#categoryList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/forum/include/move_category.php', { catID: intCatID, cDir: strDir }, function(data) {
						$('#categoryList').html(data);
						$('#loadingSpiral').hide();
						$('#categoryList').fadeIn(250);
					});
				});
			}
		
		
			function deleteCat(intCatID) {
			
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."members/include/forum/include/delete_category.php', { catID: intCatID }, function(data) {
					
						$('#deleteCatDiv').html(data);
					
					});
				
				});
			
			}
		</script>
		
	";
	
}



?>