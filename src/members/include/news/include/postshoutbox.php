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

$cID = $consoleObj->findConsoleIDByName("Post in Shoutbox");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$newsObj->addNew(array("member_id", "newstype", "newspost", "dateposted", "postsubject"), array($member->get_info("member_id"), 3, $_POST['message'], time(), "Shoutbox Post"));
	
	$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");
	
	$consoleObj->select($manageNewsCID);
	
	$shoutboxObj = new Shoutbox($mysqli, "news", "news_id");
	
	$shoutboxObj->strDivID = filterText($_POST['updateDiv']);
	$shoutboxObj->intDispWidth = 140;
	$shoutboxObj->intDispHeight = 300;
	$shoutboxObj->blnUpdateShoutbox = true;
	
	if($member->hasAccess($consoleObj)) {
		$shoutboxObj->strEditLink = $MAIN_ROOT."members/console.php?cID=".$manageNewsCID."&newsID=";
		$shoutboxObj->strDeleteLink = $MAIN_ROOT."members/include/news/include/deleteshoutpost.php";
		
	}
	
	echo $shoutboxObj->dispShoutbox();
	
}