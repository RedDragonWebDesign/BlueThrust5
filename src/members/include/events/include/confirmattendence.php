<?php

/*
 * BlueThrust Clan Scripts
 * Copyright 2014
 *
 * Author: Bluethrust Web Development
 * E-mail: support@bluethrust.com
 * Website: http://www.bluethrust.com
 *
 * License: http://www.bluethrust.com/license.php
 *
 */


require_once("../../../../_setup.php");


// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("View Event Invitations");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$eventObj = new Event($mysqli);

// Check Login
if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	$memberInfo = $member->get_info();
} else {
	exit();
}


if ($eventObj->objEventMember->select($_POST['emID']) && $eventObj->objEventMember->get_info("member_id") == $memberInfo['member_id']) {
	$eventMemberInfo = $eventObj->objEventMember->get_info_filtered();
	$eventObj->select($eventMemberInfo['event_id']);
	$eventInfo = $eventObj->get_info_filtered();

	if ($eventInfo['startdate'] <= time() && $eventMemberInfo['status'] == 1) {
		$eventObj->objEventMember->update(["attendconfirm_member"], ["1"]);
	}

	require_once("invitelist.php");
}
