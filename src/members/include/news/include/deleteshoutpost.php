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
require_once("../../../../classes/rank.php");
require_once("../../../../classes/news.php");
require_once("../../../../classes/shoutbox.php");

// Start Page

$consoleObj = new ConsoleOption($mysqli);

$cID = $consoleObj->findConsoleIDByName("Manage News");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if ($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj) && $newsObj->select($_POST['postID'])) {

	$newsObj->delete();

	$shoutboxObj = new Shoutbox($mysqli, "news", "news_id");

	$shoutboxObj->strDivID = filterText($_POST['updateDiv']);
	$shoutboxObj->intDispWidth = 140;
	$shoutboxObj->intDispHeight = 300;
	$shoutboxObj->blnUpdateShoutbox = true;
	$shoutboxObj->blnMainShoutbox = true;


	$shoutboxObj->strEditLink = $MAIN_ROOT."members/console.php?cID=".$cID."&newsID=";
	$shoutboxObj->strDeleteLink = $MAIN_ROOT."members/include/news/include/deleteshoutpost.php";


	echo $shoutboxObj->dispShoutbox();

}