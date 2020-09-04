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

include_once("../classes/forumboard.php");

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
$intAddBoardCID = $consoleObj->findConsoleIDByName("Add Board");
$intEditCatCID = $consoleObj->findConsoleIDByName("Manage Forum Categories");
$intAddCatCID = $consoleObj->findConsoleIDByName("Add Forum Category");

$boardObj = new ForumBoard($mysqli);

$categoryObj = new BasicOrder($mysqli, "forum_category", "forumcategory_id");
$categoryObj->set_assocTableName("forum_board");
$categoryObj->set_assocTableKey("forumboard_id");


if($boardObj->select($_GET['bID']) && $_GET['action'] == "edit") {

	include("include/edit_board.php");

}
else {
	
	echo "
	
		<table class='formTable'>
			<tr>
				<td class='main' colspan='2' align='right'>
					&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddBoardCID."'>Add Board</a> &laquo;&nbsp;&nbsp;&nbsp;&raquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$intAddCatCID."'>Add Category</a> &laquo;<br><br>
				</td>
			</tr>
			<tr>
				<td class='formTitle' style='width: 76%'>Board Name:</td>
				<td class='formTitle' style='width: 24%'>Actions:</td>
			</tr>
		</table>
	
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		
		
		<div id='boardList'>
	
	
	";
	
	include("include/main_manageboards.php");
	
	echo "
		</div>
		<div id='deleteBoardDiv' style='display: none'></div>
		
		<script type='text/javascript'>
		
			function moveBoard(strDir, intBoardID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#boardList').fadeOut(250);
					$.post('".$MAIN_ROOT."members/include/forum/include/move_board.php', { bID: intBoardID, bDir: strDir }, function(data) {
						$('#boardList').html(data);
						$('#loadingSpiral').hide();
						$('#boardList').fadeIn(250);
					});
				});
			}
		
		
			function deleteBoard(intBoardID) {
			
				$(document).ready(function() {
				
					$.post('".$MAIN_ROOT."members/include/forum/include/delete_board.php', { bID: intBoardID }, function(data) {
					
						$('#deleteBoardDiv').html(data);
						$('#deleteBoardDiv').dialog({
						
							title: 'Delete Board',
							width: 400,
							modal: true,
							show: 'scale',
							resizable: false,
							zIndex: 999999,
							buttons: {
						
								'Yes': function() {
								
									$('#loadingSpiral').show();
									$('#boardList').fadeOut(250);
								
									$.post('".$MAIN_ROOT."members/include/forum/include/delete_board.php', { bID: intBoardID, confirm: 1 }, function(data1) {

										$('#boardList').html(data1);
										$('#loadingSpiral').hide();
										$('#boardList').fadeIn(250);
									
									});
									
									$(this).dialog('close');
									
								},
								'Cancel': function() {
									$(this).dialog('close');
								}
							
							}
						
						});

					});
				
				});
			
			}
		</script>
	";
}


?>