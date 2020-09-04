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
include_once("../../../../classes/rank.php");
include_once("../../../../classes/news.php");
include_once("../../../../classes/shoutbox.php");

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
	include("manageshoutbox_list.php");
	
}

?>
	