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

include_once("../../../../_setup.php");
include_once("../../../../classes/member.php");
include_once("../../../../classes/rank.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);
$gameStatsObj = new Basic($mysqli, "gamestats", "gamestats_id");


$consoleObj = new ConsoleOption($mysqli);
$manageCID = $consoleObj->findConsoleIDByName("Manage Games Played");
$consoleObj->select($manageCID);

$checkAccess1 = $member->hasAccess($consoleObj);

$addCID = $consoleObj->findConsoleIDByName("Add Games Played");
$consoleObj->select($addCID);

$checkAccess2 = $member->hasAccess($consoleObj);

$checkAccess = $checkAccess1 || $checkAccess2;


if($member->authorizeLogin($_SESSION['btPassword'])) {


	$memberInfo = $member->get_info_filtered();

	if($checkAccess) {
		
		
		if(isset($_SESSION['btStatCache'][$_POST['sID']])) {
		
			
			$countErrors = 0;
			if($_SESSION['btStatCache'][$_POST['sID']]['statType'] == "input") {
				
				foreach($_SESSION['btStatCache'] as $statInfo) {
					if($statInfo['statType'] == "calculate" AND ($statInfo['firstStat'] == $_POST['sID'] OR $statInfo['secondStat'] == $_POST['sID'])) {
						$countErrors++;
					}
				}
				
			}
			
			
			if($countErrors == 0) {
				
				if($gameStatsObj->select($_SESSION['btStatCache'][$_POST['sID']]['gamestatsID'])) {
					$gameStatsObj->delete();
				}
				
				
				unset($_SESSION['btStatCache'][$_POST['sID']]);
				
				$x = 0;
				$tempArray = array();
				foreach($_SESSION['btStatCache'] as $statInfo) {
					$tempArray[$x] = $statInfo;
					$x++;
				}
				
				$_SESSION['btStatCache'] = $tempArray;
				
			}
			else {
				echo "
					<div id='errorPopup' style='display: none'><p align='center'>There is currently an auto-calculated stat using <b>".filterText($_SESSION['btStatCache'][$_POST['sID']]['statName'])."</b>.  Please delete all auto-calculated stats that are using <b>".filterText($_SESSION['btStatCache'][$_POST['sID']]['statName'])."</b> to continue.</p></div>
				
					<script type='text/javascript'>
						$(document).ready(function() {
							
							$('#errorPopup').dialog({
								title: 'Add Game Statistics - Error',
								modal: true,
								width: 425,
								show: 'scale',
								resizable: false,
								zIndex: 99999,
								buttons: {
									'OK': function() {
										$(this).dialog('close');
									}
								
								}
							
							
							});
							$('.ui-dialog :button').blur();
						});
					
						
					</script>
				
				";
				
			}
			
			echo "
			<script type='text/javascript'>
				$(document).ready(function() {
				
					$('#loadingSpiral').show();
					$('#statList').hide();
					$.post('".$MAIN_ROOT."members/include/admin/statcache/view.php', { }, function(data) {
						$('#statList').html(data);
						$('#statList').fadeOut(400);
						$('#loadingSpiral').hide();
						$('#statList').fadeIn(400);
					});
						
				
				});
			</script>
			";
			
			
		}
	}
	
}



?>