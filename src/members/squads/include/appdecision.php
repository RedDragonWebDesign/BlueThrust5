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

include_once("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/rank.php");
include_once("../../../classes/squad.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$arrSquadPrivileges = $squadObj->arrSquadPrivileges;

$squadObj = new Squad($mysqli);
$squadAppObj = new Basic($mysqli, "squadapps", "squadapp_id");


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	
	if($squadObj->select($_POST['sID']) && $squadObj->memberHasAccess($memberInfo['member_id'], "acceptapps") && $squadAppObj->select($_POST['saID'])) {

		$squadInfo = $squadObj->get_info_filtered();
		$squadAppInfo = $squadAppObj->get_info();
		$squadRankList = $squadObj->getRankList();
		
		
		if($squadAppInfo['squad_id'] == $_POST['sID'] && $squadAppInfo['status'] == 0 && count($squadRankList) > 1) {
		
			
			
			if($_POST['action'] == "accept") {
			
				$squadRankKey = count($squadRankList)-1;
				$newMemberSquadRank = $squadRankList[$squadRankKey];
				$squadAppObj->update(array("dateaction", "status", "squadmember_id"), array(time(), "1", $memberInfo['member_id']));
			
				$arrColumns = array("squad_id", "member_id", "squadrank_id", "datejoined");
				$arrValues = array($squadAppInfo['squad_id'], $squadAppInfo['member_id'], $newMemberSquadRank, time());
			
				$squadObj->objSquadMember->addNew($arrColumns, $arrValues);
				$intViewSquadsCID = $consoleObj->findConsoleIDByName("View Your Squads");
				$member->select($squadAppInfo['member_id']);
				$member->postNotification("Congratulations!  Your application for the squad <b>".$squadInfo['name']."</b> has been approved.  View the Squads section of <a href='".$MAIN_ROOT."members'>My Account</a> to <a href='".$MAIN_ROOT."members/console.php?cID=".$intViewSquadsCID."'>View Your Squads</a>.");
			
				$mysqli->query("DELETE FROM ".$dbprefix."squadinvites WHERE receiver_id = '".$squadAppInfo['member_id']."'");
				
				echo "
					<script type='text/javascript'>
						$(document).ready(function() {
							$('#actionMessage').html(\"<p class='main' align='center' style='font-weight: bold'><span class='successFont'>Squad Application Approved!</span></p>\");
						});
					</script>
				";
			
			
			}
			else {
			
				$squadAppObj->update(array("dateaction", "status", "squadmember_id"), array(time(), "2", $memberInfo['member_id']));
			
				$member->select($squadAppInfo['member_id']);
				$member->postNotification("Your application to join <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b> has been declined.  You may now re-apply if you want to.");
			
				echo "
			
					<script type='text/javascript'>
						$(document).ready(function() {
							$('#actionMessage').html(\"<p class='main' align='center' style='font-weight: bold'><span class='failedFont'>Squad Applicaion Declined!</span></p>\");
						});
					</script>
			
				";
			
			}
			
			
		
		}
		elseif(count($squadRankList) <= 1 && $_POST['action'] == "accept") {
			echo "
				<div style='display: none' id='errorMessage'>
					<p align='center' class='main'>You must have at least one rank besides the founder's rank to add a new member!</p>
				</div>
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#errorMessage').dialog({
						
							title: 'Manage Squads - Accept Application',
							width: 400,
							modal: true,
							zIndex: 9999,
							resizable: false,
							show: 'scale',
							buttons: {
							
								'Ok': function() {
									$(this).dialog('close');
								}
							
							}
						
						});
					});
				</script>
			";
		}
		
		
		
		include("applist.php");
	}
	else {
		echo "
			<script type='text/javascript'>
				window.location = '".$MAIN_ROOT."members/console.php?cID=".$cID."'
			</script>
		";
	}
	
	
	
}

?>