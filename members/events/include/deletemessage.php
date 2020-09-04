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

if($_POST['comment'] == 1) {
	$checkMessage = $eventObj->objEventMessageComment->select($_POST['messageID']);
	$objMessage = $eventObj->objEventMessageComment;
	
	$eventMessageID = $objMessage->get_info("eventmessage_id");
	$eventObj->objEventMessage->select($eventMessageID);
	$eventID = $eventObj->objEventMessage->get_info("event_id");
	
	
}
else {
	$checkMessage = $eventObj->objEventMessage->select($_POST['messageID']);
	$objMessage = $eventObj->objEventMessage;
	
	
	$eventID = $objMessage->get_info("event_id");
	
}


if($member->authorizeLogin($_SESSION['btPassword']) && $checkMessage) {
	
	
	$eventObj->select($eventID);
	
	$eventInfo = $eventObj->get_info_filtered();
	
	$memberInfo = $member->get_info_filtered();
	
	
	
	if(($member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "managemessages")) || $memberInfo['rank_id'] == 1)) {
		
		$objMessage->delete();
		
	}
	
	
	include("eventmessages.php");

	
}



?>