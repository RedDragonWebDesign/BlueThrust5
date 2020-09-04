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
include_once("../../../classes/chatroom.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$objMember = new Member($mysqli);

$eventObj = new Event($mysqli);
$eventChatObj = new ChatRoom($mysqli);


$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventChatObj->select($_POST['ecID'])) {

	$memberInfo = $member->get_info_filtered();
	$eventChatInfo = $eventChatObj->get_info_filtered();
	
	$eventObj->select($eventChatInfo['event_id']);
	
	if(in_array($memberInfo['member_id'], $eventObj->getInvitedMembers(true)) || $memberInfo['member_id'] == $eventInfo['member_id']) {
		
	
	
		$eventInfo = $eventObj->get_info_filtered();
		$eventID = $eventInfo['event_id'];

	
	
	}
	
}

?>