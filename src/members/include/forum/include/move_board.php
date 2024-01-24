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
require_once("../../../../classes/member.php");
require_once("../../../../classes/forumboard.php");


$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);

$boardObj = new ForumBoard($mysqli);

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage Boards");
$consoleObj->select($cID);

if ($member->authorizeLogin($_SESSION['btPassword'])) {
	$memberInfo = $member->get_info_filtered();

	if (($memberInfo['rank_id'] == 1 || $member->hasAccess($consoleObj)) && $boardObj->select($_POST['bID'])) {
		define('MEMBERRANK_ID', $memberInfo['rank_id']);

		$boardObj->move($_POST['bDir']);

		require_once("main_manageboards.php");
	}
}
