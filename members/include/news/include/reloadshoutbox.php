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

$cID = $consoleObj->findConsoleIDByName("Post in Shoutbox");
$consoleObj->select($cID);
$consoleInfo = $consoleObj->get_info_filtered();



$member = new Member($mysqli);
$member->select($_SESSION['btUsername']);


$newsObj = new News($mysqli);

// Check Login
$LOGIN_FAIL = true;

$shoutboxObj = new Shoutbox($mysqli, "news", "news_id");
$shoutboxObj->strDivID = $_POST['divID'];
$shoutboxObj->intDispWidth = 140;
$shoutboxObj->intDispHeight = 300;
$shoutboxObj->blnUpdateShoutbox = true;

if($member->authorizeLogin($_SESSION['btPassword'])) {

	$manageNewsCID = $consoleObj->findConsoleIDByName("Manage News");

	$consoleObj->select($manageNewsCID);

	if($member->hasAccess($consoleObj)) {
		$shoutboxObj->strEditLink = $MAIN_ROOT."members/console.php?cID=".$manageNewsCID."&newsID=";
		$shoutboxObj->strDeleteLink = $MAIN_ROOT."members/include/news/include/deleteshoutpost.php";

	}

	

}

echo $shoutboxObj->dispShoutbox();

$checkNewsUpdates = $dbprefix."news_update";
$result = $mysqli->query("SELECT updatetime FROM ".$dbprefix."tableupdates WHERE tablename = '".$dbprefix."news'");
if($result->num_rows > 0) {
	$row = $result->fetch_assoc();
	if(!isset($_SESSION[$checkNewsUpdates]) || (isset($_SESSION[$checkNewsUpdates]) && $_SESSION[$checkNewsUpdates] != $row['updatetime'])) {
		echo "
			<script type='text/javascript'>
				$(document).ready(function() {
					$('#".filterText($_POST['divID'])."').animate({
						scrollTop:$('#".filterText($_POST['divID'])."')[0].scrollHeight
					}, 1000);
				});
			</script>
		";
		$_SESSION[$checkNewsUpdates] = $row['updatetime'];
	}
}

?>