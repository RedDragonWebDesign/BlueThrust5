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


$createPollCID = $consoleObj->findConsoleIDByName("Create a Poll");
$consoleObj->select($createPollCID);

$blnConsoleCheck1 = $member->hasAccess($consoleObj);


$managePollsCID = $consoleObj->findConsoleIDByName("Manage Polls");
$consoleObj->select($managePollsCID);

$blnConsoleCheck2 = $member->hasAccess($consoleObj);

$blnConsoleCheck = $blnConsoleCheck1 || $blnConsoleCheck2;


$pollObj = new Poll($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $blnConsoleCheck) {

	$pollObj->moveCache($_POST['direction'], $_POST['optionOrder']);
	
}



?>