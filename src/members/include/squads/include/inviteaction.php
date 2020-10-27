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
include_once("../../../../classes/squad.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Squad Invitations");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$squadObj = new Squad($mysqli);
$squadInviteObj = new Basic($mysqli, "squadinvites", "squadinvite_id");


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $squadInviteObj->select($_POST['siID'])) {
	
	$memberInfo = $member->get_info();
	$memberLink = $member->getMemberLink();
	$squadInviteInfo = $squadInviteObj->get_info();
	$squadObj->select($squadInviteInfo['squad_id']);
	$squadInfo = $squadObj->get_info_filtered();
	$squadMemberList = $squadObj->getMemberList();
	
	if($squadInviteInfo['receiver_id'] == $memberInfo['member_id'] && $squadInviteInfo['status'] == 0 && !in_array($memberInfo['member_id'], $squadMemberList)) {
		
		if($_POST['action'] == "accept") {
			
			$arrRankList = $squadObj->getRankList();
			
			if(!$squadObj->objSquadRank->select($squadInviteInfo['startingrank_id']) && count($arrRankList) > 1) {

				$rankKey = count($arrRankList)-1;
				$squadInviteInfo['startingrank_id'] = $arrRankList[$rankKey];
			
			}
			elseif(!$squadObj->objSquadRank->select($squadInviteInfo['startingrank_id']) && count($arrRankList) <= 1) {
				
				$member->select($squadInfo['member_id']);
				$member->postNotification("There are currently members in your squad, <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b> without ranks!");
				$member->select($memberInfo['member_id']);
				
			}
			
			
			$squadInviteObj->update(array("dateaction", "status"), array(time(), "1"));
			
			$arrColumns = array("squad_id", "member_id", "squadrank_id", "datejoined");
			$arrValues = array($squadInviteInfo['squad_id'], $memberInfo['member_id'], $squadInviteInfo['startingrank_id'], time());
			
			$squadObj->objSquadMember->addNew($arrColumns, $arrValues);
			$intViewSquadsCID = $consoleObj->findConsoleIDByName("View Your Squads");
			$member->postNotification("Congratulations!  You just joined the squad <b>".$squadInfo['name']."</b>.  View the Squads section of <a href='".$MAIN_ROOT."members'>My Account</a> to <a href='".$MAIN_ROOT."members/console.php?cID=".$intViewSquadsCID."'>View Your Squads</a>.");
			
			$member->select($squadInviteInfo['sender_id']);
			$member->postNotification("<b>".$memberLink."</b> has accepted the invitation to join <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>");
			
			
			$mysqli->query("DELETE FROM ".$dbprefix."squadapps WHERE member_id = '".$memberInfo['member_id']."'");
			
			echo "
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#actionMessage').html(\"<p class='main' align='center' style='font-weight: bold'><span class='successFont'>Squad Invitation Accepted!</span></p>\");
					});
				</script>
			";
			
			
		}
		else {
			
			$squadInviteObj->update(array("dateaction", "status"), array(time(), "2"));
			
			$member->select($squadInviteInfo['sender_id']);
			$member->postNotification("<b>".$memberLink."</b> has declined the invitation to join <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$squadInfo['squad_id']."'>".$squadInfo['name']."</a></b>");
			
			echo "
				
				<script type='text/javascript'>
					$(document).ready(function() {
						$('#actionMessage').html(\"<p class='main' align='center' style='font-weight: bold'><span class='failedFont'>Squad Invitation Declined!</span></p>\");
					});
				</script>
			
			";
			
		}

	}
	elseif(in_array($memberInfo['member_id'], $squadMemberList)) {
		$squadInviteObj->delete($_POST['siID']);	
	}
	
	include("invitelist.php");
	
}


?>