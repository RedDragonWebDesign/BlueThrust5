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

$checkTerm = filterText($_GET['term']);

$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->select($_SESSION['btEventID'])) {

	$eventID = $eventObj->get_info("event_id");
	$eventMID = $eventObj->get_info("member_id");
	
	$memberInfo = $member->get_info_filtered();

	if($member->hasAccess($consoleObj) && ($eventObj->memberHasAccess($memberInfo['member_id'], "invitemembers") || $memberInfo['rank_id'] == 1)) {

		$arrInvitedMembers = $eventObj->getInvitedMembers(true);
		$arrInvitedMembers = array_merge($arrInvitedMembers, $_SESSION['btInviteList']);

		$arrACMemberList = array();
		
		$sqlInvitedMembers = "('".implode("','", $arrInvitedMembers)."')";
		$memberoptions = "<option value=''>Select</option>";
		$result = $mysqli->query("SELECT m.username,m.member_id,r.ordernum,r.name FROM ".$dbprefix."members m, ".$dbprefix."ranks r WHERE m.rank_id = r.rank_id AND m.member_id NOT IN ".$sqlInvitedMembers." AND m.disabled = '0' AND m.rank_id != '1' AND m.member_id != '".$eventMID."' AND m.username LIKE '".$checkTerm."%' ORDER BY r.ordernum DESC");
		while($row = $result->fetch_assoc()) {
			$arrACMemberList[] = array("id" => $row['member_id'], "value" => filterText($row['username']));
		}

		echo json_encode($arrACMemberList);
		
	}


}



?>