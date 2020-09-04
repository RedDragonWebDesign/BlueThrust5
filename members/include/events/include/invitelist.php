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


if(!isset($member) || substr($_SERVER['PHP_SELF'], -11) != "console.php" || !isset($_GET['cID'])) {
	
	include_once("../../../../_setup.php");
	include_once("../../../../classes/member.php");
	include_once("../../../../classes/event.php");
	
	// Start Page
	
	$consoleObj = new ConsoleOption($mysqli);
	
	$cID = $consoleObj->findConsoleIDByName("View Event Invitations");
	$consoleObj->select($cID);
	$consoleInfo = $consoleObj->get_info_filtered();
	
	$member = new Member($mysqli);
	$member->select($_SESSION['btUsername']);
	
	$eventObj = new Event($mysqli);
	
	// Check Login
	if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
		$memberInfo = $member->get_info();
	}
	else {
		exit();	
	}
	
}
else {
	$memberInfo = $member->get_info();
	$consoleObj->select($consoleObj->findConsoleIDByName("View Event Invitations"));
	if(!$member->hasAccess($consoleObj)) {
		exit();
	}
}


$result = $mysqli->query("SELECT em.* FROM ".$dbprefix."events_members em, ".$dbprefix."events e WHERE em.event_id = e.event_id AND em.hide = '0' AND em.member_id = '".$memberInfo['member_id']."'");


if($result->num_rows > 0) {
	
	echo "<table class='formTable' style='margin-top: 0px; border-spacing: 0px'>";
	$counter = 0;
	while($row = $result->fetch_assoc()) {
		$row = filterArray($row);
		$eventObj->select($row['event_id']);
		$eventInfo = $eventObj->get_info_filtered();
		
		if($member->select($row['invitedbymember_id'])) {
			$dispInvitedByLink = $member->getMemberLink();
		}
		elseif($member->select($eventInfo['member_id'])) {
			$dispInvitedByLink = $member->getMemberLink();
		}
		else {
			$dispInvitedByLink = "<i>Unknown</i>";	
		}
		
		if($counter == 1) {
			$addCSS = " alternateBGColor";
			$counter = 0;
		}
		else {
			$addCSS = "";
			$counter = 1;
		}
		
		
		if($row['status'] == 0 && time() < $eventInfo['startdate']) {
			$dispActions = "<a href='javascript:void(0)' onclick=\"rsvpEvent('".$row['eventmember_id']."', '1')\">Accept</a> - <a href='javascript:void(0)' onclick=\"rsvpEvent('".$row['eventmember_id']."', '2')\">Decline</a>";
		}
		elseif($row['status'] == 1 && time() < $eventInfo['startdate']) {
			$dispActions = "You are going - <a href='javascript:void(0)' onclick=\"rsvpEvent('".$row['eventmember_id']."', '2')\" title='Decline Invitation'>Change your mind?</a>";
		}
		elseif($row['status'] == 1 && time() >= $eventInfo['startdate'] && ($row['attendconfirm_member'] == 0 && $row['attendconfirm_admin'] == 0)) {
			$dispActions = "Attended - <a href='javascript:void(0)' onclick=\"confirmAttendence('".$row['eventmember_id']."')\">Confirm Attendence</a> - <a href='javascript:void(0)' onclick=\"hideEvent('".$row['eventmember_id']."')\" onmouseover=\"showToolTip('Hide this event if you didn\'t attend.')\" onmouseout='hideToolTip()'>Hide</a>";	
		}
		elseif($row['status'] == 1 && time() >= $eventInfo['startdate'] && $row['attendconfirm_member'] == 1) {
			$dispActions = "Attendence Confirmed - <a href='javascript:void(0)' onclick=\"hideEvent('".$row['eventmember_id']."')\">Hide</a>";
		}
		elseif($row['status'] == 2 && time() < $eventInfo['startdate']) {
			$dispActions = "You are not going - <a href='javascript:void(0)' onclick=\"rsvpEvent('".$row['eventmember_id']."', '1')\" title='Accept Invitation'>Change your mind?</a>";
		}
		elseif(($row['status'] == 2 || $row['status'] == 0) && time() >= $eventInfo['startdate']) {
			$dispActions = "Did not attend - <a href='javascript:void(0)' onclick=\"hideEvent('".$row['eventmember_id']."')\">Hide</a>";
		}
		
		
		echo "
			<tr>
				<td class='main dottedLine".$addCSS."' style='height: 32px; width: 40%'><a href='".$MAIN_ROOT."events/info.php?eID=".$eventInfo['event_id']."'>".$eventInfo['title']."</a></td>
				<td class='main dottedLine".$addCSS."' style='height: 32px; width: 20%' align='center'>".$dispInvitedByLink."</td>
				<td class='main dottedLine".$addCSS."' style='height: 32px; width: 20%' align='center'>".date("D M j, Y g:i a", $eventInfo['startdate'])."</td>
				<td class='main dottedLine".$addCSS."' style='height: 32px; width: 20%' align='center'>".$dispActions."</td>
			</tr>
		";
	}
	
	echo "</table>";
	$member->select($memberInfo['member_id']);
}
else {
	echo "
		
		<div class='shadedBox' style='width: 30%; margin-left: auto; margin-right: auto; margin-top: 25px'>
			<p class='main' align='center'>
				<i>You haven't been invited to any events!</i>
			</p>
		</div>
	
	";
}



?>