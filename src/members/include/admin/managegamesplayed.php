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
	$memberInfo = $member->get_info_filtered();
	$consoleObj->select($_GET['cID']);
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


include_once($prevFolder."classes/btupload.php");
include_once($prevFolder."classes/game.php");
$cID = $_GET['cID'];
$gameObj = new Game($mysqli);
$gameStatsObj = new Basic($mysqli, "gamestats", "gamestats_id");

if($_GET['gID'] == "") {
	
	echo "
		<div id='loadingSpiral' class='loadingSpiral'>
			<p align='center'>
				<img src='".$MAIN_ROOT."themes/".$THEME."/images/loading-spiral.gif'><br>Loading
			</p>
		</div>
		<div id='contentDiv'>
	";
	
	include("gamesplayed/main.php");
	
	echo "
		</div>
		<div id='deleteMessage' style='display: none'></div>
	
		<script type='text/javascript'>
			function moveGame(strDir, intGameID) {
				$(document).ready(function() {
					$('#loadingSpiral').show();
					$('#contentDiv').hide();
					$.post('".$MAIN_ROOT."members/include/admin/gamesplayed/move.php', { gDir: strDir, gID: intGameID }, function(data) {
						$('#contentDiv').html(data);
						$('#loadingSpiral').hide();
						$('#contentDiv').fadeIn(400);
					});
				
				});
			}
			
			
			function deleteGame(intGameID) {
				$(document).ready(function() {				
				
					$.post('".$MAIN_ROOT."members/include/admin/gamesplayed/delete.php', { gID: intGameID }, function(data) {
						$('#deleteMessage').html(data);
						
						$('#deleteMessage').dialog({
					
							title: 'Manage Games Played - Delete',
							width: 400,
							modal: true,
							zIndex: 9999,
							resizable: false,
							show: 'scale',
							buttons: {
								'Yes': function() {
									
									$('#loadingSpiral').show();
									$('#contentDiv').hide();
									$(this).dialog('close');
									$.post('".$MAIN_ROOT."members/include/admin/gamesplayed/delete.php', { gID: intGameID, confirm: 1 }, function(data1) {
										$('#contentDiv').html(data1);
										$('#loadingSpiral').hide();
										$('#contentDiv').fadeIn(400);	
									});
								
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
elseif($_GET['gID'] != "" AND $_GET['action'] == "edit") {
	include("gamesplayed/edit.php");
}




?>