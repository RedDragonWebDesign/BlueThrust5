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
include_once("../../../classes/rank.php");
include_once("../../../classes/consoleoption.php");
include_once("../../../classes/event.php");

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$objMember = new Member($mysqli);

$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->select($_POST['eID'])) {
	
	
	$eventInfo = $eventObj->get_info_filtered();
	$eventID = $eventInfo['event_id'];
	$memberInfo = $member->get_info_filtered();
	
	if(trim($_POST['postMessage']) != "" && $member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "postmessages") || $memberInfo['rank_id'] == 1)) {
	
		$eventObj->objEventMessage->addNew(array("event_id", "member_id", "dateposted", "message"), array($eventID, $memberInfo['member_id'], time(), $_POST['postMessage']));
	
	}
	
	if(in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true)) || $memberInfo['member_id'] == $eventInfo['member_id'] || $memberInfo['rank_id'] == 1) {
		
		include("eventmessages.php");
		
	}
	
}




?>