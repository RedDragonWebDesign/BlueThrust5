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

if(!isset($prevFolder)) {
	$prevFolder = "../../../";	
}

include_once($prevFolder."_setup.php");


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Your Squads");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$arrSquadPrivileges = $squadObj->arrSquadPrivileges;


// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();

	if($squadObj->memberHasAccess($memberInfo['member_id'], "acceptapps")) {

		$squadInfo = $squadObj->get_info_filtered();

		$counter = 0;
		$result = $mysqli->query("SELECT * FROM ".$dbprefix."squadapps WHERE squad_id = '".$squadInfo['squad_id']."' AND status = '0' ORDER BY applydate DESC");
		while($row = $result->fetch_assoc()) {
		
			$member->select($row['member_id']);
			$newMemberInfo = $member->get_info_filtered();
		
			$squadObj->select($row['squad_id']);
			$squadInfo = $squadObj->get_info_filtered();
		
			if($newMemberInfo['avatar'] == "") {
				$newMemberInfo['avatar'] = $MAIN_ROOT."themes/".$THEME."/images/defaultavatar.png";
			}
		
			if(trim($row['message']) == "") {
				$row['message'] = "None";
			}

			echo "
			<div class='newsDiv'>
				<img src='".$newMemberInfo['avatar']."' class='avatarImg'>
				<div class='postInfo'>
					From: ".$member->getMemberLink()." - ".getPreciseTime($row['applydate'])."<br>
				</div>
				<br>
				<div class='dottedLine' style='margin-top: 5px'></div>
				<div class='postMessage'>
					<b>Message:</b><br><br>
					<div style='padding-left: 15px'>".nl2br(parseBBCode(filterText($row['message'])))."</div>
				</div>
				<div class='dottedLine' style='margin-top: 5px; margin-bottom: 5px'></div>
				<p style='padding: 0px; margin: 0px' align='right'><b><a href='javascript:void(0)' onclick=\"decisionClicked('".$row['squadapp_id']."', 'accept')\">APPROVE</a> | <a href='javascript:void(0)' onclick=\"decisionClicked('".$row['squadapp_id']."', 'decline')\">DECLINE</a></b></p>
			</div>

			";
		
			$counter++;
		}
		
		
		if($counter == 0) {
		
			echo "
			<div class='shadedBox' style='width: 300px; margin-top: 50px; margin-left: auto; margin-right: auto; font-style: italic'>
				<p class='main' align='center'>
					There are currently no pending squad applications!
				</p>
				</div>
			<br>
			";
		}
		
		
	}
	
	
}

?>