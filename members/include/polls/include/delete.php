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
include_once("../../../../classes/poll.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$managePollsCID = $consoleObj->findConsoleIDByName("Manage Polls");
$consoleObj->select($managePollsCID);


$pollObj = new Poll($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $pollObj->select($_POST['pollID']) && $member->hasAccess($consoleObj)) {
	
	$pollInfo = $pollObj->get_info_filtered();
	
	$pollObj->delete();
	
	
	define("SHOW_POLLLIST", true);
	include("polllist.php");
}

?>