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


$eventObj = new Event($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);

if($member->authorizeLogin($_SESSION['btPassword']) && $eventObj->objEventPosition->select($_POST['posID'])) {

	$eventID = $eventObj->objEventPosition->get_info("event_id");

	$memberInfo = $member->get_info_filtered();

	if(($memberInfo['rank_id'] == 1 || ($member->hasAccess($consoleObj)) && $eventObj->select($eventID) && ($eventObj->memberHasAccess($memberInfo['member_id'], "eventpositions") || $memberInfo['rank_id'] == 1))) {


		$eventObj->objEventPosition->move($_POST['pDir']);
		
		$_GET['eID'] = $eventID;

		include("manageposition_main.php");

	}



}


?>