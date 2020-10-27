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


include("../../../_setup.php");
include_once("../../../classes/member.php");
include_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$eventObj = new Event($mysqli);


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventMember->select($_POST['eMemID'])) {

	$eventID = $eventObj->objEventMember->get_info("event_id");
	$memberInfo = $member->get_info_filtered();

	if($eventObj->select($eventID) && $member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "manageinvites") || $memberInfo['rank_id'] == 1)) {

		$eventInfo = $eventObj->get_info_filtered();
		$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
		$objInviteMember = new Member($mysqli);
		$objInviteMember->select($eventMemberInfo['member_id']);
		
		$objInviteMember->postNotification("You were uninvited from the event <b>".$eventInfo['title']."</b>!");
		
		
		$eventObj->objEventMember->delete();
		
		
		$arrInvitedMembers = $eventObj->getInvitedMembers(true);
		
		$sqlInvitedMembers = "('".implode("','", $arrInvitedMembers)."')";
		
		$memberoptions = "<option value=''>Select</option>";
		$result = $mysqli->query("SELECT m.member_id, m.username, r.ordernum, r.name FROM ".$dbprefix."members m, ".$dbprefix."ranks r WHERE m.rank_id = r.rank_id AND m.member_id IN ".$sqlInvitedMembers." AND m.disabled = '0' AND m.rank_id != '1' ORDER BY r.ordernum DESC");
		while($row = $result->fetch_assoc()) {
			$row = filterArray($row);
			$eventMemberID = $eventObj->getEventMemberID($row['member_id']);
			if($eventMemberID !== false) {
		
				$memberoptions .= "<option value='".$eventMemberID."'>".$row['name']." ".$row['username']."</option>";
		
			}
		}
		
		echo $memberoptions;
		
	}
	
	
}


?>