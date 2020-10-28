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

$cID = $consoleObj->findConsoleIDByName("Manage Shoutbox Posts");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;
if($member->authorizeLogin($_SESSION['btPassword']) && $member->hasAccess($consoleObj)) {
	
	$memberInfo = $member->get_info_filtered();

	$arrPostIDs = json_decode($_POST['deletePosts'], true);
	
	foreach($arrPostIDs as $postID) {
		if($newsObj->select($postID) && $newsObj->get_info("newstype") == 3) {
			$newsObj->delete();
		}
	}
	
	$countPosts = count($arrPostIDs);
	$addS = ($countPosts > 1) ? "s" : "";
	
	$member->logAction("Deleted ".$countPosts." shoutbox post".$addS.".");
	
	define("SHOW_SHOUTBOXLIST", true);
	require_once("manageshoutbox_list.php");
	
}