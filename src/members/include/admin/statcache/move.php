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
		
		if(isset($_SESSION['btStatCache'][$_POST['sID']]) AND is_numeric($_POST['sID'])) {
			
			$moveUp = $_POST['sID']-1;
			$moveDown = $_POST['sID']+1;
			
			$newSpot = "none";
			if($_POST['statDir'] == "up" AND isset($_SESSION['btStatCache'][$moveUp])) {
				

				$newSpot = $moveUp;
				
				
			}
			elseif($_POST['statDir'] == "down" AND isset($_SESSION['btStatCache'][$moveDown])) {
				
				$newSpot = $moveDown;
				
			}

			
			if(is_numeric($newSpot)) {
				
				$temp = $_SESSION['btStatCache'][$newSpot];
				$temp2 = $_SESSION['btStatCache'][$_POST['sID']];
				
				$_SESSION['btStatCache'][$_POST['sID']] = $temp;
				$_SESSION['btStatCache'][$newSpot] = $temp2;
				
				
				foreach($_SESSION['btStatCache'] as $key => $statInfo) {
					
					if($statInfo['firstStat'] == $newSpot) {
						$_SESSION['btStatCache'][$key]['firstStat'] = $_POST['sID'];
					}
					elseif($statInfo['firstStat'] == $_POST['sID']) {
						$_SESSION['btStatCache'][$key]['firstStat'] = $newSpot;
					}
					
					if($statInfo['secondStat'] == $newSpot) {
						$_SESSION['btStatCache'][$key]['secondStat'] = $_POST['sID'];
					}
					elseif($statInfo['secondStat'] == $_POST['sID']) {
						$_SESSION['btStatCache'][$key]['secondStat'] = $newSpot;
					}
					
					
				}
				
				
			}
			
			
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


?>