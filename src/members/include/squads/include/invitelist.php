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


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();
	
	$counter = 0;
	$result = $mysqli->query("SELECT * FROM ".$dbprefix."squadinvites WHERE receiver_id = '".$memberInfo['member_id']."' AND status = '0' ORDER BY datesent DESC");
	while($row = $result->fetch_assoc()) {
	
		$member->select($row['sender_id']);
		$squadMemberInfo = $member->get_info_filtered();
		
		$squadObj->select($row['squad_id']);
		$squadInfo = $squadObj->get_info_filtered();
		
		if($squadMemberInfo['avatar'] == "") {
			$squadMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
		}
		else {
			$squadMemberInfo['avatar'] = $MAIN_ROOT.$squadMemberInfo['avatar'];
		}
		
		if(trim($row['message']) == "") {
			$row['message'] = "None";	
		}
		$squadObj->objSquadRank->select($row['startingrank_id']);
		
		echo "
			<div class='newsDiv'>
			
				<div class='postInfo'>
					<div id='newsPostAvatar' style='float: left'><img src='".$squadMemberInfo['avatar']."' class='avatarImg'></div>
					<div id='newsPostInfo' style='float: left; margin-left: 15px'>
						From: ".$member->getMemberLink()." - ".getPreciseTime($row['datesent'])."<br>
						Squad: <b><a href='".$MAIN_ROOT."squads/profile.php?sID=".$row['squad_id']."'>".$squadInfo['name']."</a></b><br>
						Starting Rank: ".$squadObj->objSquadRank->get_info_filtered("name")."
					</div>
					<div style='clear: both'></div>
				</div>
				<br>
				<div class='dottedLine' style='margin-top: 5px'></div>
				<div class='postMessage'>
					<b>Message:</b><br><br>
					<div style='padding-left: 15px'>".nl2br(parseBBCode(filterText($row['message'])))."</div>
				</div>
				<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
				<p style='padding: 0px; margin: 0px' align='right'><b><a href='javascript:void(0)' onclick=\"inviteClicked('".$row['squadinvite_id']."', 'accept')\">ACCEPT</a> | <a href='javascript:void(0)' onclick=\"inviteClicked('".$row['squadinvite_id']."', 'decline')\">DECLINE</a></b></p>
			</div>

		";
	
		$counter++;
	}
	
	
	if($counter == 0) {
		
		echo "
			<div class='shadedBox' style='width: 300px; margin-top: 50px; margin-left: auto; margin-right: auto; font-style: italic'>
				<p class='main' align='center'>
					You currently have no squad invitiations!
				</p>
			</div>
			<br>
		";
	}
	
}


?>