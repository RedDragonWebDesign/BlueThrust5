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


require_once("../../_setup.php");
require_once("../../classes/member.php");
require_once("../../classes/rank.php");
require_once("../../classes/event.php");


$ipbanObj = new Basic($mysqli, "ipban", "ipaddress");

if($ipbanObj->select($IP_ADDRESS, false)) {
	$ipbanInfo = $ipbanObj->get_info();

	if(time() < $ipbanInfo['exptime'] OR $ipbanInfo['exptime'] == 0) {
		die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."banned.php';</script>");
	}
	else {
		$ipbanObj->delete();
	}

}


// Start Page
$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage My Events");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();
$consoleTitle = $consoleInfo['pagetitle'];


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$pID = strtolower($_GET['pID']);


$eID = $_GET['eID'];


// Check Login
$LOGIN_FAIL = true;
$blnShowPage = false;
if($member->authorizeLogin($_SESSION['btPassword'])) {
	$LOGIN_FAIL = false;
	$memberInfo = $member->get_info_filtered();

	$eventObj = new Event($mysqli);

	if($eventObj->select($eID)) {

		$arrMembers = $eventObj->getInvitedMembers(true);
		if(in_array($memberInfo['member_id'], $arrMembers) || $eventObj->get_info("member_id") == $memberInfo['member_id'] || $memberInfo['rank_id'] == 1) {
			$blnShowPage = true;
			$eventInfo = $eventObj->get_info_filtered();
		}
	}

}



if($LOGIN_FAIL) {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."login.php';</script>");
}

$_SESSION['btEventID'] = "";
$prevFolder = "../../";
$PAGE_NAME = $consoleTitle." - ";

$EXTERNAL_JAVASCRIPT .= "
<script type='text/javascript' src='".$MAIN_ROOT."members/js/console.js'></script>
<script type='text/javascript' src='".$MAIN_ROOT."members/js/main.js'></script>
";

require_once("../../themes/".$THEME."/_header.php");

$breadcrumbObj->setTitle($consoleTitle);
$breadcrumbObj->addCrumb("Home", MAIN_ROOT);
$breadcrumbObj->addCrumb("My Account", MAIN_ROOT."members");
$breadcrumbObj->addCrumb($consoleTitle);
require_once($prevFolder."include/breadcrumb.php");


if($blnShowPage) {

	if($_GET['posID'] != "") {
		echo "
		<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$_GET['eID']."&pID=ManagePositions'>Go Back</a></p>
		";
	}
	else {
		echo "
			<p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eID."'>Go Back</a></p>
		";
	}

	switch($pID) {
		case "addposition":
			require_once("addposition.php");
			break;
		case "managepositions":
			require_once("managepositions.php");
			break;
		case "invitemembers":
			require_once("invitemembers.php");
			break;
		case "manageinvites":
			require_once("manageinvites.php");
			break;
		case "editinfo":
			require_once("editinfo.php");
			break;
		case "setattendance":
			require_once("setattendance.php");
			break;
		case "chat":
			require_once("chat.php");
			break;
	}


	if($_GET['posID'] != "") {
		echo "
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/events/manage.php?eID=".$_GET['eID']."&pID=ManagePositions'>Go Back</a></p></div>
		";
	}
	else {
		echo "
			<div style='clear: both'><p align='right' style='margin-bottom: 20px; margin-right: 20px;'>&laquo; <a href='".$MAIN_ROOT."members/console.php?cID=".$cID."&select=".$eID."'>Go Back</a></p></div>
		";
	}

}
else {
	die("<script type='text/javascript'>window.location = '".$MAIN_ROOT."members';</script>");
}


require_once("../../themes/".$THEME."/_footer.php");