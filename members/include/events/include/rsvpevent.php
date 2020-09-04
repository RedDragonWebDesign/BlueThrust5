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


if($eventObj->objEventMember->select($_POST['emID']) && $eventObj->objEventMember->get_info("member_id") == $memberInfo['member_id']) {
	
	$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
	$eventObj->select($eventMemberInfo['event_id']);
	$eventInfo = $eventObj->get_info_filtered();
	
	$arrColumns = array();
	$dispAction = "";
	
	if($_SESSION['btCountMindChanges'][$_POST['emID']] == "") {
		$_SESSION['btCountMindChanges'][$_POST['emID']] == 1;
	}
	else {
		$_SESSION['btCountMindChanges'][$_POST['emID']]++;
	}
	
	
	if($_POST['rsvpNum'] == 1 && time() < $eventInfo['startdate']) {
		$arrColumns = array("status");
		$arrValues = array("1");
		$dispAction = "going";
	}
	elseif($_POST['rsvpNum'] == 2 && time() < $eventInfo['startdate']) {
		$arrColumns = array("status");
		$arrValues = array("2");
		$dispAction = "not going";
	}
	
	
	if(count($arrColumns) > 0) {
		if($eventObj->objEventMember->update($arrColumns, $arrValues)) {
			$dispEventMemberLink = $member->getMemberLink();
			
			if($_SESSION['btCountMindChanges'][$_POST['emID']] < 5) {
				if($member->select($eventMemberInfo['invitedbymember_id'])) {
					$member->postNotification($dispEventMemberLink." is ".$dispAction." to your <a href='".$MAIN_ROOT."events/info.php?eID=".$eventInfo['event_id']."'>event</a>.");
				}
				
				if($eventInfo['member_id'] != $eventMemberInfo['invitedbymember_id'] && $member->select($eventInfo['member_id'])) {
					$member->postNotification($dispEventMemberLink." is ".$dispAction." to your <a href='".$MAIN_ROOT."events/info.php?eID=".$eventInfo['event_id']."'>event</a>.");
				}
			}
			
			$member->select($eventMemberID['member_id']);
		}
	}
	
	include("invitelist.php");
	
}


?>